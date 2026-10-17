<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Visit;
use App\Support\TrafficAttribution;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PageViewController extends Controller
{
    private const CAMPAIGN_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    public function __invoke(Request $request): Response
    {
        $agent = strtolower((string) $request->userAgent());
        if (config('app.env') !== 'production'
            || $request->cookie('devsprendimai_analytics_consent') !== 'granted'
            || $request->user()
            || preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|linkedinbot|whatsapp|gptbot|chatgpt|claude|perplexity|bytespider|semrush|ahrefs/', $agent)
            || preg_match('/\bprefetch\b/i', $request->header('Purpose', '').' '.$request->header('Sec-Purpose', ''))) {
            return $this->emptyResponse();
        }

        $validator = Validator::make($request->all(), [
            'event_id' => ['required', 'string', 'regex:/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i'],
            'url' => ['required', 'string', 'max:2048'],
            'referrer' => ['present', 'nullable', 'string', 'max:2048'],
        ]);
        $path = null;
        $validator->after(function ($validator) use ($request, &$path) {
            if (! $validator->errors()->has('url')) {
                $path = $this->publicPath($request->input('url'));
                if ($path === null) {
                    $validator->errors()->add('url', 'Only existing public website pages can be measured.');
                }
            }
            $referrer = $request->input('referrer');
            if (! $validator->errors()->has('referrer') && filled($referrer)
                && (! in_array(strtolower((string) parse_url($referrer, PHP_URL_SCHEME)), ['http', 'https'], true)
                    || ! parse_url($referrer, PHP_URL_HOST))) {
                $validator->errors()->add('referrer', 'The referrer must use HTTP or HTTPS.');
            }
        });
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid page view.', 'errors' => $validator->errors()], 422, [
                'Cache-Control' => 'no-store, private',
                'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            ]);
        }

        // Never forward arbitrary query values or the POST endpoint's referrer.
        // The displayed page supplies attribution; the real request supplies the
        // monthly pseudonym and device. Raw IPs and user agents are not stored.
        parse_str((string) parse_url($request->input('url'), PHP_URL_QUERY), $query);
        $campaign = Arr::only($query, self::CAMPAIGN_KEYS);
        $pageRequest = Request::create($request->getSchemeAndHttpHost().$path, 'GET', $campaign, [], [], [
            'HTTP_REFERER' => (string) $request->input('referrer'),
        ]);
        $attribution = TrafficAttribution::from($pageRequest);
        $fingerprint = implode('|', [
            now()->utc()->format('Y-m'), (string) $request->ip(), (string) $request->userAgent(),
        ]);

        // The database UNIQUE index is essential: firstOrCreate recovers from
        // concurrent unique-key conflicts, not just sequential client retries.
        Visit::firstOrCreate(['event_id' => strtolower($request->input('event_id'))], [
            'ip' => null,
            'session_hash' => hash_hmac('sha256', $fingerprint, (string) config('app.key')),
            'url' => Str::limit($path, 255, ''),
            ...$attribution,
            'device' => TrafficAttribution::device($request->userAgent()),
            'browser' => TrafficAttribution::browser($request->userAgent()),
            'is_entry' => $attribution['medium'] !== 'direct'
                || blank($attribution['referrer_host']) || filled($attribution['campaign']),
            'user_agent' => null,
        ]);

        return $this->emptyResponse();
    }

    private function publicPath(string $url): ?string
    {
        if (! str_starts_with($url, '/') || str_starts_with($url, '//')
            || preg_match('/[\\\\\x00-\x20\x7f]/', $url)) {
            return null;
        }
        $parts = parse_url($url);
        if ($parts === false || isset($parts['host']) || isset($parts['scheme']) || isset($parts['fragment'])) {
            return null;
        }
        $path = $parts['path'] ?? '';
        $localPath = preg_replace('#^/en(?=/|$)#', '', $path) ?: '/';
        if (in_array($localPath, [
            '/', '/interneto-svetaines', '/el-parduotuves', '/crm-sistemos',
            '/automatizacija', '/aplikaciju-kurimas', '/svetainiu-prieziura',
            '/privatumo-politika', '/slapuku-politika', '/paslaugu-teikimo-salygos',
        ], true)) {
            return $path;
        }
        if (preg_match('#^/projektai/([^/]+)$#', $localPath, $match)
            && Project::where('slug', rawurldecode($match[1]))->exists()) {
            return $path;
        }

        return null;
    }

    private function emptyResponse(): Response
    {
        return response()->noContent(204, [
            'Cache-Control' => 'no-store, private',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ]);
    }
}
