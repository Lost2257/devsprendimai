<?php

namespace App\Http\Middleware;

use App\Support\PrivacyRetention;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintainPrivacyRetention
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (config('app.env') === 'production' && $request->isMethod('GET')) {
            defer(function (): void {
                try {
                    app(PrivacyRetention::class)->runWhenDue();
                } catch (\Throwable $exception) {
                    // Retention failures must be visible to the operator, not visitors.
                    report($exception);
                }
            });
        }

        return $response;
    }
}
