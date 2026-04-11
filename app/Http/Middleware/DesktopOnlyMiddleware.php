<?php

namespace App\Http\Middleware;

use Closure;

class DesktopOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!settingCache('exam_desktop_only')) {
            return $next($request);
        }

        $userAgent = $request->header('User-Agent', '');

        if ($this->isMobilePhone($userAgent) && in_array(strtolower(request()->user()->school->country), config('app.secure_exam_countries', []))) {
            return redirect()->route('student.home', ['desktop_only' => 1]);
        }

        return $next($request);
    }

    protected function isMobilePhone($userAgent)
    {
        // Allow tablets and iPads
        $tabletKeywords = ['iPad', 'Tablet', 'PlayBook', 'Silk', 'Kindle'];
        foreach ($tabletKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return false;
            }
        }

        $mobileKeywords = [
            'Mobile', 'iPhone', 'iPod',
            'webOS', 'BlackBerry', 'Opera Mini', 'Opera Mobi',
            'IEMobile', 'Windows Phone', 'BB10',
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
