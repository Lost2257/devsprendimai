<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="dark">
        @php
            extract(app(\App\Support\PageSeo::class)->forPage(request(), $page), EXTR_SKIP);
            $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
        @endphp
        <link data-seo-canonical rel="canonical" href="{{ $canonical }}">
        @if ($public)
            <link data-seo-canonical rel="alternate" hreflang="lt-LT" href="{{ $ltUrl }}">
            <link data-seo-canonical rel="alternate" hreflang="en" href="{{ $enUrl }}">
            <link data-seo-canonical rel="alternate" hreflang="x-default" href="{{ $ltUrl }}">
        @endif

        <meta data-seo-meta="description" name="description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="robots" name="robots" content="{{ $seo['robots'] }}">
        <meta data-seo-meta="googlebot" name="googlebot" content="{{ $seo['robots'] }}">
        <meta name="author" content="DevSprendimai">
        <meta name="theme-color" content="#02060e">
        <meta data-seo-meta="og:locale" property="og:locale" content="{{ $english ? 'en_US' : 'lt_LT' }}">
        <meta data-seo-meta="og:locale:alternate" property="og:locale:alternate" content="{{ $english ? 'lt_LT' : 'en_US' }}">
        <meta property="og:site_name" content="DevSprendimai">
        <meta data-seo-meta="og:type" property="og:type" content="{{ $seo['type'] }}">
        <meta data-seo-meta="og:title" property="og:title" content="{{ $seo['title'] }}">
        <meta data-seo-meta="og:description" property="og:description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="og:url" property="og:url" content="{{ $canonical }}">
        <meta data-seo-meta="og:image" property="og:image" content="{{ $seo['image'] }}">
        <meta data-seo-meta="og:image:secure_url" property="og:image:secure_url" content="{{ $seo['image'] }}">
        <meta data-seo-meta="og:image:alt" property="og:image:alt" content="{{ $seo['image_alt'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta data-seo-meta="twitter:title" name="twitter:title" content="{{ $seo['title'] }}">
        <meta data-seo-meta="twitter:description" name="twitter:description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="twitter:image" name="twitter:image" content="{{ $seo['image'] }}">
        <meta data-seo-meta="twitter:image:alt" name="twitter:image:alt" content="{{ $seo['image_alt'] }}">

        @if ($googleVerification)
            <meta name="google-site-verification" content="{{ $googleVerification }}">
        @endif
        @if ($bingVerification)
            <meta name="msvalidate.01" content="{{ $bingVerification }}">
        @endif

        @if ($public)
            <script id="devsprendimai-organization" type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $globalGraph], $jsonFlags) !!}</script>
            <script id="devsprendimai-structured-data" type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $pageGraph], $jsonFlags) !!}</script>
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <title inertia>{{ $seo['title'] }}</title>

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @if ($public && empty($__inertiaSsrResponse))
            {{-- Everyone receives readable content and real links before JavaScript starts.
                 Vue replaces this fallback on mount; nothing is hidden solely for crawlers. --}}
            @if (config('inertia.use_script_element_for_initial_page'))
                <script data-page="app" type="application/json">{!! json_encode($page, $jsonFlags) !!}</script>
                <div id="app">@include('seo-content')</div>
            @else
                <div id="app" data-page="{{ json_encode($page) }}">@include('seo-content')</div>
            @endif
        @else
            @inertia
        @endif
    </body>
</html>
