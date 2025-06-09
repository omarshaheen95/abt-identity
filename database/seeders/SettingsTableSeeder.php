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
        //['text', 'radio', 'checkbox', 'color', 'file', 'multi_select', 'select', 'number', 'password', 'textarea', 'date']
        $settings = [
            //General
            ['name' => 'Logo', 'key' => 'logo', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['name' => 'Min Logo', 'key' => 'logo_min', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['name' => 'Favicon Ico', 'key' => 'icon', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['name' => 'Arabic Name', 'key' => 'name_ar', 'value' => null, 'type' => 'text', 'group' => 'general'],
            ['name' => 'English Name', 'key' => 'name_en', 'value' => null, 'type' => 'text', 'group' => 'general'],
            ['name' => 'Mobile', 'key' => 'mobile', 'value' => null, 'type' => 'text', 'group' => 'general'],
            ['name' => 'Email', 'key' => 'email', 'value' => null, 'type' => 'text', 'group' => 'general'],
            //SMTP
            ['name' => 'SMTP MAIL Driver', 'key' => 'smtp_mail_driver', 'value' => 'smtp', 'type' => 'text', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL HOST', 'key' => 'smtp_mail_host', 'value' => 'smtp.office365.com', 'type' => 'text', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL PORT', 'key' => 'smtp_mail_port', 'value' => 587, 'type' => 'number', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL Encryption', 'key' => 'smtp_mail_encryption', 'value' => 'tls', 'type' => 'text', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL Username', 'key' => 'smtp_mail_username', 'value' => null, 'type' => 'text', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL Password', 'key' => 'smtp_mail_password', 'value' => null, 'type' => 'password', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL From Address', 'key' => 'smtp_mail_from_address', 'value' => 'support@abt-assessments.com', 'type' => 'text', 'group' => 'SMTP'],
            ['name' => 'SMTP MAIL From Name', 'key' => 'smtp_mail_from_name', 'value' => 'ABT-Assessments', 'type' => 'text', 'group' => 'SMTP'],
            //Telegram
            ['name' => 'Telegram Bot Token', 'key' => 'telegram_bot_token', 'value' => '7204627160:AAFBry-YE65Ntg0ijk0K4TJl5ghRYO6qrBI', 'type' => 'text', 'group' => 'telegram'],
            ['name' => 'Telegram Channel ID', 'key' => 'telegram_channel_id', 'value' => '-4542490273', 'type' => 'text', 'group' => 'telegram'],
            //Captcha
            ['name' => 'reCAPTCHA secret key', 'key' => 'captcha_secret_key', 'value' => null, 'type' => 'text', 'group' => 'captcha'],
            ['name' => 'reCAPTCHA site key', 'key' => 'captcha_site_key', 'value' => null, 'type' => 'text', 'group' => 'captcha'],
            //Assessments
            ['name' => 'Submit Assessment When Timeout', 'key' => 'submit_when_timeout', 'value' => 1, 'type' => 'checkbox', 'group' => 'assessment'],

        ];

        $rows = Setting::query()->get();
        foreach ($settings as $setting) {
            $row = $rows->where('key', $setting['key'])->first();
            if (!$row) {
                Setting::query()->create($setting);
            } else {
                $row->update([
                    'name' => $setting['name'],
                    'type' => $setting['type'],
                    'group' => $setting['group']
                ]);
            }
        }
    }

}
