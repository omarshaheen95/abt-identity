<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): saves the normalized body of every
 * parameterless GET page, as a page and as a datatables ajax call, per guard
 * (and for a guest), into SNAPSHOT_DIR so two versions of the app can be diffed.
 */
class ResponseSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    public function test_snapshots()
    {
        $dir = getenv('SNAPSHOT_DIR');
        $this->assertNotEmpty($dir);
        @mkdir($dir, 0777, true);

        $users = [
            'guest' => null,
            'manager' => SmokeRoutesTest::manager(),
            'school' => SmokeRoutesTest::school(),
            'inspection' => SmokeRoutesTest::inspection(),
            'student' => SmokeRoutesTest::student(),
        ];

        $count = 0;
        foreach ($users as $guard => $user) {
            foreach ($this->pages($guard) as $uri) {
                foreach (['page', 'ajax'] as $mode) {
                    if ($user) {
                        $this->actingAs($user, $guard);
                    }
                    if ($mode === 'ajax') {
                        $response = $this->get('/' . $uri . '?draw=1&start=0&length=10', [
                            'X-Requested-With' => 'XMLHttpRequest',
                            'Accept' => 'application/json, text/javascript, */*; q=0.01',
                        ]);
                    } else {
                        $response = $this->get('/' . $uri);
                    }
                    $body = $this->normalize((string) $response->getContent(), $response->headers->get('Content-Type'));
                    $location = $response->headers->get('Location');
                    try {
                        $e = $response->exception;
                    } catch (\Throwable $ignored) {
                        $e = null;
                    }
                    $exception = $e ? get_class($e) . ': ' . strtok($e->getMessage(), "\n") : '';
                    if ($response->baseResponse instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                        $body = 'BINARY FILE ' . $response->headers->get('Content-Disposition');
                    }
                    $name = $guard . '__' . $mode . '__' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $uri === '/' ? 'root' : $uri);
                    file_put_contents($dir . '/' . $name . '.txt',
                        "STATUS " . $response->getStatusCode() . "\nTYPE " . $response->headers->get('Content-Type') .
                        "\nLOCATION " . $location . "\nEXCEPTION " . $exception . "\n\n" . $body);
                    $this->flushSession();
                    $count++;
                }
            }
        }

        $this->assertGreaterThan(0, $count);
    }

    private function pages($guard)
    {
        $uris = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (!in_array('GET', $route->methods(), true) || strpos($uri, '{') !== false || preg_match(SmokeRoutesTest::SKIP, $uri)) {
                continue;
            }
            $auth = '#/(login|register|password/reset)$#';
            $mine = $guard === 'guest'
                ? !preg_match('#^(manager|school|inspection|student)/#', $uri) || preg_match($auth, $uri)
                : strpos($uri, $guard . '/') === 0 && !preg_match($auth, $uri);
            if ($mine) {
                $uris[] = $uri;
            }
        }
        sort($uris);

        return array_values(array_unique($uris));
    }

    private function normalize($body, $type)
    {
        if (strpos((string) $type, 'json') !== false) {
            $data = json_decode($body, true);
            if (is_array($data)) {
                unset($data['input'], $data['queries']);
                return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
        $body = preg_replace('/(name="_token"\s+value=")[^"]+/', '$1X', $body);
        $body = preg_replace('/(name="csrf-token"\s+content=")[^"]+/', '$1X', $body);
        $body = preg_replace("/('X-CSRF-TOKEN'\s*:\s*')[^']+/", '$1X', $body);
        $body = preg_replace('/("_token"\s*:\s*")[^"]+/', '$1X', $body);
        $body = preg_replace('/(_token=)[A-Za-z0-9]+/', '$1X', $body);
        $body = preg_replace('/\?id=[0-9a-f]{20,}/', '?id=X', $body);

        return preg_replace("/[ \t]+\r?\n/", "\n", $body);
    }
}
