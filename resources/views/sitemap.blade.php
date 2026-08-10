{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticPaths as $path)
    <url>
        <loc>{{ $baseUrl }}{{ $path === '/' ? '/' : $path }}</loc>
    </url>
@endforeach
@foreach ($projects as $project)
    <url>
        <loc>{{ $baseUrl }}/projektai/{{ $project->slug }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
    </url>
    <url>
        <loc>{{ $baseUrl }}/en/projektai/{{ $project->slug }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
