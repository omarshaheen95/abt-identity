<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::group([
                'middleware' => ['web', 'generalLocal'],
                'namespace' => $this->namespace,
            ], function ($router) {
                require base_path('routes/web.php');
            });

            Route::group([
                'middleware' => ['web', 'manager', 'auth:manager','checkIfActive', 'generalLocal','setRequestData'],
                'prefix' => 'manager',
                'as' => 'manager.',
                'namespace' => $this->namespace,
            ], function ($router) {
                require base_path('routes/manager.php');
            });
            Route::group([
                'middleware' => ['web', 'school', 'auth:school','checkIfActive','generalLocal','setRequestData'],
                'prefix' => 'school',
                'as' => 'school.',
                'namespace' => $this->namespace,
            ], function ($router) {
                require base_path('routes/school.php');
            });
            Route::group([
                'middleware' => ['web', 'inspection', 'auth:inspection', 'generalLocal','setRequestData'],
                'prefix' => 'inspection',
                'as' => 'inspection.',
                'namespace' => $this->namespace,
            ], function ($router) {
                require base_path('routes/inspection.php');
            });

            Route::group([
                'middleware' => ['web', 'student', 'auth:student', 'generalLocal','setRequestData'],
                'prefix' => 'student',
                'as' => 'student.',
                'namespace' => $this->namespace,
            ], function ($router) {
                require base_path('routes/student.php');
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?$request->user()->id:request()->ip();
            return Limit::perMinute(60)->by($key);
        });
    }
}
