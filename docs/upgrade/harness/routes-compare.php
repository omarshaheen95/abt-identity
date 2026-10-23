<?php
// Usage: php routes-compare.php routes-before.json routes-after.json
// Compares two routes-dump.php outputs. Middleware that only changed class
// (the app's copies of framework middleware, renamed packages) are mapped to
// their new name first; everything else must match, in the same order.
[$self, $beforeFile, $afterFile] = $argv;
$renames = [
    'App\Http\Middleware\EncryptCookies' => 'Illuminate\Cookie\Middleware\EncryptCookies',
    'App\Http\Middleware\VerifyCsrfToken' => 'Illuminate\Foundation\Http\Middleware\PreventRequestForgery',
    'App\Http\Middleware\Authenticate' => 'Illuminate\Auth\Middleware\Authenticate',
    'App\Http\Middleware\RedirectIfAuthenticated' => 'Illuminate\Auth\Middleware\RedirectIfAuthenticated',
    'Spatie\Permission\Middlewares\\' => 'Spatie\Permission\Middleware\\',
];
// package routes that were replaced on purpose
$ignore = '#^(_debugbar|log-viewer)(/|$)#';

$load = function ($file) use ($renames, $ignore) {
    $out = [];
    foreach (json_decode(file_get_contents($file), true) as $r) {
        if (preg_match($ignore, $r['uri'])) {
            continue;
        }
        $r['mw'] = array_map(function ($m) use ($renames) {
            foreach ($renames as $old => $new) {
                if (strpos($m, $old) === 0) {
                    return $new.substr($m, strlen($old));
                }
            }
            return $m;
        }, $r['mw']);
        $out[$r['methods'].' '.$r['uri']] = $r;
    }
    return $out;
};
$a = $load($beforeFile);
$b = $load($afterFile);
echo 'routes: '.count($a).' before, '.count($b)." after (without debugbar and log-viewer)\n";
foreach (array_diff_key($a, $b) as $k => $r) {
    echo "only before: $k {$r['action']}\n";
}
foreach (array_diff_key($b, $a) as $k => $r) {
    echo "only after: $k {$r['action']}\n";
}
$same = 0;
foreach (array_intersect_key($a, $b) as $k => $r) {
    $diff = [];
    foreach (['name', 'action', 'mw'] as $f) {
        if ($r[$f] !== $b[$k][$f]) {
            $diff[] = "$f: ".json_encode($r[$f])."\n    => ".json_encode($b[$k][$f]);
        }
    }
    if ($diff) {
        echo "$k\n  ".implode("\n  ", $diff)."\n";
    } else {
        $same++;
    }
}
echo "identical: $same\n";
