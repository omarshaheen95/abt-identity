<?php

namespace App\Services;

use App\Models\LoginSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Single write path for every authentication event (success / failure / lockout / logout).
 *
 * Never call LoginSession::create() directly from a listener or a controller:
 * this class is responsible for stripping credentials and truncating payloads.
 */
class LoginActivityService
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_LOCKOUT = 'lockout';
    public const STATUS_LOGOUT = 'logout';

    public const REASON_INVALID_CREDENTIALS = 'invalid_credentials';
    public const REASON_USER_NOT_FOUND = 'user_not_found';
    public const REASON_THROTTLED = 'throttled';

    /**
     * Keys that must never reach the database.
     */
    protected const SECRET_KEYS = ['password', 'password_confirmation', 'new_password', 'remember', 'token', '_token'];

    /**
     * @param  string  $status  one of the STATUS_* constants
     * @param  string|null  $guard  manager|school|student|inspection|web
     * @param  mixed  $user  the authenticatable model, or null when unknown
     * @param  array  $extra  identifier|reason
     */
    public static function record(string $status, ?string $guard = null, $user = null, array $extra = []): void
    {
        try {
            $request = request();
            $ip = $request ? $request->ip() : null;
            $browserInfo = Str::limit(self::stringOrNull($request ? $request->get('browserInfo') : null), 2000, '');

            LoginSession::query()->create([
                'model_type' => $user ? get_class($user) : null,
                'model_id' => $user ? $user->getKey() : null,
                'status' => $status,
                'guard' => $guard,
                'identifier' => Str::limit(self::stringOrNull(Arr::get($extra, 'identifier')), 190, ''),
                'reason' => Arr::get($extra, 'reason'),
                'ip' => $ip,
                'user_agent' => Str::limit(self::stringOrNull($request ? $request->userAgent() : null), 500, ''),
                // Kept for backward compatibility with the rows written before this table
                // was turned into a full auth log.
                'data' => 'IP : '.$ip.'-'.$browserInfo,
            ]);
        } catch (Throwable $e) {
            // Logging must never break an authentication attempt.
            Log::error('Failed to record login activity', [
                'status' => $status,
                'guard' => $guard,
            ]);
        }
    }

    /**
     * Remove plain-text passwords and tokens from an Auth\Events\Failed payload.
     */
    public static function sanitize(array $credentials): array
    {
        return Arr::except($credentials, self::SECRET_KEYS);
    }

    /**
     * Pull the login identifier (email / username) out of sanitized credentials.
     */
    public static function identifierFrom(array $credentials): ?string
    {
        $credentials = self::sanitize($credentials);

        foreach (['email', 'username', 'name', 'phone'] as $key) {
            if (! empty($credentials[$key]) && is_string($credentials[$key])) {
                return $credentials[$key];
            }
        }

        $first = Arr::first($credentials, function ($value) {
            return is_string($value) && $value !== '';
        });

        return $first ?: null;
    }

    /**
     * Resolve the guard name for an already authenticated model, used by the
     * Login event which does not always carry a reliable guard name.
     */
    public static function guardFor($user, ?string $fallback = null): ?string
    {
        if (! $user) {
            return $fallback;
        }

        foreach (['manager', 'school', 'student', 'inspection'] as $guard) {
            $provider = config('auth.guards.'.$guard.'.provider');
            if ($provider && config('auth.providers.'.$provider.'.model') === get_class($user)) {
                return $guard;
            }
        }

        return $fallback;
    }

    protected static function stringOrNull($value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
