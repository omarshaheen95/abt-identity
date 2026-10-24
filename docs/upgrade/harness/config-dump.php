<?php
// Usage: php config-dump.php <project_dir> > out.json
// Prints every config value after the app has booted (with the project's .env),
// so the configs of two versions can be diffed key by key.
$dir = $argv[1];
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$flat = [];
$walk = function ($value, $prefix) use (&$walk, &$flat) {
    if (is_array($value) && $value !== [] && array_keys($value) !== range(0, count($value) - 1)) {
        foreach ($value as $k => $v) {
            $walk($v, $prefix === '' ? (string) $k : "$prefix.$k");
        }
        return;
    }
    $flat[$prefix] = is_object($value) ? 'object:'.get_class($value) : $value;
    // never print credentials: only whether they are set
    if (preg_match('/(key|token|password|secret|channel_id|username)$/', $prefix) && is_string($value) && $value !== '') {
        $flat[$prefix] = 'SET';
    }
};
$walk($app['config']->all(), '');
ksort($flat);
echo json_encode($flat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
