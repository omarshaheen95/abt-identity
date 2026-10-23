<?php
// Usage: php mail-render.php <project_dir> > out.html
// Renders the manager password reset email as it would be sent.
$dir = $argv[1];
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manager = App\Models\Manager::query()->orderBy('id')->first();
$mail = (new App\Notifications\ManagerResetPassword('TOKEN'))->toMail($manager);
echo $mail->render();
