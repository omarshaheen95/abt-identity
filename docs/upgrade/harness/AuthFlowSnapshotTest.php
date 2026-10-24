<?php

namespace Tests\Feature;

use App\Models\Manager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): walks the login / next pages /
 * second login in the same session / failed login / lockout / forced password
 * change / logout flow of every guard, the manager signing in as a school, an
 * inspection account and a student, and the student login, and writes what
 * happened to SNAPSHOT_DIR/auth.txt. All changes are rolled back.
 *
 * Every step after a login opens more than one page: Laravel 11+ writes its
 * own value under password_hash_<guard> at login, the key ValidateSessionPassword
 * uses, and a clash only shows on the request after the login.
 */
class AuthFlowSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
        // the same "now" on both versions (password expiry depends on it)
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    private $log = [];

    private const PAGES = [
        'manager' => ['/manager/home', '/manager/update-profile', '/manager/school'],
        'school' => ['/school/home', '/school/profile', '/school/student'],
        'inspection' => ['/inspection/home', '/inspection/profile', '/inspection/school'],
    ];

    public function test_flows()
    {
        $password = 'Upgrade-Check-2026!';
        $hash = Hash::make($password);

        // the school and inspection accounts are made usable inside the transaction
        $users = [
            'manager' => Manager::query()->where('approved', 1)->where('force_password_change', 0)->orderBy('id')->first(),
            'school' => SmokeRoutesTest::school(),
            'inspection' => SmokeRoutesTest::inspection(),
        ];
        $tables = ['manager' => 'managers', 'school' => 'schools', 'inspection' => 'inspections'];

        foreach ($users as $guard => $user) {
            // the same password age everywhere, so expiry does not depend on the day of the run
            DB::table($tables[$guard])->where('id', $user->id)->update([
                'password' => $hash, 'force_password_change' => 0, 'password_changed_at' => now()->subDay(),
            ]);
            $before = (int) DB::table('login_sessions')->max('id');

            $r = $this->post("/$guard/login", ['email' => $user->email, 'password' => $password]);
            $this->record($guard, 'login ok', $r, ['authenticated' => auth($guard)->check()]);
            $this->record($guard, 'follow redirect', $this->get($r->headers->get('Location') ?: "/$guard/home"));
            foreach (self::PAGES[$guard] as $page) {
                $this->record($guard, "then $page", $this->get($page));
            }
            $r = $this->post("/$guard/logout");
            $this->record($guard, 'logout', $r, ['authenticated' => auth($guard)->check()]);

            // signing in again in the same browser session
            $r = $this->post("/$guard/login", ['email' => $user->email, 'password' => $password]);
            $this->record($guard, 'second login, same session', $r, ['authenticated' => auth($guard)->check()]);
            foreach (self::PAGES[$guard] as $page) {
                $this->record($guard, "then $page", $this->get($page));
            }
            $this->post("/$guard/logout");
            $this->flushSession();
            app('auth')->guard($guard)->logout();

            $r = $this->post("/$guard/login", ['email' => $user->email, 'password' => 'wrong-password']);
            $this->record($guard, 'login wrong password', $r, ['errors' => $this->errors()]);
            $this->flushSession();

            for ($i = 0; $i < 6; $i++) {
                $r = $this->post("/$guard/login", ['email' => $user->email, 'password' => 'wrong-password']);
            }
            $this->record($guard, 'after 7 wrong attempts', $r, ['errors' => $this->errors()]);
            $this->flushSession();
            app(\Illuminate\Cache\RateLimiter::class)->clear(strtolower($user->email) . '|127.0.0.1');

            // a forced password change keeps the account on the password screen
            DB::table($tables[$guard])->where('id', $user->id)->update(['force_password_change' => 1]);
            $r = $this->post("/$guard/login", ['email' => $user->email, 'password' => $password]);
            $this->record($guard, 'login with forced change', $r, ['authenticated' => auth($guard)->check()]);
            $this->record($guard, 'home with forced change', $this->get("/$guard/home"));
            $this->post("/$guard/logout");
            $this->flushSession();
            app('auth')->guard($guard)->logout();
            DB::table($tables[$guard])->where('id', $user->id)->update(['force_password_change' => 0]);

            $this->log[] = "[$guard] login_sessions rows written: " . json_encode($this->sessions($before), JSON_UNESCAPED_UNICODE);
            ResponseSnapshotTest::resetAuth();
        }

        // the manager signs in as the school, the inspection account and a student
        $student = SmokeRoutesTest::student();
        $r = $this->post('/manager/login', ['email' => $users['manager']->email, 'password' => $password]);
        $this->record('manager', 'login before impersonating', $r);
        $steps = [
            ['school', "/manager/school_login/{$users['school']->id}", self::PAGES['school']],
            ['inspection', "/manager/login-inspection/{$users['inspection']->id}", self::PAGES['inspection']],
            ['student', "/manager/student-login/{$student->id}", ['/student/home', '/student']],
        ];
        foreach ($steps as [$guard, $url, $pages]) {
            $r = $this->get($url);
            $this->record($guard, "manager opens $url", $r, ['authenticated' => auth($guard)->check()]);
            foreach ($pages as $page) {
                $this->record($guard, "then $page", $this->get($page));
            }
        }
        $this->record('manager', 'manager home after impersonating', $this->get('/manager/home'));
        $this->flushSession();
        ResponseSnapshotTest::resetAuth();

        $before = (int) DB::table('login_sessions')->max('id');
        $r = $this->post('/student/login', ['username' => strtoupper($student->email)]);
        $this->record('student', 'login ok', $r, ['authenticated' => auth('student')->check()]);
        $this->record('student', 'follow redirect', $this->get($r->headers->get('Location') ?: '/student/home'));
        $this->record('student', 'then /student', $this->get('/student'));
        $r = $this->post('/student/logout');
        $this->record('student', 'logout', $r, ['authenticated' => auth('student')->check()]);
        $this->flushSession();
        $r = $this->post('/student/login', ['username' => 'nobody-' . $student->id . '@example.invalid']);
        $this->record('student', 'login unknown email', $r, ['errors' => $this->errors()]);
        $this->log[] = "[student] login_sessions rows written: " . json_encode($this->sessions($before), JSON_UNESCAPED_UNICODE);

        file_put_contents(getenv('SNAPSHOT_DIR') . '/auth.txt', implode("\n", $this->log) . "\n");
        $this->assertTrue(true);
    }

    private function sessions($afterId)
    {
        return DB::table('login_sessions')->where('id', '>', $afterId)->orderBy('id')->get()
            ->map(function ($row) {
                $row = (array) $row;
                unset($row['id'], $row['created_at'], $row['updated_at'], $row['user_agent']);
                return $row;
            })->all();
    }

    private function errors()
    {
        $errors = session('errors');
        if (is_object($errors)) {
            return $errors->getBag('default')->toArray();
        }
        return $errors;
    }

    private function record($guard, $step, $response, array $extra = [])
    {
        try {
            $e = $response->exception;
        } catch (\Throwable $ignored) {
            $e = null;
        }
        $flash = [];
        foreach (['message', 'm-class'] as $key) {
            if (session()->has($key)) {
                $flash[$key] = session($key);
            }
        }
        $this->log[] = sprintf('[%s] %s: status %d, location %s, exception %s %s %s',
            $guard, $step, $response->getStatusCode(), $response->headers->get('Location') ?: '-',
            $e ? get_class($e) . ': ' . strtok($e->getMessage(), "\n") : '-',
            $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '',
            $flash ? json_encode($flash, JSON_UNESCAPED_UNICODE) : '');
    }
}
