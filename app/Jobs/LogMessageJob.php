<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Throwable;

class LogMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    public function __construct(?string $message) {
        $this->message = $message;
        $this->queue = 'log';
    }

    public function handle(): void {
        $token = env('TELEGRAM_BOT_TOKEN', '7204627160:AAFBry-YE65Ntg0ijk0K4TJl5ghRYO6qrBI');
        $chatId = env('TELEGRAM_CHAT_ID', "-4542490273");

        try {
            $client = new \GuzzleHttp\Client([
                //'verify' => false, // Disable SSL certificate verification
            ]);
            $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $this->message,
                    'parse_mode' => 'HTML',
                ]
            ]);
        } catch (Throwable $e) {
            \Log::alert('Telegram Logger Error: '.$e->getMessage());
        }
    }
}
