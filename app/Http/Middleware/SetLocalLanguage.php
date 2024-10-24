<?php

namespace App\Http\Middleware;

use Closure;
use App;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;

class SetLocalLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\request()->is('manager/*') && Auth::guard('manager')->check() ){
            $locale = isAPI()? request()->header('Accept-Language') : Auth::guard('manager')->user()->lang ;
        }else if(\request()->is('school/*') && Auth::guard('school')->check() ){
            $locale = isAPI()? request()->header('Accept-Language') : Auth::guard('school')->user()->lang ;
        }else if(\request()->is('inspection/*') && Auth::guard('inspection')->check() ){
            $locale = isAPI()? request()->header('Accept-Language') : Auth::guard('inspection')->user()->lang ;
        }else{
            $locale = isAPI()? request()->header('Accept-Language') : (session('lang') ?  session('lang'): 'ar') ;
        }
        if(!$locale || !in_array($locale,['ar','en'])) $locale = 'ar';
        app()->setLocale($locale);
        return $next($request);
    }
}
