<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Support\TrafficAttribution;
use Illuminate\Support\Str;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $userAgent = strtolower((string) $request->userAgent());
        $isCrawler = preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|linkedinbot|whatsapp|gptbot|chatgpt|claude|perplexity|bytespider|semrush|ahrefs/', $userAgent) === 1;
        $shouldTrack = $request->isMethod('GET')
            && $request->cookie('devsprendimai_analytics_consent') === 'granted'
            && !$request->user()
            && $response->isSuccessful()
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
            && strtolower((string) $request->header('Purpose')) !== 'prefetch'
            && strtolower((string) $request->header('Sec-Purpose')) !== 'prefetch';

        if (!$shouldTrack) {
            return $response;
        }

        $attribution = TrafficAttribution::from($request);
        $isEntry = (
            $attribution['medium'] !== 'direct'
            || blank($attribution['referrer_host'])
            || filled($attribution['campaign'])
        );

        $visitorFingerprint = implode('|', [
            now()->utc()->format('Y-m'),
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]);

        $visit = $shouldTrack
            ? [
                // Monthly rotation limits correlation; never store a raw IP or user agent.
                'ip' => null,
                'session_hash' => hash_hmac('sha256', $visitorFingerprint, (string) config('app.key')),
                'url' => Str::limit('/'.ltrim($request->path(), '/'), 255, ''),
                ...$attribution,
                'device' => TrafficAttribution::device($request->userAgent()),
                'browser' => TrafficAttribution::browser($request->userAgent()),
                'is_entry' => $isEntry,
                'user_agent' => null,
            ]
            : null;

        if ($visit) {
            defer(fn () => Visit::create($visit));
        }

        return $response;
    }
}
