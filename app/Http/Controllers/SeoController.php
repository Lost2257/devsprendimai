<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\PageSeo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $staticPaths = [
            '/',
            '/interneto-svetaines',
            '/el-parduotuves',
            '/crm-sistemos',
            '/automatizacija',
            '/aplikaciju-kurimas',
            '/svetainiu-prieziura',
            '/privatumo-politika',
            '/slapuku-politika',
            '/paslaugu-teikimo-salygos',
        ];
        $staticPaths = [
            ...$staticPaths,
            ...array_map(fn (string $path) => '/en'.($path === '/' ? '' : $path), $staticPaths),
        ];
        $projects = Project::query()
            ->orderBy('id')
            ->get(['slug', 'title', 'title_en', 'thumbnail', 'mobile_thumbnail', 'gallery', 'updated_at']);
        $projectUrls = [];
        foreach ($projects as $project) {
            foreach (['lt', 'en'] as $locale) {
                $projectUrls[] = [
                    'url' => $baseUrl.($locale === 'en' ? '/en' : '').'/projektai/'.$project->slug,
                    'ltUrl' => $baseUrl.'/projektai/'.$project->slug,
                    'enUrl' => $baseUrl.'/en/projektai/'.$project->slug,
                    'lastmod' => $project->updated_at?->toAtomString(),
                    'images' => app(PageSeo::class)->projectImages($project, $baseUrl),
                ];
            }
        }

        return response()
            ->view('sitemap', compact('baseUrl', 'staticPaths', 'projectUrls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
