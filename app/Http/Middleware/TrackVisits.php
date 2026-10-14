<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Support\TrafficAttribution;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $userAgent = strtolower((string) $request->userAgent());
        $isCrawler = preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|linkedinbot|whatsapp|gptbot|chatgpt|claude|perplexity|bytespider|semrush|ahrefs/', $userAgent) === 1;
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

        $attribution = $shouldTrack ? TrafficAttribution::from($request) : null;
        $isEntry = $shouldTrack && (
            $attribution['medium'] !== 'direct'
            || blank($attribution['referrer_host'])
            || filled($attribution['campaign'])
        );

        $visitorFingerprint = implode('|', [
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]);

        $visit = $shouldTrack
            ? [
                // A one-way identifier enables unique-visitor counts without storing a raw IP.
                'ip' => null,
                'session_hash' => hash_hmac('sha256', $visitorFingerprint, (string) config('app.key')),
                'url' => '/'.ltrim($request->path(), '/'),
                ...$attribution,
                'device' => TrafficAttribution::device($request->userAgent()),
                'browser' => TrafficAttribution::browser($request->userAgent()),
                'is_entry' => $isEntry,
                'user_agent' => null,
            ]
            : null;

        $response = $next($request);

        if ($visit && $response->isSuccessful()) {
            defer(fn () => Visit::create($visit));
        }

        return $response;
    }
}
