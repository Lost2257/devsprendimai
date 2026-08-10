<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $userAgent = strtolower((string) $request->userAgent());
        $isCrawler = preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|linkedinbot|whatsapp/', $userAgent) === 1;
        $shouldTrack = $request->isMethod('GET')
            && !$request->is(
                'dashboard*',
                'login',
                'register',
                'forgot-password',
                'reset-password*',
                'verify-email',
                'confirm-password',
                'profile*',
                'sitemap.xml',
                'robots.txt',
                'up',
            )
            && !$isCrawler
            && $request->header('Purpose') !== 'prefetch';

        $visit = $shouldTrack
            ? [
                'ip' => $request->ip(),
                'url' => $request->path(),
                'user_agent' => $request->userAgent(),
            ]
            : null;

        $response = $next($request);

        if ($visit) {
            defer(fn () => Visit::create($visit));
        }

        return $response;
    }
}
