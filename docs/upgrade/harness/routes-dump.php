<?php
// Usage: php routes-dump.php <project_dir> > out.json
$dir = $argv[1];
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$out = [];
foreach ($app['router']->getRoutes() as $r) {
    $mw = $app['router']->gatherRouteMiddleware($r);
    $mw = array_map(function ($m) { return is_string($m) ? $m : get_class($m); }, $mw);
    $out[] = [
        'methods' => implode('|', array_diff($r->methods(), ['HEAD'])),
        'uri' => $r->uri(),
        'name' => $r->getName(),
        'action' => ltrim($r->getActionName(), chr(92)),
        'mw' => array_values($mw),
    ];
}
usort($out, function ($a, $b) { return strcmp($a['methods'].' '.$a['uri'], $b['methods'].' '.$b['uri']); });
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
