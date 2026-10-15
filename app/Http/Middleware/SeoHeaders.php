<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('seo.indexable') || $response->getStatusCode() >= 400 || $request->is(
            'dashboard*',
            'login',
            'register',
            'forgot-password',
            'reset-password*',
            'verify-email*',
            'confirm-password',
            'profile*',
            'up',
        )) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        return $response;
    }
}
