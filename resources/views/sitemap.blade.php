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
@foreach ($projects as $project)
    <url>
        <loc>{{ $baseUrl }}/projektai/{{ $project->slug }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
        <xhtml:link rel="alternate" hreflang="lt-LT" href="{{ $baseUrl }}/projektai/{{ $project->slug }}" />
        <xhtml:link rel="alternate" hreflang="en" href="{{ $baseUrl }}/en/projektai/{{ $project->slug }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/projektai/{{ $project->slug }}" />
        @if ($project->thumbnail)
            <image:image>
                <image:loc>{{ $baseUrl }}/storage/{{ ltrim($project->thumbnail, '/') }}</image:loc>
                <image:title>{{ $project->title }} projekto vizualizacija</image:title>
            </image:image>
        @endif
    </url>
    <url>
        <loc>{{ $baseUrl }}/en/projektai/{{ $project->slug }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
        <xhtml:link rel="alternate" hreflang="lt-LT" href="{{ $baseUrl }}/projektai/{{ $project->slug }}" />
        <xhtml:link rel="alternate" hreflang="en" href="{{ $baseUrl }}/en/projektai/{{ $project->slug }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/projektai/{{ $project->slug }}" />
        @if ($project->thumbnail)
            <image:image>
                <image:loc>{{ $baseUrl }}/storage/{{ ltrim($project->thumbnail, '/') }}</image:loc>
                <image:title>{{ $project->title }} project preview</image:title>
            </image:image>
        @endif
    </url>
@endforeach
</urlset>
