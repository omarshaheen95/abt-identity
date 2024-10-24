<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Factory $cache)
    {
        try {
            if(DB::connection()->getPdo()){
                if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {

                    $settings = $cache->remember('settings', 60, function () {
                        // Laravel >= 5.2, use 'lists' instead of 'pluck' for Laravel <= 5.1
                        return Setting::query()->get();
                    });
                }
            }
           // dump('Database connected: ' . 'Yes');

        }catch (\Exception $e){
            dump('Database connected: ' . 'No');
        }


    }
}
