<?php
// Checks that the encrypted session cookie and encrypted values (QR links of the
// printed reports) made by one version are read by the other, with the same APP_KEY.
//   php cookie-check.php <project_dir> make            prints a cookie value and an encrypted string
//   php cookie-check.php <project_dir> read <cookie> <encrypted>
$dir = $argv[1];
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$name = config('session.cookie');
$sessionId = str_repeat('b', 40);

if ($argv[2] === 'make') {
    // what EncryptCookies sends for the session cookie
    $value = Illuminate\Cookie\CookieValuePrefix::create($name, app('encrypter')->getKey()).$sessionId;
    echo app('encrypter')->encrypt($value, false), "\n";
    echo encrypt(['student_id' => 1, 'term' => 2]), "\n";
} else {
    $plain = app('encrypter')->decrypt($argv[3], false);
    // the check EncryptCookies does (Laravel 7 has no CookieValuePrefix::validate())
    $prefix = Illuminate\Cookie\CookieValuePrefix::create($name, app('encrypter')->getKey());
    $id = strpos($plain, $prefix) === 0 ? substr($plain, strlen($prefix)) : null;
    echo "cookie name: $name\n";
    echo 'session id read back: '.json_encode($id === $sessionId)."\n";
    echo 'encrypted value read back: '.json_encode(decrypt($argv[4]))."\n";
}
