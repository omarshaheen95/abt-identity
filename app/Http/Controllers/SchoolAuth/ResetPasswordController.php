<?php

namespace App\Http\Controllers\SchoolAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/school/home';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('school.guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('school.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('schools');
    }


    /**
     * Password reset has to satisfy the same policy as an in app change.
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => \App\Support\PasswordPolicy::rules(),
        ];
    }

    protected function validationErrorMessages()
    {
        return \App\Support\PasswordPolicy::messages();
    }

    /**
     * A successful reset also clears any pending forced change.
     */
    protected function resetPassword($user, $password)
    {
        // the broker only deletes the token after this callback returns, so a
        // rejection here leaves the reset link usable for another attempt
        if (method_exists($user, 'passwordWasUsedBefore') && $user->passwordWasUsedBefore($password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => t('You have used this password recently, please choose a different one. The last :count passwords are remembered.', ['count' => (int) config('password_policy.history', 5)]),
            ]);
        }

        $user->forceFill([
            'password' => bcrypt($password),
            'force_password_change' => 0,
            'password_changed_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        $this->guard()->login($user);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('school');
    }
}
