<?php
// Usage: php desktop-only-check.php <project_dir>
// Prints whether DesktopOnlyMiddleware sends a student of a "desktop only"
// school back home, for a fixed list of user agents. The middleware matches
// keywords itself (no mobiledetect here), so only PHP 8 string functions can
// change the answer.
require $argv[1].'/vendor/autoload.php';
$agents = [
    '',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15',
    'Mozilla/5.0 (X11; CrOS x86_64 14541.0.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',
    'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1',
    'Mozilla/5.0 (iPad; CPU OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
    'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36',
    'Mozilla/5.0 (Linux; Android 13; SM-X700) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Linux; Android 12; Lenovo TB-X606F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.36',
    'Mozilla/5.0 (Windows Phone 10.0; Android 6.0.1; Microsoft; Lumia 950) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0 Mobile Safari/537.36 Edge/15.15063',
    'Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; Kindle Fire HDX) AppleWebKit/537.36 (KHTML, like Gecko) Silk/3.68 like Chrome/39.0 Safari/537.36',
    'Opera/9.80 (J2ME/MIDP; Opera Mini/9.80 (S60; SymbOS; Opera Mobi/23.348; U; en) Presto/2.5.25 Version/10.54',
];
$app = require $argv[1].'/bootstrap/app.php';
$middleware = new \App\Http\Middleware\DesktopOnlyMiddleware();
$isMobilePhone = new ReflectionMethod($middleware, 'isMobilePhone');
$isMobilePhone->setAccessible(true);
foreach ($agents as $ua) {
    printf("%s  %s\n", $isMobilePhone->invoke($middleware, $ua) ? 'BLOCKED' : 'allowed', substr($ua, 0, 70));
}
