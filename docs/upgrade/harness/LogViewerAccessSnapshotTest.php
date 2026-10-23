<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): who can open the log viewer.
 * arcanedev/log-viewer (Laravel 7) sits at /log-viewer with no middleware at
 * all; opcodesio/log-viewer (Laravel 13) is meant to sit there behind the
 * manager guard. Writes SNAPSHOT_DIR/log-viewer.txt.
 */
class LogViewerAccessSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    public function test_access()
    {
        $users = [
            'guest' => null,
            'student' => SmokeRoutesTest::student(),
            'school' => SmokeRoutesTest::school(),
            'inspection' => SmokeRoutesTest::inspection(),
            'manager' => SmokeRoutesTest::manager(),
        ];
        $out = [];
        foreach ($users as $guard => $user) {
            if ($user) {
                $this->actingAs($user, $guard);
            }
            $r = $this->get('/log-viewer');
            $location = (string) $r->headers->get('Location');
            $out[] = sprintf('%s: %s %s', $guard, $r->getStatusCode() === 200 ? 'opens' : 'refused ('.$r->getStatusCode().')', $location);
            if ($user) {
                app('auth')->guard($guard)->logout();
            }
            $this->flushSession();
        }
        file_put_contents(getenv('SNAPSHOT_DIR').'/log-viewer.txt', implode("\n", $out)."\n");
        $this->assertTrue(true);
    }
}
