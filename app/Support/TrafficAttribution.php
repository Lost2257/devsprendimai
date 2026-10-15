<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrafficAttribution
{
    public static function from(Request $request): array
    {
        $referrer = trim((string) $request->headers->get('referer'));
        $scheme = strtolower((string) parse_url($referrer, PHP_URL_SCHEME));
        $referrerHost = in_array($scheme, ['http', 'https'], true)
            ? strtolower((string) parse_url($referrer, PHP_URL_HOST)) : '';
        $referrerHost = Str::limit($referrerHost, 255, '');
        $siteHost = strtolower($request->getHost());
        $utmSource = self::queryValue($request, 'utm_source') ?? '';
        $utmMedium = self::queryValue($request, 'utm_medium') ?? '';

        if ($utmSource !== '') {
            $source = Str::limit($utmSource, 100, '');
            $medium = Str::limit($utmMedium ?: 'campaign', 100, '');
        } elseif ($referrerHost === '' || $referrerHost === $siteHost || str_ends_with($referrerHost, '.'.$siteHost)) {
            $source = 'Tiesiogiai';
            $medium = 'direct';
        } else {
            [$source, $medium] = self::sourceFromHost($referrerHost);
        }

        $referrerOrigin = $referrerHost !== ''
            ? $scheme.'://'.$referrerHost
            : null;

        return [
            'referrer' => $referrerOrigin,
            'referrer_host' => $referrerHost !== '' ? Str::limit($referrerHost, 255, '') : null,
            'source' => Str::limit($source, 100, ''),
            'medium' => Str::limit($medium, 100, ''),
            'campaign' => self::queryValue($request, 'utm_campaign'),
            'term' => self::queryValue($request, 'utm_term'),
            'content' => self::queryValue($request, 'utm_content'),
        ];
    }

    public static function device(?string $userAgent): string
    {
        $agent = strtolower((string) $userAgent);

        if (preg_match('/ipad|tablet|kindle|silk/', $agent)) return 'Planšetė';
        if (preg_match('/mobile|iphone|ipod|android/', $agent)) return 'Telefonas';

        return 'Kompiuteris';
    }

    public static function browser(?string $userAgent): string
    {
        $agent = strtolower((string) $userAgent);

        return match (true) {
            str_contains($agent, 'edg/') => 'Edge',
            str_contains($agent, 'opr/') || str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'chrome/') && !str_contains($agent, 'chromium') => 'Chrome',
            str_contains($agent, 'firefox/') => 'Firefox',
            str_contains($agent, 'safari/') && !str_contains($agent, 'chrome/') => 'Safari',
            default => 'Kita',
        };
    }

    private static function sourceFromHost(string $host): array
    {
        return match (true) {
            preg_match('/(^|\.)google\.(com|lt|lv|ee|de|fr|es|it|pl|nl|be|ie|se|no|dk|fi|co\.uk|co\.jp|com\.au|ca|co\.in)$/', $host) === 1
                || self::hostMatches($host, ['googleusercontent.com']) => ['Google', 'organic'],
            self::hostMatches($host, ['bing.com']) => ['Bing', 'organic'],
            self::hostMatches($host, ['yahoo.com']) => ['Yahoo', 'organic'],
            self::hostMatches($host, ['duckduckgo.com']) => ['DuckDuckGo', 'organic'],
            self::hostMatches($host, ['facebook.com', 'fb.com', 'l.facebook.com']) => ['Facebook', 'social'],
            self::hostMatches($host, ['instagram.com', 'l.instagram.com']) => ['Instagram', 'social'],
            self::hostMatches($host, ['linkedin.com', 'lnkd.in']) => ['LinkedIn', 'social'],
            self::hostMatches($host, ['t.co', 'twitter.com', 'x.com']) => ['X / Twitter', 'social'],
            self::hostMatches($host, ['youtube.com', 'youtu.be']) => ['YouTube', 'social'],
            self::hostMatches($host, ['tiktok.com']) => ['TikTok', 'social'],
            default => [$host, 'referral'],
        };
    }

    private static function hostMatches(string $host, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($host === $needle || str_ends_with($host, '.'.$needle)) return true;
        }

        return false;
    }

    private static function queryValue(Request $request, string $key): ?string
    {
        $raw = $request->query($key);
        if (!is_scalar($raw)) return null;
        $value = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $raw) ?? '');

        return $value !== '' ? Str::limit($value, 255, '') : null;
    }
}
