<?php

namespace App\Providers;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Factory;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;


class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Factory $cache)
    {
        //check if has connection
        if (DB::connection()->getDatabaseName()) {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                Cache::forget('settings');
                $settings = Cache::remember('settings', 60 * 48, function () {
                    $data = \App\Models\Setting::query()->get()->pluck('value', 'key')->all();
                    return $data;
                });
                config()->set('settings', $settings);

                // Update Mail Configuration
                if (isset($settings['smtp_mail_driver']) && isset($settings['smtp_mail_host'])) {
                    Config::set('mail.mailers.smtp.transport', $settings['smtp_mail_driver']);
                    Config::set('mail.mailers.smtp.host', $settings['smtp_mail_host']);
                    Config::set('mail.mailers.smtp.port', $settings['smtp_mail_port']);
                    Config::set('mail.mailers.smtp.username', $settings['smtp_mail_username']);
                    Config::set('mail.mailers.smtp.password', $settings['smtp_mail_password']);
                    Config::set('mail.mailers.smtp.encryption', $settings['smtp_mail_encryption']);
                    Config::set('mail.from.address', $settings['smtp_mail_from_address'] ?? 'default@example.com');
                    Config::set('mail.from.name', $settings['smtp_mail_from_name'] ?? 'Default');
                }
                if (isset($settings['name_en'])) {
                    Config::set('app.name', $settings['name_en']);
                }

                if (isset($settings['telegram_bot_token'])) {
                    Config::set('app.telegram_bot_token', $settings['telegram_bot_token']);
                    Config::set('app.telegram_channel_id', $settings['telegram_channel_id']);
                }
//                if (isset($settings['captcha_secret_key'])) {
//                    Config::set('captcha.secret', $settings['captcha_secret_key']);
//                    Config::set('captcha.sitekey', $settings['captcha_site_key']);
//                }

            }
        }
    }
}
