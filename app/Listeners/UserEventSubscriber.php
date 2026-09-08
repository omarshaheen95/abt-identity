<?php

namespace App\Listeners;

use App\Services\LoginActivityService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class UserEventSubscriber
{
    /**
     * Successful authentication on any guard.
     */
    public function handleUserLogin(Login $event)
    {
        $user = $event->user;

        $browserInfo = request()->get('browserInfo', $user->last_login_info);
        $browserInfo = is_string($browserInfo) ? mb_substr($browserInfo, 0, 2000) : '';
        $user->update([
            'last_login' => Carbon::now(),
            'last_login_info' => 'IP:'.Request::ip().'-'.$browserInfo,
        ]);

        LoginActivityService::record(
            LoginActivityService::STATUS_SUCCESS,
            LoginActivityService::guardFor($user, $event->guard),
            $user,
            ['identifier' => $user->username ?? $user->email ?? null]
        );
    }

    /**
     * Failed authentication: wrong password, or an account that does not exist.
     *
     * $event->credentials carries the plain-text password, it must go through
     * LoginActivityService::sanitize() before anything is stored.
     */
    public function handleUserFailed(Failed $event)
    {
        $credentials = LoginActivityService::sanitize($event->credentials);

        LoginActivityService::record(
            LoginActivityService::STATUS_FAILED,
            LoginActivityService::guardFor($event->user, $event->guard),
            $event->user,
            [
                'identifier' => LoginActivityService::identifierFrom($credentials),
                'reason' => $event->user
                    ? LoginActivityService::REASON_INVALID_CREDENTIALS
                    : LoginActivityService::REASON_USER_NOT_FOUND,
            ]
        );
    }

    /**
     * Too many attempts, the throttle middleware locked the identifier out.
     */
    public function handleLockout(Lockout $event)
    {
        $request = $event->request;

        LoginActivityService::record(
            LoginActivityService::STATUS_LOCKOUT,
            getGuard(),
            null,
            [
                'identifier' => $request->get('email', $request->get('username')),
                'reason' => LoginActivityService::REASON_THROTTLED,
            ]
        );
    }

    public function handleUserLogout(Logout $event)
    {
        if (! $event->user) {
            return;
        }

        LoginActivityService::record(
            LoginActivityService::STATUS_LOGOUT,
            LoginActivityService::guardFor($event->user, $event->guard),
            $event->user,
            ['identifier' => $event->user->username ?? $event->user->email ?? null]
        );
    }

    public function subscribe($events)
    {
        $events->listen(Login::class, [UserEventSubscriber::class, 'handleUserLogin']);
        $events->listen(Failed::class, [UserEventSubscriber::class, 'handleUserFailed']);
        $events->listen(Lockout::class, [UserEventSubscriber::class, 'handleLockout']);
        $events->listen(Logout::class, [UserEventSubscriber::class, 'handleUserLogout']);
    }
}
