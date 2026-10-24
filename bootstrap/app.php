<?php

use App\Http\Middleware\SetLocalLanguage;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

$app = Application::configure(basePath: dirname(__DIR__))
    // UserEventSubscriber and the School observer are registered in
    // AppServiceProvider, as EventServiceProvider did. Discovery would register
    // the subscriber a second time.
    ->withEvents(discover: false)
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        // Same groups, in the same order, as the Laravel 8 RouteServiceProvider.
        using: function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'generalLocal'])
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'manager', 'auth:manager', 'checkIfActive', 'generalLocal', 'setRequestData', 'session.password:manager', 'force.password:manager'])
                ->prefix('manager')
                ->as('manager.')
                ->group(base_path('routes/manager.php'));

            Route::middleware(['web', 'school', 'auth:school', 'checkIfActive', 'generalLocal', 'setRequestData', 'session.password:school', 'force.password:school'])
                ->prefix('school')
                ->as('school.')
                ->group(base_path('routes/school.php'));

            Route::middleware(['web', 'inspection', 'auth:inspection', 'generalLocal', 'setRequestData', 'session.password:inspection', 'force.password:inspection'])
                ->prefix('inspection')
                ->as('inspection.')
                ->group(base_path('routes/inspection.php'));

            Route::middleware(['web', 'student', 'auth:student', 'generalLocal', 'setRequestData'])
                ->prefix('student')
                ->as('student.')
                ->group(base_path('routes/student.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trimStrings(except: ['old_password']);

        // CORS was never in the Laravel 8 global stack (fruitcake/laravel-cors
        // was installed but not registered), so the responses carried no CORS
        // headers.
        $middleware->remove(\Illuminate\Http\Middleware\HandleCors::class);

        // The Laravel 8 groups
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // RouteServiceProvider::HOME of Laravel 8, for the "guest" middleware
        $middleware->redirectUsersTo('/home');

        $middleware->alias([
            'student' => \App\Http\Middleware\RedirectIfNotStudent::class,
            'student.guest' => \App\Http\Middleware\RedirectIfStudent::class,
            'inspection' => \App\Http\Middleware\RedirectIfNotInspection::class,
            'inspection.guest' => \App\Http\Middleware\RedirectIfInspection::class,
            'school' => \App\Http\Middleware\RedirectIfNotSchool::class,
            'school.guest' => \App\Http\Middleware\RedirectIfSchool::class,
            'manager' => \App\Http\Middleware\RedirectIfNotManager::class,
            'manager.guest' => \App\Http\Middleware\RedirectIfManager::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'generalLocal' => SetLocalLanguage::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'checkIfActive' => \App\Http\Middleware\CheckIfActive::class,
            'setRequestData' => \App\Http\Middleware\SetRequestDataMiddleware::class,
            'desktop.only' => \App\Http\Middleware\DesktopOnlyMiddleware::class,
            'force.password' => \App\Http\Middleware\ForcePasswordChange::class,
            'session.password' => \App\Http\Middleware\ValidateSessionPassword::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('login-sessions:prune --days=365')->dailyAt('03:00');
        $schedule->command('proctor:clean-images')->dailyAt('02:00');
        $schedule->command('student-terms:auto-correct')
            ->dailyAt('00:00')
            ->appendOutputTo(storage_path('logs/auto-correction.log'));
    })
    ->create();

// The Laravel 8 exception handler is kept as it is: it reports to Telegram,
// shapes the JSON errors and the per-guard login redirects.
$app->singleton(ExceptionHandler::class, \App\Exceptions\Handler::class);

return $app;
