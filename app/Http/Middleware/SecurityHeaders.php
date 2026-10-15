<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        // Deliberately scoped: keep Vite, Inertia, WebGL and consented GA functional.
        // This is defence-in-depth, not a claim of comprehensive XSS protection.
        $response->headers->set('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'");
        $response->headers->remove('X-Powered-By');

        if (config('app.env') === 'production' && $request->isSecure()) {
            // Do not opt other subdomains into HSTS without auditing them first.
            $response->headers->set('Strict-Transport-Security', 'max-age=15552000');
        }

        if ($request->user() || !$request->isMethod('GET') || $request->is(
            'dashboard*', 'login', 'register', 'forgot-password', 'reset-password*',
            'verify-email*', 'confirm-password', 'profile*',
        )) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
