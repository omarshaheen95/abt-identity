<?php
// php compare.php <dirA> <dirB> [--show=N] [--today=YYYY-MM-DD]
// Diffs the snapshots after removing known, harmless noise.
[$self, $a, $b] = $argv;
$show = 0;
$today = date('Y-m-d');
foreach ($argv as $arg) {
    if (preg_match('/^--show=(\d+)$/', $arg, $m)) {
        $show = (int) $m[1];
    }
    if (preg_match('/^--today=(\d{4}-\d\d-\d\d)$/', $arg, $m)) {
        $today = $m[1];
    }
}

function clean($s, $today)
{
    $s = str_replace('charset=UTF-8', 'charset=utf-8', $s);
    // @csrf of Laravel 13 adds autocomplete="off" to the hidden token field
    $s = preg_replace('/(name="_token" value="[^"]*") autocomplete="off"/', '$1', $s);
    $s = preg_replace('/\?(v=?|time=)\d{6,}/', '?v=X', $s);
    // CSRF tokens printed into page scripts
    $s = preg_replace("/(_token:\s*'|var CSRF\s*=\s*')[A-Za-z0-9]{40}'/", '$1X\'', $s);
    // activitylog 4 columns (null for the rows written before) in the raw activity_log rows
    $s = preg_replace('/^\s*"(event|batch_uuid)": null,\r?\n/m', '', $s);
    $s = preg_replace('/^EXCEPTION Illuminate\\\\Validation\\\\ValidationException: .*$/m', 'EXCEPTION ValidationException', $s);
    // the same inside the auth.txt lines: Laravel 13 puts the first error in
    // the exception message instead of "The given data was invalid."
    $s = preg_replace('/(Illuminate\\\\Validation\\\\ValidationException): [^{\n]*/', '$1 ', $s);
    // Laravel 8 reported view errors through Ignition's wrapper
    $s = str_replace('Facade\\Ignition\\Exceptions\\ViewException', 'Illuminate\\View\\ViewException', $s);
    // datatables 13 adds this key to every response
    $s = preg_replace('/\],\s*"disableOrdering": false\s*\}\s*$/', "]\n}", $s);
    // per-request random values
    $s = preg_replace('/pw_password_[0-9a-f]+/', 'pw_password_X', $s);
    $s = preg_replace("/(['\"]_token['\"]\s*:\s*['\"])[^'\"]+/", '$1X', $s);
    $s = preg_replace("/(\['_token'\]\s*=\s*')[^']+/", '$1X', $s);
    // Str::random(10) element ids of the lesson pages
    $s = preg_replace('/\b(id|for|data-[a-z-]+)="([a-z_-]+)-[A-Za-z0-9]{10}"/', '$1="$2-RANDOM"', $s);
    $s = preg_replace('/([#\'"])([a-z_]+-)[A-Za-z0-9]{10}([\'"])/', '$1$2RANDOM$3', $s);
    // bcrypt hashes have a random salt
    $s = preg_replace('/\$2y\$\d\d\$[.\/A-Za-z0-9]{53}/', 'HASH', $s);
    $s = preg_replace('/"eyJpdiI6[A-Za-z0-9+\/=]+"/', '"ENCRYPTED"', $s);
    // rows written during the run itself (last_login, updated_at): only the time of the run
    $s = preg_replace('/' . preg_quote($today, '/') . '[T ]\d\d:\d\d(:\d\d(\.\d+)?Z?)?/', 'TODAY', $s);
    // jsvalidation's "after:today" is strtotime('today'), outside Carbon's test clock
    $s = preg_replace('/\["After",\[\d{9,}\]/', '["After",[TODAY]', $s);
    $s = preg_replace('/\b\d\d:\d\d:\d\d GMT\b/', 'HH:MM:SS GMT', $s);
    // relative times and durations measured during the run
    $s = preg_replace('/منذ [^<)]*/u', 'منذ T', $s);
    $s = preg_replace('/\b\d+ (seconds?|minutes?) (ago|from now)/', 'N units ago', $s);
    $s = preg_replace('/\b\d+(\.\d+)?(ms|s)\b/', 'Ns', $s);
    // view files checked out with CRLF in one copy and LF in the other
    $s = str_replace('\r\n', '\n', $s);
    // QR codes of encrypted (random IV) links
    $s = preg_replace('/<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" version="1\.1".*?<\/svg>/s', 'QRSVG', $s);
    // framework error page: keep only the status and exception lines
    if (preg_match('/^EXCEPTION \S/m', $s) && preg_match('/^STATUS 5\d\d/m', $s)) {
        $s = preg_replace('/\n\n.*/s', '', $s);
        $s = preg_replace('/^EXCEPTION ([^:]+:).*/m', 'EXCEPTION $1', $s);
    }
    $s = preg_replace('/[ \t]{2,}/', ' ', $s);
    // whitespace-only differences (Blade compiles some directives with other spacing)
    $lines = array_map('trim', explode("\n", $s));
    $lines = array_values(array_filter($lines, function ($l) { return $l !== ''; }));
    return implode("\n", $lines);
}

$same = 0;
$diffs = [];
foreach (glob("$a/*.{txt,html}", GLOB_BRACE) as $fa) {
    $name = basename($fa);
    if ($name[0] === '_') {
        continue;
    }
    $fb = "$b/$name";
    if (!is_file($fb)) {
        $diffs[$name] = ['missing in B'];
        continue;
    }
    $la = explode("\n", clean(file_get_contents($fa), $today));
    $lb = explode("\n", clean(file_get_contents($fb), $today));
    if ($la === $lb) {
        $same++;
        continue;
    }
    // line-based comparison is enough for these pages: report removed / added lines
    $removed = array_diff($la, $lb);
    $added = array_diff($lb, $la);
    $out = [];
    foreach (array_slice($removed, 0, 6, true) as $i => $l) {
        $out[] = "- [$i] " . mb_substr(trim($l), 0, 220);
    }
    foreach (array_slice($added, 0, 6, true) as $i => $l) {
        $out[] = "+ [$i] " . mb_substr(trim($l), 0, 220);
    }
    if (!$removed && !$added) {
        $out[] = '(same lines, different order or count)';
    }
    $diffs[$name] = $out;
}
foreach (glob("$b/*.{txt,html}", GLOB_BRACE) as $fb) {
    $name = basename($fb);
    if ($name[0] !== '_' && !is_file("$a/$name")) {
        $diffs[$name] = ['missing in A'];
    }
}

echo "identical: $same, different: " . count($diffs) . "\n\n";
$i = 0;
foreach ($diffs as $name => $out) {
    echo "== $name\n";
    if ($i++ < $show) {
        echo implode("\n", $out), "\n";
    }
}
