<?php
// Checks that values cached by one version are read back by the other. The keys
// hold the kinds of values the app caches: a Setting model and a Collection of
// them ('settings'), a stdClass (all terms counters) and an array.
//   php cache-check.php <project_dir> write
//   php cache-check.php <project_dir> read [true|false]   overrides cache.serializable_classes
//   php cache-check.php <project_dir> clean
$dir = $argv[1];
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$values = function () {
    return [
        'model' => App\Models\Setting::query()->first(),
        'collection' => App\Models\Setting::query()->get(),
        'stdclass' => Illuminate\Support\Facades\DB::query()->selectRaw('1 as corrected_count, 2 as uncorrected_count')->first(),
        'array' => ['a' => 1, 'b' => [2, 3]],
    ];
};

if ($argv[2] === 'write') {
    foreach ($values() as $name => $value) {
        cache()->put('upgrade_check_'.$name, $value, 600);
    }
    echo "written\n";
    exit;
}
if ($argv[2] === 'clean') {
    foreach (array_keys($values()) as $name) {
        cache()->forget('upgrade_check_'.$name);
    }
    exit;
}

if (isset($argv[3])) {
    config(['cache.serializable_classes' => $argv[3] === 'true']);
    // the store was already built while booting (SettingServiceProvider reads the cache)
    app('cache')->forgetDriver(config('cache.default'));
}
echo 'serializable_classes: '.json_encode(config('cache.serializable_classes'))."\n";
foreach ($values() as $name => $expected) {
    $got = cache()->get('upgrade_check_'.$name);
    $same = is_object($expected)
        ? is_object($got) && get_class($got) === get_class($expected) && json_encode($got) === json_encode($expected)
        : $got === $expected;
    echo str_pad($name, 11).': '.(is_object($got) ? get_class($got) : gettype($got)).' '.($same ? 'same' : 'DIFFERENT')."\n";
}
