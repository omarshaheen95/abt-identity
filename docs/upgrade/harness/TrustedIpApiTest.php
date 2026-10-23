<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * api/update_settings and api/import_students have no login: only callers from
 * config('app.trusted_ips') may use them. The caller is the TCP peer
 * (REMOTE_ADDR, through TrustProxies), never an address the client writes into
 * a forwarding header itself.
 */
class TrustedIpApiTest extends TestCase
{
    use DatabaseTransactions;

    private const UNTRUSTED = '203.0.113.7';

    /** forwarding headers a web server hands to PHP as $_SERVER entries */
    private const SPOOFABLE = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED'];

    protected function tearDown(): void
    {
        foreach (self::SPOOFABLE as $key) {
            unset($_SERVER[$key]);
        }
        parent::tearDown();
    }

    public static function spoofedHeaders()
    {
        return array_combine(self::SPOOFABLE, array_map(function ($key) { return [$key]; }, self::SPOOFABLE));
    }

    #[DataProvider('spoofedHeaders')]
    public function test_update_settings_refuses_a_forged_forwarding_header($serverKey)
    {
        $setting = Setting::query()->orderBy('id')->firstOrFail();

        $response = $this->spoofed($serverKey, 'post', '/api/update_settings', [
            'settings' => [$setting->key => 'changed-by-an-attacker'],
        ]);

        $response->assertStatus(401);
        $this->assertSame($setting->value, Setting::query()->find($setting->id)->value);
    }

    #[DataProvider('spoofedHeaders')]
    public function test_import_students_refuses_a_forged_forwarding_header($serverKey)
    {
        $files = DB::table('import_student_files')->count();

        $response = $this->spoofed($serverKey, 'post', '/api/import_students', [
            'school_email' => DB::table('schools')->orderBy('id')->value('email'),
            'year' => DB::table('years')->orderBy('id')->value('slug'),
            'process_type' => 'create',
        ]);

        $response->assertStatus(401);
        $this->assertSame($files, DB::table('import_student_files')->count());
    }

    public function test_a_trusted_server_can_still_update_settings()
    {
        $setting = Setting::query()->orderBy('id')->firstOrFail();
        $trusted = config('app.trusted_ips')[1];

        $response = $this->withServerVariables(['REMOTE_ADDR' => $trusted])
            ->postJson('/api/update_settings', ['settings' => [$setting->key => 'synced-value']]);

        $response->assertOk()->assertJsonPath('data.updated', [$setting->key]);
        $this->assertSame('synced-value', Setting::query()->find($setting->id)->value);
    }

    public function test_an_untrusted_server_is_refused_without_any_header()
    {
        $response = $this->withServerVariables(['REMOTE_ADDR' => self::UNTRUSTED])
            ->postJson('/api/update_settings', ['settings' => ['logo' => 'x']]);

        $response->assertStatus(401);
    }

    /**
     * A request from an untrusted address that claims, in a forwarding header,
     * to come from a trusted one: on the request, and in $_SERVER as PHP-FPM
     * would put it.
     */
    private function spoofed($serverKey, $method, $uri, array $data)
    {
        $claimed = config('app.trusted_ips')[0];
        $_SERVER[$serverKey] = $claimed;
        $header = str_replace('_', '-', substr($serverKey, 5));

        return $this->withServerVariables(['REMOTE_ADDR' => self::UNTRUSTED, $serverKey => $claimed])
            ->withHeaders([$header => $claimed])
            ->json(strtoupper($method), $uri, $data);
    }
}
