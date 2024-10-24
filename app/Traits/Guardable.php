<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Traits;


use Illuminate\Support\Facades\Auth;

trait Guardable
{
    static public function user(){
        return Auth::guard(static::activeGuard())->user() ?: null;
    }

    static public function id(){
        return static::user()->MID ?: null;
    }

    static private function activeGuard(){

        if (strpos(request()->url(), '/api/') !== false || strpos(request()->url(), '/web/') !== false) {
            foreach (array_keys(config('auth.guards')) as $guard) {

                if (auth()->guard('sanctum:'.$guard)->check()) return $guard;

            }
        }
        foreach(array_keys(config('auth.guards')) as $guard){

            if(auth()->guard($guard)->check()) return $guard;

        }
        return null;
    }
}
