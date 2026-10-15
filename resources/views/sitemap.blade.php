{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($staticPaths as $path)
    @php
        $plainPath = preg_replace('#^/en(?=/|$)#', '', $path) ?: '/';
        $ltUrl = $baseUrl.($plainPath === '/' ? '/' : $plainPath);
        $enUrl = $baseUrl.'/en'.($plainPath === '/' ? '' : $plainPath);
    @endphp
    <url>
        <loc>{{ $baseUrl }}{{ $path === '/' ? '/' : $path }}</loc>
        <xhtml:link rel="alternate" hreflang="lt-LT" href="{{ $ltUrl }}" />
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $ltUrl }}" />
    </url>
@endforeach
@foreach ($projectUrls as $project)
    <url>
        <loc>{{ $project['url'] }}</loc>
        @if ($project['lastmod'])<lastmod>{{ $project['lastmod'] }}</lastmod>@endif
        <xhtml:link rel="alternate" hreflang="lt-LT" href="{{ $project['ltUrl'] }}" />
        <xhtml:link rel="alternate" hreflang="en" href="{{ $project['enUrl'] }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $project['ltUrl'] }}" />
        @foreach ($project['images'] as $image)
            <image:image><image:loc>{{ $image }}</image:loc></image:image>
        @endforeach
    </url>
@endforeach
</urlset>
