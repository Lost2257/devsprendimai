<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_language_alternates_and_project_image(): void
    {
        Project::create([
            'title' => 'SEO projektas',
            'slug' => 'seo-projektas',
            'thumbnail' => 'projects/seo.png',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
        $response->assertSee('hreflang="lt-LT"', false);
        $response->assertSee('/en/projektai/seo-projektas', false);
        $response->assertSee('/storage/projects/seo.png', false);
    }

    public function test_private_pages_send_noindex_header(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }
}
