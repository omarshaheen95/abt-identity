<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): runs the Excel exports of the
 * three panels with the same filters and writes every sheet, cell by cell,
 * into SNAPSHOT_DIR. The student exports are limited to one grade of the
 * school SmokeRoutesTest picks, so the files stay small.
 */
class ExportSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
        // the same "now" on both versions (export titles and dates)
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    public function test_exports()
    {
        $dir = getenv('SNAPSHOT_DIR');
        @mkdir($dir, 0777, true);

        $manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $inspection = SmokeRoutesTest::inspection();
        $yearId = $school->available_year_id;
        $level = DB::table('levels')->where('year_id', $yearId)->where('grade', 4)->whereNull('deleted_at')->orderBy('id')->first();
        $termId = DB::table('terms')->where('level_id', $level->id)->whereNull('deleted_at')->orderBy('id')->value('id');
        $importFile = DB::table('student_import_files')->where('school_id', $school->id)->whereNull('deleted_at')->orderByDesc('id')->value('id');

        $grade = ['year_id' => $yearId, 'grade' => 4];
        $bySchool = ['school_id' => $school->id] + $grade;
        $standards = ['year_id' => $yearId, 'level_id' => [$level->id]];

        $calls = [
            ['manager', $manager, 'post', '/manager/managers-export', []],
            ['manager', $manager, 'post', '/manager/export-schools', []],
            ['manager', $manager, 'post', '/manager/level-export', []],
            ['manager', $manager, 'post', '/manager/inspections-export', []],
            ['manager', $manager, 'post', '/manager/term-export', ['year_id' => $yearId]],
            ['manager', $manager, 'post', '/manager/terms_questions_export', ['term_id' => $termId]],
            ['manager', $manager, 'post', '/manager/standards_export', ['term_id' => $termId]],
            ['manager', $manager, 'post', '/manager/login_sessions/export', ['login_guard' => 'manager']],
            ['manager', $manager, 'get', "/manager/students_files_import/$importFile/export_excel", []],
            ['manager', $manager, 'post', '/manager/student-export', $bySchool],
            ['manager', $manager, 'post', '/manager/student-marks-export', $bySchool],
            ['manager', $manager, 'post', '/manager/student-terms-export', $bySchool + ['corrected' => 1]],
            ['manager', $manager, 'post', '/manager/students_not_submitted_terms_export', $bySchool + ['round' => 'september']],
            ['manager', $manager, 'post', '/manager/export_student_standards', ['school_id' => $school->id] + $standards],
            ['school', $school, 'post', '/school/student-export', $grade],
            ['school', $school, 'post', '/school/student-marks-export', $grade],
            ['school', $school, 'post', '/school/students_not_submitted_terms/export', $grade + ['round' => 'september']],
            ['school', $school, 'post', '/school/export_student_standards', $standards],
            ['inspection', $inspection, 'get', '/inspection/student-export', $bySchool],
            ['inspection', $inspection, 'get', '/inspection/student-marks-export', $bySchool],
            ['inspection', $inspection, 'post', '/inspection/export-schools', []],
        ];

        foreach ($calls as [$guard, $user, $method, $url, $data]) {
            $this->actingAs($user, $guard);
            $response = $method === 'post' ? $this->post($url, $data) : $this->get($url . '?' . http_build_query($data));
            $out = "URL $url\nSTATUS " . $response->getStatusCode() . "\n";
            try {
                $e = $response->exception;
            } catch (\Throwable $ignored) {
                $e = null;
            }
            if ($e) {
                $out .= 'EXCEPTION ' . get_class($e) . ': ' . strtok($e->getMessage(), "\n") . "\n";
            }
            $base = $response->baseResponse;
            if ($base instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                $out .= 'FILE ' . $response->headers->get('Content-Disposition') . "\n";
                $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($base->getFile()->getPathname());
                foreach ($book->getAllSheets() as $sheet) {
                    $out .= "SHEET " . $sheet->getTitle() . ' ' . $sheet->getHighestColumn() . $sheet->getHighestRow() . "\n";
                    foreach ($sheet->toArray(null, false, false, false) as $row) {
                        $out .= json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
            } else {
                $out .= 'LOCATION ' . $response->headers->get('Location') . "\n";
                $out .= 'FLASH ' . json_encode(session('errors') ? session('errors')->getBag('default')->toArray() : session('message'), JSON_UNESCAPED_UNICODE) . "\n";
                $out .= 'BODY ' . substr((string) $response->getContent(), 0, 300) . "\n";
            }
            $name = $guard . '_' . trim(preg_replace('/[^A-Za-z0-9]+/', '_', $url), '_');
            file_put_contents("$dir/export_$name.txt", $out);
            $this->flushSession();
            ResponseSnapshotTest::resetAuth();
        }

        $this->assertTrue(true);
    }
}
