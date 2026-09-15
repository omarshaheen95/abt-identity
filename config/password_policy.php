<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    |
    | Turns password expiry on or off for every guard at once, without having
    | to clear the per guard durations below. Useful to stage a rollout, or to
    | stop the clock immediately if expiry starts causing support load.
    |
    | This only controls ageing. The forced change flag an administrator raises
    | keeps working either way, and so does the password history, which is
    | switched off on its own with PASSWORD_HISTORY=0.
    |
    */

    'expiry_enabled' => env('PASSWORD_EXPIRY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Password lifetime
    |--------------------------------------------------------------------------
    |
    | How many days a password stays valid, per guard. Once the age passes the
    | limit the account is held on the change password screen exactly as if the
    | forced change flag had been raised.
    |
    | Set a guard to null to switch expiry off for that guard alone, or use
    | the master switch above to stop it everywhere.
    |
    | The defaults are staggered on purpose: administrators carry the widest
    | privileges, schools are seasonal users who sign in around assessment
    | rounds and are the most expensive to support.
    |
    */

    'expiry_days' => [
        'manager' => env('PASSWORD_EXPIRY_MANAGER', 180),
        'inspection' => env('PASSWORD_EXPIRY_INSPECTION', 180),
        'school' => env('PASSWORD_EXPIRY_SCHOOL', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Advance warning
    |--------------------------------------------------------------------------
    |
    | Days before expiry to start showing the reminder banner, so nobody meets
    | a hard wall in the middle of their work. Set to 0 to disable the banner.
    |
    */

    'warn_days' => env('PASSWORD_EXPIRY_WARN_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Password history
    |--------------------------------------------------------------------------
    |
    | How many previous passwords are remembered and refused on the way back.
    | Expiry without this is trivially defeated by setting the same password
    | again, so keep it at least at 3. Set to 0 to disable.
    |
    */

    'history' => env('PASSWORD_HISTORY', 3),

];
