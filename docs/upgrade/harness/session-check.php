<?php
// Checks that a session written by one version is still read by the other.
//   php session-check.php <project_dir> write <file>   writes a session, prints its file name
//   php session-check.php <project_dir> read <file> [serialization]
// <file> is a session file path: "write" saves it there, "read" loads it.
[$self, $dir, $mode, $file] = $argv;
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = str_repeat('a', 40);
$handler = new Illuminate\Session\FileSessionHandler(new Illuminate\Filesystem\Filesystem, dirname($file), 120);
$serialization = $argv[4] ?? config('session.serialization', 'php');
$store = $serialization === 'php' || !property_exists(Illuminate\Session\Store::class, 'serialization')
    ? new Illuminate\Session\Store('laravel_session', $handler, $id)
    : new Illuminate\Session\Store('laravel_session', $handler, $id, $serialization);

if ($mode === 'write') {
    $store->start();
    $store->put('login_manager_59ba36addc2b2f9401580f014c7f58ea4e30989d', 3);
    $store->put('password_hash_manager', 'example');
    $store->put('_token', 'upgrade-check-token');
    $store->save();
    rename(dirname($file).'/'.$id, $file);
    echo "written: $file\n";
} else {
    copy($file, dirname($file).'/'.$id);
    $store->start();
    echo 'serialization: '.$serialization."\n";
    echo 'manager id: '.json_encode($store->get('login_manager_59ba36addc2b2f9401580f014c7f58ea4e30989d'))."\n";
    echo 'csrf token kept: '.json_encode($store->token() === 'upgrade-check-token')."\n";
    @unlink(dirname($file).'/'.$id);
}
