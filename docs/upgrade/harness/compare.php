<?php
// php compare.php <dirA> <dirB> [--show=N]  — diffs the snapshots after removing known, harmless noise.
[$self, $a, $b] = $argv;
$show = 0;
foreach ($argv as $arg) {
    if (preg_match('/^--show=(\d+)$/', $arg, $m)) {
        $show = (int) $m[1];
    }
}

function clean($s)
{
    $s = str_replace('charset=UTF-8', 'charset=utf-8', $s);
    $s = str_replace(' autocomplete="off">', '>', $s);
    $s = preg_replace('/\?(v=?|time=)\d{6,}/', '?v=X', $s);
    // CSRF tokens printed into page scripts
    $s = preg_replace("/(_token:\s*'|var CSRF\s*=\s*')[A-Za-z0-9]{40}'/", '$1X\'', $s);
    // activitylog 4 columns (always null here) in the raw activity_log rows
    $s = preg_replace('/^\s*"(event|batch_uuid)": null,\r?\n/m', '', $s);
    $s = preg_replace('/^EXCEPTION Illuminate\\\\Validation\\\\ValidationException: .*$/m', 'EXCEPTION ValidationException', $s);
    // datatables 13 adds this key to every response
    $s = preg_replace('/\],\s*"disableOrdering": false\s*\}\s*$/', "]\n}", $s);
    // per-request random values
    $s = preg_replace('/pw_password_[0-9a-f]+/', 'pw_password_X', $s);
    $s = preg_replace("/(['\"]_token['\"]\s*:\s*['\"])[^'\"]+/", '$1X', $s);
    $s = preg_replace("/(\['_token'\]\s*=\s*')[^']+/", '$1X', $s);
    // bcrypt hashes have a random salt
    $s = preg_replace('/\$2y\$\d\d\$[.\/A-Za-z0-9]{53}/', 'HASH', $s);
    $s = preg_replace('/"eyJpdiI6[A-Za-z0-9+\/=]+"/', '"ENCRYPTED"', $s);
    // relative times and durations measured during the run
    $s = preg_replace('/منذ [^<)]*/u', 'منذ T', $s);
    $s = preg_replace('/\b\d+(\.\d+)?(ms|s)\b/', 'Ns', $s);
    $s = preg_replace('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', 'DATETIME', $s);
    // export sheet titles carry the time of the export ("2026-10-22 - At 6 PM - 55 Min")
    $s = preg_replace('/\d{4}-\d{2}-\d{2} - At \d+ [AP]M - \d+ Min/', 'EXPORT TIME', $s);
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
    // Laravel 6 Blade has no @once and printed the words; Laravel 13 honours it
    $s = preg_replace('/^\s*@(once|endonce)\s*$/m', '', $s);
    // whitespace-only differences (Blade compiles some directives with other spacing)
    $lines = array_map('trim', explode("\n", $s));
    $lines = array_values(array_filter($lines, fn ($l) => $l !== ''));
    $s = implode("\n", $lines);
    return $s;
}

$same = 0;
$diffs = [];
foreach (glob("$a/*.txt") as $fa) {
    $name = basename($fa);
    $fb = "$b/$name";
    if (!is_file($fb)) {
        $diffs[$name] = ['missing in B'];
        continue;
    }
    $la = explode("\n", clean(file_get_contents($fa)));
    $lb = explode("\n", clean(file_get_contents($fb)));
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

echo "identical: $same, different: " . count($diffs) . "\n\n";
$i = 0;
foreach ($diffs as $name => $out) {
    echo "== $name\n";
    if ($i++ < $show) {
        echo implode("\n", $out), "\n";
    }
}
