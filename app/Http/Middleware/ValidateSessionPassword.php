<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Scoped equivalent of Laravel's AuthenticateSession: stores the password hash
 * in the session and signs the session out as soon as the stored hash stops
 * matching the account. That is what makes "changing my password logs out my
 * other devices" actually hold.
 *
 * It is applied only to the manager, school and inspection groups so the
 * student guard keeps its current behaviour.
 */
class ValidateSessionPassword
{
    public function handle(Request $request, Closure $next, $guard = 'manager')
    {
        $user = Auth::guard($guard)->user();

        if (!$user || !$request->hasSession()) {
            return $next($request);
        }

        $key = 'password_hash_' . $guard;
        $ownerKey = $key . '_id';
        $current = $user->getAuthPassword();

        // A stored hash only means anything for the account it was stored for.
        // Signing in as somebody else on the same guard - a normal second login
        // or an administrator impersonating a school - starts a fresh record
        // instead of being treated as a changed password.
        $sameOwner = $request->session()->has($key)
            && (string) $request->session()->get($ownerKey) === (string) $user->getKey();

        if (!$sameOwner) {
            $request->session()->put($key, $current);
            $request->session()->put($ownerKey, $user->getKey());

            return $next($request);
        }

        if (hash_equals((string) $request->session()->get($key), (string) $current)) {
            return $next($request);
        }

        // The password changed somewhere else: drop this session.
        Auth::guard($guard)->logout();
        $request->session()->forget([$key, $ownerKey]);
        $request->session()->invalidate();

        $message = t('Your password was changed, please sign in again.');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect' => $this->loginUrl($guard),
            ], 401);
        }

        return redirect()->to($this->loginUrl($guard))
            ->with('message', $message)
            ->with('m-class', 'warning');
    }

    private function loginUrl($guard)
    {
        $prefixes = ['manager' => '/manager/login', 'school' => '/school/login', 'inspection' => '/inspection/login'];

        return $prefixes[$guard] ?? '/';
    }
}
