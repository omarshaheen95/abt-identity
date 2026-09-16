<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Traits;

use App\Http\Middleware\ForcePasswordChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Shared "change my own password" flow for the admin, delegate and khda
 * portals. Keeps the three controllers in sync: same policy, same clearing of
 * the forced change flag, same session handling.
 */
trait UpdatesPassword
{
    /**
     * Apply a validated new password to the currently authenticated user of
     * the given guard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function applyPasswordUpdate(Request $request, $guard)
    {
        $user = Auth::guard($guard)->user();

        if (!Hash::check($request->get('old_password'), $user->password)) {
            return redirect()->back()
                ->withErrors(['old_password' => t('Current Password Invalid')])
                ->with('message', t('Current Password Invalid'))
                ->with('m-class', 'error');
        }

        $wasForced = (bool) ($user->force_password_change ?? false);

        $user->update([
            'password' => bcrypt($request->get('password')),
            'force_password_change' => 0,
            'password_changed_at' => now(),
        ]);

        // Keep this session alive and let ValidateSessionPassword drop the
        // other devices, whose stored hash no longer matches.
        $request->session()->put('password_hash_' . $guard, $user->getAuthPassword());
        $request->session()->put('password_hash_' . $guard . '_id', $user->getKey());
        $request->session()->forget(ForcePasswordChange::IMPERSONATION_KEY);

        if ($wasForced) {
            return redirect()->route($guard . '.home')
                ->with('message', t('Your password has been updated, you can continue now.'))
                ->with('m-class', 'success');
        }

        return redirect()->back()
            ->with('message', t('Successfully Updated'))
            ->with('m-class', 'success');
    }

    /**
     * Whether the current user of the guard is locked out until the password
     * is changed, either by the flag or because the password aged out. Used by
     * the views to show the blocking banner.
     */
    protected function passwordChangeIsForced($guard)
    {
        return $this->passwordChangeReason($guard) !== null;
    }

    /**
     * Why the account is being held, or null when it is free to browse:
     * 'forced' for the administrator raised flag, 'expired' for age.
     */
    protected function passwordChangeReason($guard)
    {
        $user = Auth::guard($guard)->user();

        if (!$user || ForcePasswordChange::isImpersonating($guard, $user->getKey())) {
            return null;
        }

        if ((bool) ($user->force_password_change ?? false)) {
            return 'forced';
        }

        if (method_exists($user, 'passwordHasExpired') && $user->passwordHasExpired()) {
            return 'expired';
        }

        return null;
    }
}
