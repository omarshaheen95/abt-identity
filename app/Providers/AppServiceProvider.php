<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Database\Eloquent\Builder;
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
        Builder::macro('withConditions', function ($school, $grade, $year, $sections, $include_sen, $include_g_t, $arab) {
            return $this
                ->where('school_id', $school->id)
                ->when(is_array($grade), function ($query) use ($grade) {
                    $query->whereHas('level', function ($query) use ($grade) {
                        $query->whereIn('grade', $grade);
                    });
                }, function ($query) use ($grade) {
                    $query->whereRelation('level', 'grade', $grade);
                })
                ->where('year_id', $year)
                ->when(count($sections) > 0, function ($query) use ($sections) {
                    $query->whereIn('grade_name', $sections);
                })
                ->when(!$include_sen, function (Builder $query) {
                    $query->where('sen', 0);
                })
                ->when(!$include_g_t, function (Builder $query) {
                    $query->where('g_t', 0);
                })
                ->where('arab', $arab);
        });
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
