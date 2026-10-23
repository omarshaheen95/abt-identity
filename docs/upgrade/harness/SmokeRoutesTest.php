<?php

namespace Tests\Feature;

use App\Models\Inspection;
use App\Models\Manager;
use App\Models\School;
use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Smoke test for the Laravel 13 upgrade.
 *
 * Opens every parameterless GET page of each guard against the local database
 * and compares the status with tests/smoke-baseline.json, which was recorded
 * on Laravel 7 before the upgrade. Pages that change data or build large files
 * on GET are skipped. Everything runs inside a transaction.
 *
 * Record a new baseline:  SMOKE_RECORD=1 php vendor/bin/phpunit --filter SmokeRoutesTest
 */
class SmokeRoutesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    /**
     * GET routes that write data (the student term repair tools, restore,
     * copy and correct actions) or end in dd() (the repair tools' reports),
     * stream big exports or card sheets, or are ajax lookups that load whole
     * tables when called without filters.
     */
    public const SKIP = '/remove|restore|update_|copy|correctMay|approveStandard|fix_|fixMismatched|mismatchedGrade|reorder_|unorder_|resubmit_|export|excel|cards|load-more|logout|_debugbar|log-viewer|\/get[-_A-Z]|_get_/i';

    private const BASELINE = 'tests/smoke-baseline.json';

    public function test_guest_pages()
    {
        $this->check('guest', null, function ($uri) {
            return !preg_match('#^(manager|school|inspection|student)/#', $uri) || preg_match('#/(login|register|password/reset)$#', $uri);
        });
    }

    public function test_manager_pages()
    {
        $this->check('manager', self::manager(), $this->underPrefix('manager'));
    }

    public function test_school_pages()
    {
        $this->check('school', self::school(), $this->underPrefix('school'));
    }

    public function test_inspection_pages()
    {
        $this->check('inspection', self::inspection(), $this->underPrefix('inspection'));
    }

    public function test_student_pages()
    {
        $this->check('student', self::student(), $this->underPrefix('student'));
    }

    /**
     * The accounts every upgrade check logs in with, picked the same way on
     * both versions. Call inside a transaction: the school and inspection
     * accounts are changed (see below) and must be rolled back.
     */
    public static function manager()
    {
        return Manager::query()
            ->where('approved', 1)
            ->where('force_password_change', 0)
            ->get()
            ->sortByDesc(function ($manager) {
                return $manager->getAllPermissions()->count();
            })
            ->first();
    }

    public static function school()
    {
        // the approved school with the most corrected assessments, so lists and
        // reports have real data to show
        $schoolId = DB::table('student_terms')
            ->join('students', 'students.id', '=', 'student_terms.student_id')
            ->join('schools', 'schools.id', '=', 'students.school_id')
            ->where('schools.approved', 1)->where('student_terms.corrected', 1)
            ->whereNull('schools.deleted_at')->whereNull('students.deleted_at')->whereNull('student_terms.deleted_at')
            ->groupBy('schools.id')
            ->orderByRaw('count(*) desc')->orderBy('schools.id')
            ->value('schools.id');
        $school = School::query()->findOrFail($schoolId);
        // Every local school has a forced password change pending. The query
        // builder, so updated_at (shown in the lists) is left alone.
        DB::table('schools')->where('id', $school->id)->update(['force_password_change' => 0]);

        return $school->refresh();
    }

    public static function inspection()
    {
        // Both local inspection accounts are unapproved, have a forced password
        // change pending and no schools: approve the first one and give it the
        // school above and the first approved one, so its pages show real data.
        $inspection = Inspection::query()->orderBy('id')->first();
        DB::table('inspections')->where('id', $inspection->id)->update(['approved' => 1, 'force_password_change' => 0]);
        $schools = [self::school()->id, School::query()->where('approved', 1)->whereHas('students')->orderBy('id')->value('id')];
        foreach (array_unique($schools) as $schoolId) {
            DB::table('inspection_schools')->insertOrIgnore([
                'inspection_id' => $inspection->id, 'school_id' => $schoolId,
                'created_at' => '2026-10-22 00:00:00', 'updated_at' => '2026-10-22 00:00:00',
            ]);
        }

        return $inspection->refresh();
    }

    public static function student()
    {
        return Student::query()
            ->whereHas('school', function ($query) {
                $query->where('approved', 1);
            })
            ->orderBy('id')
            ->first();
    }

    private function underPrefix($prefix)
    {
        return function ($uri) use ($prefix) {
            return strpos($uri, $prefix . '/') === 0 && !preg_match('#/(login|register|password/reset)$#', $uri);
        };
    }

    private function check($guard, $user, callable $filter)
    {
        if ($guard !== 'guest') {
            $this->assertNotNull($user, "No {$guard} account found in the local database.");
        }

        $results = [];
        foreach ($this->pages($filter) as $uri) {
            if ($user) {
                $this->actingAs($user, $guard);
            }

            if (getenv('SMOKE_VERBOSE')) {
                fwrite(STDERR, sprintf("\n[%s] %s %dMB", $guard, $uri, memory_get_usage(true) / 1048576));
            }

            $response = $this->get('/' . ltrim($uri, '/'));
            $status = $response->getStatusCode();
            $results[$uri] = $status;

            if ($status >= 500 && $response->exception) {
                fwrite(STDERR, sprintf("\n[%s] %s -> %d %s: %s", $guard, $uri, $status,
                    get_class($response->exception), strtok($response->exception->getMessage(), "\n")));
            }

            $this->flushSession();
        }

        $baseline = $this->baseline();

        if (getenv('SMOKE_RECORD')) {
            $baseline[$guard] = $results;
            ksort($baseline);
            file_put_contents(base_path(self::BASELINE), json_encode($baseline, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
            $this->assertNotEmpty($results);

            return;
        }

        $this->assertArrayHasKey($guard, $baseline, 'Record the baseline first with SMOKE_RECORD=1.');

        $problems = [];
        foreach ($baseline[$guard] as $uri => $expected) {
            if (!array_key_exists($uri, $results)) {
                $problems[] = "{$uri}: missing (was {$expected})";
                continue;
            }
            $actual = $results[$uri];
            $fixed = $expected >= 500 && $actual < 500;
            if ($actual !== $expected && !$fixed) {
                $problems[] = "{$uri}: {$actual} (was {$expected})";
            }
        }
        foreach (array_diff_key($results, $baseline[$guard]) as $uri => $actual) {
            if ($actual >= 500) {
                $problems[] = "{$uri}: {$actual} (new page)";
            }
        }

        $this->assertSame([], $problems, "Pages that changed for {$guard}:\n" . implode("\n", $problems));
    }

    private function pages(callable $filter)
    {
        $uris = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (!in_array('GET', $route->methods(), true) || strpos($uri, '{') !== false || preg_match(self::SKIP, $uri)) {
                continue;
            }
            if ($filter($uri)) {
                $uris[] = $uri;
            }
        }
        sort($uris);

        return array_values(array_unique($uris));
    }

    private function baseline()
    {
        $path = base_path(self::BASELINE);

        return is_file($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
