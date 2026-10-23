<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): the manager signs in as the same
 * school, and as the same inspection account, twice in one session, opening
 * pages in between. Laravel 11+ writes an HMAC under password_hash_<guard> at
 * each login, while ValidateSessionPassword already keeps the plain hash and
 * its owner for that guard. Writes SNAPSHOT_DIR/impersonate-twice.txt.
 */
class ImpersonateTwiceSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Never post reported exceptions to Telegram from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    public function test_impersonate_twice()
    {
        $school = SmokeRoutesTest::school();
        $inspection = SmokeRoutesTest::inspection();
        $this->actingAs(SmokeRoutesTest::manager(), 'manager');
        $out = [];
        $steps = [
            "/manager/school_login/{$school->id}", '/school/home', '/school/student',
            "/manager/school_login/{$school->id}", '/school/home', '/school/student',
            "/manager/login-inspection/{$inspection->id}", '/inspection/home',
            "/manager/login-inspection/{$inspection->id}", '/inspection/home', '/inspection/school',
        ];
        foreach ($steps as $url) {
            $r = $this->get($url);
            $out[] = sprintf('%s: %d %s %s', $url, $r->getStatusCode(), $r->headers->get('Location') ?: '-',
                json_encode(session('message'), JSON_UNESCAPED_UNICODE));
        }
        file_put_contents(getenv('SNAPSHOT_DIR') . '/impersonate-twice.txt', implode("\n", $out) . "\n");
        $this->assertTrue(true);
    }
}
