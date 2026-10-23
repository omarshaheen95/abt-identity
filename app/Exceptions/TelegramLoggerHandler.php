<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Exceptions;

use App\Jobs\LogMessageJob;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class TelegramLoggerHandler extends AbstractProcessingHandler {
    protected function write($record): void {
        $levelMarkers = [
            'DEBUG'     => '🐛 DEBUG',
            'INFO'      => 'ℹ️ INFO',
            'NOTICE'    => '📝 NOTICE',
            'WARNING'   => '⚠️ WARNING',
            'ERROR'     => '❌ ERROR',
            'CRITICAL'  => '🔥 CRITICAL',
            'ALERT'     => '🚨 ALERT',
            'EMERGENCY' => '🚩 EMERGENCY',
        ];

        $levelMarker = $levelMarkers[strtoupper($record['level_name'])] ?? 'LOG';

        $currentUrl = request()->fullUrl();

        $message = "<b>Application:</b> " . config('app.name')=='Laravel'?'ABT-IDENTITY':config('app.name') . "\n" .
            "\n".
            "<b>URL:</b> " . $currentUrl.
            "\n".
            "<b>Time:</b> " . $record['datetime']->format('Y-m-d H:i:s') . "\n" .
            "<b>Level:</b> " . $levelMarker . "\n" .
            "\n".
            "<b>Message:</b> " . $record['message'];

        //get Current guard name and user if authenticated
        $guard = auth()->getDefaultDriver();
        $user = auth()->user();
        if ($user) {
            $message .= "\n\n<b>Guard:</b> " . $guard . "\n" .
                "<b>User:</b> " . $user->name . " (ID: " . $user->id . ")";
        }


        $request_data = $this->mask(request()->all());

        if (!empty($request_data)) {
            $requestData = json_encode($request_data, JSON_PRETTY_PRINT);
            $message .= "\n\n<b>Request:</b>\n<pre>" . htmlspecialchars($requestData) . "</pre>";
        }

        if (!empty($record['context'])) {
            $contextDetails = json_encode($record['context'], JSON_PRETTY_PRINT);
            $message .= "\n\n<b>Details:</b>\n<pre>" . htmlspecialchars($contextDetails) . "</pre>";
        }

        $sessionData = $this->mask(session()->all());
        if (!empty($sessionData)) {
            $sessionDetails = json_encode($sessionData, JSON_PRETTY_PRINT);
            $message .= "\n\n<b>Session:</b>\n<pre>" . htmlspecialchars($sessionDetails) . "</pre>";
        }

        LogMessageJob::dispatch($message);
    }

    /**
     * The request and the session go to a Telegram group: hide the passwords
     * (password, old_password, password_confirmation...), the CSRF token, and
     * the password hashes the session keeps (password_hash_<guard>).
     */
    private function mask(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && preg_match('/pass|token|secret/i', $key)) {
                $data[$key] = '***';
            } elseif (is_array($value)) {
                $data[$key] = $this->mask($value);
            }
        }

        return $data;
    }
}
