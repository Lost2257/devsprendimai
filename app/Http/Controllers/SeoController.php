<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $baseUrl = request()->getSchemeAndHttpHost();
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
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', compact('baseUrl', 'staticPaths', 'projects'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
