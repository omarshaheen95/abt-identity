<?php

namespace Tests\Feature;

use App\Exports\LoginSessionExport;
use App\Models\LoginSession;
use App\Models\Manager;
use App\Services\LoginActivityService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class LoginActivityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_session_routes_and_view_use_current_project_resources(): void
    {
        $routes = $this->app['router']->getRoutes();
        $route = $routes->getByName('manager.login_sessions.export');
        $this->assertSame(['POST'], $route->methods());
        $this->assertSame(\App\Http\Controllers\Manager\LoginSessionController::class.'@export', $route->getActionName());
        $view = file_get_contents(resource_path('views/manager/login_sessions/index.blade.php'));
        $this->assertStringNotContainsString('dashboard_assets', $view);
        $this->assertFileExists(public_path('assets_v1/js/datatable.js'));
        $this->assertNotEmpty($this->app['blade.compiler']->compileString($view));
    }

    public function test_unknown_failed_attempt_is_logged_without_secrets_and_can_be_filtered(): void
    {
        $request = Request::create('/student/login', 'POST', ['browserInfo' => ['unexpected']]);
        $this->app->instance('request', $request);
        event(new Failed('student', null, ['email' => 'auth-test@example.invalid', 'password' => 'secret-value', '_token' => 'secret-token']));
        $row = LoginSession::latest('id')->firstOrFail();
        $this->assertSame('failed', $row->status);
        $this->assertSame('student', $row->guard);
        $this->assertNull($row->model);
        $this->assertSame('user_not_found', $row->reason);
        $this->assertStringNotContainsString('secret', $row->toJson());
        $filter = new Request(['model_type' => 'Unknown', 'status' => 'failed', 'guard' => 'manager', 'login_guard' => 'student', 'email' => 'auth-test@example.invalid', 'row_id' => [$row->id]]);
        $this->assertSame([$row->id], LoginSession::filter($filter)->pluck('id')->all());
        $export = new LoginSessionExport($filter);
        $this->assertSame([$row->id], $export->query()->pluck('id')->all());
        $this->assertCount(13, $export->map($row));
        $this->assertSame('auth-test@example.invalid', $export->map($row)[6]);
        $xlsx = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
        $this->assertSame('PK', substr($xlsx, 0, 2));
    }

    public function test_lockout_keeps_the_current_guard(): void
    {
        $request = Request::create('/school/login', 'POST', ['email' => 'locked@example.invalid']);
        $this->app->instance('request', $request);
        event(new Lockout($request));
        $row = LoginSession::latest('id')->firstOrFail();
        $this->assertSame('lockout', $row->status);
        $this->assertSame('school', $row->guard);
        $this->assertSame('throttled', $row->reason);
    }

    public function test_success_and_logout_events_record_the_account(): void
    {
        $student = new Manager(['email' => 'student-auth@example.invalid']);
        $student->id = 2147483001;
        event(new \Illuminate\Auth\Events\Login('manager', $student, false));
        event(new \Illuminate\Auth\Events\Logout('manager', $student));
        $rows = LoginSession::where('model_type', Manager::class)->where('model_id', $student->id)->orderBy('id')->get();
        $this->assertSame(['success', 'logout'], $rows->pluck('status')->all());
        $this->assertSame(['student-auth@example.invalid', 'student-auth@example.invalid'], $rows->pluck('identifier')->all());
    }

    public function test_prune_dry_run_preserves_old_rows_and_rejects_invalid_retention(): void
    {
        LoginActivityService::record('failed', 'web');
        $row = LoginSession::latest('id')->firstOrFail();
        $row->created_at = now()->subDays(400);
        $row->save();
        $this->artisan('login-sessions:prune', ['--days' => 365, '--dry-run' => true])->assertExitCode(0);
        $this->assertNotNull($row->fresh());
        $this->artisan('login-sessions:prune', ['--days' => 0])->assertExitCode(1);
    }

    public function test_datatable_displays_unknown_accounts_and_escapes_identifiers(): void
    {
        LoginActivityService::record('failed', 'student', null, ['identifier' => '<script>alert(1)</script>']);
        $row = LoginSession::latest('id')->firstOrFail();
        $request = Request::create('/manager/login_sessions', 'GET', ['id' => $row->id], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->app->instance('request', $request);
        $response = $this->app->make(\App\Http\Controllers\Manager\LoginSessionController::class)->index($request);
        $data = $response->getData(true);
        $this->assertCount(1, $data['data']);
        $this->assertStringContainsString('&lt;script&gt;', $data['data'][0]['user']);
        $this->assertStringNotContainsString('<script>', $data['data'][0]['user']);
    }
    public function test_direct_student_login_records_an_unknown_account(): void
    {
        config(['app.locale' => 'en']);
        $this->app['translator']->setLocale('en');
        $this->app['translator']->addLines(['manager.Student Not Found' => 'Student Not Found'], 'en');
        $request = Request::create('/student/login', 'POST', ['username' => 'absent-auth-test@example.invalid']);
        $this->app->instance('request', $request);
        $response = $this->app->make(\App\Http\Controllers\StudentAuth\LoginController::class)->studentLogin($request);
        $this->assertSame(302, $response->getStatusCode());
        $row = LoginSession::latest('id')->firstOrFail();
        $this->assertSame('failed', $row->status);
        $this->assertSame('student', $row->guard);
        $this->assertSame('absent-auth-test@example.invalid', $row->identifier);
    }

}
