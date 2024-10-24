<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Factory $cache)
    {
        $settings = [
            ['name' => 'Logo', 'key' => 'logo', 'value' => null, 'type' => 'file'],
            ['name' => 'Min Logo', 'key' => 'logo_min', 'value' => null, 'type' => 'file'],
            ['name' => 'Favicon Ico', 'key' => 'icon', 'value' => null, 'type' => 'file'],
            ['name' => 'Arabic Name', 'key' => 'name_ar', 'value' => null, 'type' => 'text'],
            ['name' => 'English Name', 'key' => 'name_en', 'value' => null, 'type' => 'text'],
            ['name' => 'Mobile', 'key' => 'mobile', 'value' => null, 'type' => 'text'],
            ['name' => 'Email', 'key' => 'email', 'value' => null, 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            $sets = Setting::query()->updateOrCreate(
                [
                    'key' => $setting['key'],
                ],
                [
                    'name' => $setting['name'],
                    'type' => $setting['type'],
                    'value' => $setting['value']
                ]
            );
        }

        $settings = $cache->remember('settings', 60, function () use ($settings) {
            $settings = Setting::get();
            // Laravel >= 5.2, use 'lists' instead of 'pluck' for Laravel <= 5.1
            return $settings->pluck('value', 'key')->all();
        });
        config()->set('settings', $settings);
    }

}
