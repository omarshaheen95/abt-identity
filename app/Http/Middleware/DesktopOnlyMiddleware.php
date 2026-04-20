<?php

namespace App\Http\Middleware;

use Closure;

class DesktopOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        $school = optional(request()->user())->school;
        if (!$school || !$school->proctoringFlag('desktop_only')) {
            return $next($request);
        }

        $userAgent = $request->header('User-Agent', '');

        if ($this->isMobilePhone($userAgent)) {
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
