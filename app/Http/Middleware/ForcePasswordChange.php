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
 * Locks an account out of every page except the change password screen while
 * its force_password_change flag is raised.
 *
 * Applied per guard in RouteServiceProvider, e.g. 'force.password:admin'.
 * Logout lives in routes/web.php outside of the guarded groups, so a user can
 * always sign out instead of changing the password.
 */
class ForcePasswordChange
{
    /**
     * Route name of the change password screen for each guard.
     */
    private const PASSWORD_ROUTES = [
        'manager' => ['manager.edit-password', 'manager.update-password'],
        'school' => ['school.edit-password', 'school.update-password'],
        'inspection' => ['inspection.edit-password', 'inspection.update-password'],
    ];

    /**
     * Routes that stay reachable while locked, on top of the password screen.
     */
    private const ALWAYS_ALLOWED = [
        'manager.switch-language', 'school.switch-language', 'inspection.switch-language',
    ];

    /**
     * Session key set while an administrator is impersonating another account.
     * Holds "<guard>:<id>" so that signing in as somebody else afterwards does
     * not inherit the bypass.
     */
    public const IMPERSONATION_KEY = 'impersonating_guard';

    /**
     * Value to store in the session when impersonating.
     */
    public static function impersonationToken($guard, $id)
    {
        return $guard . ':' . $id;
    }

    /**
     * Mark the current session as an administrator acting as somebody else, so
     * a pending forced password change does not block the administrative work.
     * Call this straight after loginUsingId() on an impersonation route.
     */
    public static function impersonate($guard, $id)
    {
        session([self::IMPERSONATION_KEY => self::impersonationToken($guard, $id)]);
    }

    /**
     * Drop the bypass. Called when somebody signs in for real, so that the
     * account owner is never let through on the strength of an earlier
     * impersonation in the same browser session.
     */
    public static function stopImpersonating()
    {
        session()->forget(self::IMPERSONATION_KEY);
    }

    /**
     * Whether the session is impersonating this exact account.
     */
    public static function isImpersonating($guard, $id)
    {
        return session(self::IMPERSONATION_KEY) === self::impersonationToken($guard, $id);
    }

    public function handle(Request $request, Closure $next, $guard = 'manager')
    {
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return $next($request);
        }

        $expired = method_exists($user, 'passwordHasExpired') && $user->passwordHasExpired();

        if (!$this->isLocked($user) && !$expired) {
            $this->shareExpiryWarning($user);

            return $next($request);
        }

        // An administrator who logged in as this account should not be pushed
        // into changing somebody else's password.
        if (self::isImpersonating($guard, $user->getKey())) {
            return $next($request);
        }

        $message = $expired && !$this->isLocked($user)
            ? t('Your password has expired, please choose a new one to continue.')
            : t('You must change your password before you can continue.');

        if ($this->isAllowedRoute($request, $guard)) {
            return $next($request);
        }


        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect' => $this->passwordUrl($guard),
            ], 403);
        }

        return redirect()->to($this->passwordUrl($guard))
            ->with('message', $message)
            ->with('m-class', 'warning');
    }

    /**
     * Hand the layouts a short notice when expiry is approaching, so the user
     * is warned before being stopped rather than after.
     */
    private function shareExpiryWarning($user)
    {
        if (!method_exists($user, 'passwordExpiryIsNear') || !$user->passwordExpiryIsNear()) {
            return;
        }

        $days = $user->passwordExpiresInDays();

        \Illuminate\Support\Facades\View::share('password_expiry_notice', $days > 0
            ? t('Your password expires in :days day(s). Change it from Update Password.', ['days' => $days])
            : t('Your password expires today. Change it from Update Password.'));
    }

    /**
     * The column is only present once the migration has run, so read it
     * defensively to keep older deployments working.
     */
    private function isLocked($user)
    {
        return (bool) ($user->force_password_change ?? false);
    }

    private function isAllowedRoute(Request $request, $guard)
    {
        $name = $request->route() ? $request->route()->getName() : null;

        if (!$name) {
            return false;
        }

        $allowed = array_merge(self::PASSWORD_ROUTES[$guard] ?? [], self::ALWAYS_ALLOWED);

        return in_array($name, $allowed, true);
    }

    private function passwordUrl($guard)
    {
        $route = self::PASSWORD_ROUTES[$guard][0] ?? null;

        return $route ? route($route) : url('/');
    }
}
