<?php
// Usage: php load-all.php <project_dir>
// Boots the app, then loads every class under app/ and the helper files, and
// prints each error, warning or deprecation PHP raises while doing so.
$dir = rtrim($argv[1], '/\\');
$problems = [];
set_error_handler(function ($no, $message, $file, $line) use (&$problems) {
    $problems[] = "$message ($file:$line)";
    return true;
});
require $dir.'/vendor/autoload.php';
$app = require $dir.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// Laravel's own handler replaced ours during bootstrap
set_error_handler(function ($no, $message, $file, $line) use (&$problems) {
    $problems[] = "$message ($file:$line)";
    return true;
});

$count = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$dir/app"));
foreach ($it as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    $src = file_get_contents($file->getPathname());
    if (!preg_match('/^namespace\s+([^;]+);/m', $src, $ns) || !preg_match('/^\s*(?:abstract\s+|final\s+|readonly\s+)*(class|trait|interface|enum)\s+(\w+)/m', $src, $cls)) {
        continue;
    }
    $class = $ns[1].'\\'.$cls[2];
    try {
        if (!class_exists($class) && !trait_exists($class) && !interface_exists($class) && !enum_exists($class)) {
            $problems[] = "not loadable: $class ({$file->getPathname()})";
        }
        $count++;
        // assignments to properties the class never declares (deprecated since PHP 8.2)
        if (class_exists($class) && !method_exists($class, '__set')
            && preg_match_all('/\$this->(\w+)\s*(?:\[[^\]]*\]\s*)?=(?!=|>)/', $src, $m)) {
            foreach (array_unique($m[1]) as $prop) {
                if (!property_exists($class, $prop)) {
                    $problems[] = "dynamic property: $class::\$$prop";
                }
            }
        }
    } catch (Throwable $e) {
        $problems[] = get_class($e).': '.$e->getMessage()." ($class)";
    }
}
echo "$count classes loaded\n";
foreach (array_unique($problems) as $p) {
    echo $p, "\n";
}
