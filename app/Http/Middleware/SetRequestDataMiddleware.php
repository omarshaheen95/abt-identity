<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetRequestDataMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        if (request()->is('manager/*') && Auth::guard('manager')->check()) {
            $request['guard'] = 'manager';
        } else if (request()->is('school/*') && Auth::guard('school')->check()) {
            $school = \Auth::guard('school')->user();
            $request->merge(['school_id' => $school->id]);
            $request['guard'] = 'school';
        } else if (request()->is('student/*') && Auth::guard('student')->check()) {
            $request['guard'] = 'student';
        } else if (request()->is('inspection/*') && Auth::guard('inspection')->check()) {
            $request['guard'] = 'inspection';
        }

        return $next($request);
    }
}
