<?php

namespace Tests\Feature;

use App\Models\Manager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): asks for a password reset link
 * for each guard (a known and an unknown email), then opens the reset form of
 * the link that was sent. Writes SNAPSHOT_DIR/password-reset.txt. Nothing is
 * mailed (notifications are faked) and the tokens are rolled back.
 */
class PasswordResetSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    public function test_reset_links()
    {
        Notification::fake();
        $users = [
            'manager' => Manager::query()->where('approved', 1)->orderBy('id')->first(),
            'school' => SmokeRoutesTest::school(),
            'inspection' => SmokeRoutesTest::inspection(),
            // students sign in with a username only, but the reset routes exist
            'student' => SmokeRoutesTest::student(),
        ];
        $out = [];
        foreach ($users as $guard => $user) {
            $r = $this->post("/$guard/password/email", ['email' => 'nobody@example.invalid']);
            $out[] = "[$guard] unknown email: status {$r->getStatusCode()}, errors ".json_encode($this->errors(), JSON_UNESCAPED_UNICODE);
            $this->flushSession();

            $r = $this->post("/$guard/password/email", ['email' => $user->email]);
            $out[] = "[$guard] known email: status {$r->getStatusCode()}, status message ".json_encode(session('status'), JSON_UNESCAPED_UNICODE)
                .', errors '.json_encode($this->errors(), JSON_UNESCAPED_UNICODE);
            $this->flushSession();

            $sent = Notification::sent($user, $this->notificationClass($guard));
            $out[] = "[$guard] notifications sent: ".count($sent);
            foreach ($sent as $notification) {
                $mail = $notification->toMail($user);
                $url = (string) $mail->actionUrl;
                $token = $notification->token;
                $out[] = "[$guard] mail subject: ".$mail->subject.', action: '.$mail->actionText
                    .', url: '.str_replace($token, 'TOKEN', $url);
                $out[] = "[$guard] lines: ".json_encode(array_merge($mail->introLines, $mail->outroLines), JSON_UNESCAPED_UNICODE);
                // the whole message as it is sent: layout, theme and buttons
                file_put_contents(getenv('SNAPSHOT_DIR')."/mail-reset-$guard.html", str_replace($token, 'TOKEN', (string) $mail->render()));
                $form = $this->get(parse_url($url, PHP_URL_PATH));
                $out[] = "[$guard] reset form: status {$form->getStatusCode()}, has token field "
                    .json_encode(strpos((string) $form->getContent(), $token) !== false);
                $this->flushSession();
            }
            $table = config("auth.passwords.{$guard}s.table");
            $out[] = "[$guard] token rows in $table: ".DB::table($table)->where('email', $user->email)->count();
        }
        file_put_contents(getenv('SNAPSHOT_DIR').'/password-reset.txt', implode("\n", $out)."\n");
        $this->assertTrue(true);
    }

    private function notificationClass($guard)
    {
        return 'App\\Notifications\\'.ucfirst($guard).'ResetPassword';
    }

    private function errors()
    {
        $errors = session('errors');
        return is_object($errors) ? $errors->getBag('default')->toArray() : $errors;
    }
}
