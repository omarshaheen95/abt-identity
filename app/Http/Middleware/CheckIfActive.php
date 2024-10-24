<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfActive
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('manager/home') && !\Auth::guard('manager')->user()->approved) {
            \Session::put('not_active', 'not_active');
        } elseif ($request->is('school/home') && !\Auth::guard('school')->user()->active) {
            \Session::put('not_active', 'not_active');
        }else{
            \Session::forget('not_active');
        }

        if ($request->is('manager/*') &&
            !$request->is('manager/home') &&
            !$request->is('manager/logout') &&
            !$request->is('manager/lang/*') &&
            !\Auth::guard('manager')->user()->approved) {
            return redirect('manager/home')->with('not_active','not_active');

        } elseif ($request->is('school/*') &&
            !$request->is('school/home') &&
            !$request->is('school/lang/*') &&
            !$request->is('school/logout') && !\Auth::guard('school')->user()->active) {
            return redirect('school/home')->with('not_active','not_active');
        }

        return $next($request);
    }
}
