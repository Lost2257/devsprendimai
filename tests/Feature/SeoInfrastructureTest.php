<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://devsprendimai.lt', 'seo.indexable' => true]);
    }

    public function test_sitemap_contains_language_alternates_and_project_image(): void
    {
        Project::create([
            'title' => 'SEO projektas',
            'slug' => 'seo-projektas',
            'thumbnail' => 'projects/seo.png',
            'mobile_thumbnail' => 'projects/mobile.png',
            'gallery' => ['projects/seo.png', 'projects/detail.png'],
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
        $response->assertSee('hreflang="lt-LT"', false);
        $response->assertSee('/en/projektai/seo-projektas', false);
        $response->assertSee('/storage/projects/seo.png', false);
        $response->assertSee('/storage/projects/mobile.png', false);
        $response->assertSee('/storage/projects/detail.png', false);
        $this->assertSame(2, substr_count($response->getContent(), '<image:loc>https://devsprendimai.lt/storage/projects/seo.png</image:loc>'));
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertCount(22, $xml->url);
        $response->assertDontSee('/login', false);
    }

    public function test_private_pages_send_noindex_header(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_every_public_static_page_has_readable_html_without_javascript(): void
    {
        $paths = ['/', '/interneto-svetaines', '/el-parduotuves', '/crm-sistemos', '/automatizacija', '/aplikaciju-kurimas', '/svetainiu-prieziura', '/privatumo-politika', '/slapuku-politika', '/paslaugu-teikimo-salygos'];
        foreach (['', '/en'] as $prefix) {
            foreach ($paths as $path) {
                $url = $prefix.($path === '/' ? '' : $path) ?: '/';
                $response = $this->get($url)->assertOk();
                $dom = $this->html($response->getContent());
                $this->assertSame(1, $dom->query('//*[@id="app"]//main/h1')->length, $url);
                $this->assertGreaterThan(100, mb_strlen($dom->query('//*[@id="app"]//main')->item(0)->textContent), $url);
                $this->assertSame(1, $dom->query('//link[@rel="canonical"]')->length, $url);
                $this->assertSame('https://devsprendimai.lt'.$url, $dom->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'), $url);
                $this->assertSame(3, $dom->query('//link[@rel="alternate"]')->length, $url);
                $this->assertSame($prefix === '/en' ? 'en' : 'lt', $dom->query('/html')->item(0)->getAttribute('lang'), $url);
                $this->assertSame(1, $dom->query('//*[@id="app"]//a[@href="mailto:info@devsprendimai.lt"]')->length, $url);
            }
        }
    }

    public function test_service_content_and_schema_are_present_in_initial_response(): void
    {
        $response = $this->get('/en/interneto-svetaines?utm_source=test&email=private@example.com')->assertOk();
        $dom = $this->html($response->getContent());
        $this->assertSame('https://devsprendimai.lt/en/interneto-svetaines', $dom->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
        $this->assertStringContainsString('Custom UI/UX design', $dom->query('//*[@id="app"]//main')->item(0)->textContent);
        $graph = $this->pageSchema($dom);
        $service = collect($graph)->firstWhere('@type', 'Service');
        $this->assertSame('Website development', $service['name']);
        $this->assertSame('https://devsprendimai.lt/#organization', $service['provider']['@id']);
        $this->assertSame('en', collect($graph)->firstWhere('@type', 'WebPage')['inLanguage']);
    }

    public function test_project_story_is_readable_and_localized_without_interacting_with_3d_scene(): void
    {
        Project::create([
            'title' => 'Pavyzdys', 'title_en' => 'Example', 'slug' => 'example',
            'description' => 'Lietuviškas projekto aprašymas.', 'description_en' => 'An English project description.',
            'challenge' => 'Lietuviškas iššūkis.', 'challenge_en' => 'The original challenge.',
            'solution' => 'Lietuviškas sprendimas.', 'solution_en' => 'The delivered solution.',
            'result' => 'Lietuviškas rezultatas.', 'result_en' => 'The actual outcome.',
            'thumbnail' => 'projects/first.png', 'gallery' => ['projects/second.png'],
        ]);
        $response = $this->get('/en/projektai/example')->assertOk();
        $dom = $this->html($response->getContent());
        $body = $dom->query('//*[@id="app"]//main')->item(0)->textContent;
        $this->assertStringContainsString('The original challenge.', $body);
        $this->assertStringContainsString('The delivered solution.', $body);
        $this->assertStringContainsString('The actual outcome.', $body);
        $this->assertStringNotContainsString('Lietuviškas iššūkis.', $body);
        $project = collect($this->pageSchema($dom))->firstWhere('@type', 'CreativeWork');
        $this->assertSame('Example', $project['name']);
        $this->assertCount(2, $project['image']);
        $this->assertSame('https://devsprendimai.lt/en/projektai/example', $project['url']);
    }

    public function test_complete_cookie_policy_and_table_are_readable_without_javascript(): void
    {
        $dom = $this->html($this->get('/slapuku-politika')->assertOk()->getContent());
        $body = $dom->query('//*[@id="app"]//main')->item(0)->textContent;
        $this->assertStringContainsString('devsprendimai_analytics_consent', $body);
        $this->assertStringContainsString('Sutikimas ir jo atšaukimas', $body);
        $this->assertSame(1, $dom->query('//*[@id="app"]//table')->length);
    }

    public function test_private_pages_have_no_fake_language_alternates_or_business_content(): void
    {
        $dom = $this->html($this->get('/login')->assertOk()->getContent());
        $this->assertSame(0, $dom->query('//link[@rel="alternate"]')->length);
        $this->assertSame(0, $dom->query('//*[@class="initial-content"]')->length);
        $this->assertSame(0, $dom->query('//*[@id="devsprendimai-structured-data"]')->length);
    }

    public function test_staging_is_not_indexable(): void
    {
        config(['seo.indexable' => false]);
        $this->get('/')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('name="robots" content="noindex, nofollow, noarchive"', false);
    }

    public function test_project_text_cannot_inject_script_elements(): void
    {
        Project::create(['title' => 'A </script><script>alert(1)</script>', 'slug' => 'safe', 'description' => 'Safe description']);
        $response = $this->get('/projektai/safe')->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $this->assertSame('A </script><script>alert(1)</script>', collect($this->pageSchema($this->html($response->getContent())))->firstWhere('@type', 'CreativeWork')['name']);
    }

    public function test_dashboard_reports_actual_readiness_and_does_not_claim_search_console_verification(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Project::create(['title' => 'Incomplete', 'slug' => 'incomplete', 'description' => '   ', 'thumbnail' => 'projects/test.png']);
        Setting::create(['og_image' => 'missing-file-fixture.png']);
        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('seoHealth.checks.0.ok', true)
            ->where('seoHealth.checks.1.ok', true)
            ->where('seoHealth.checks.2.ok', false)
            ->where('seoHealth.checks.5.ok', false)
            ->where('seoHealth.checks.7.informational', true)
            ->where('seoHealth.checks.7.ok', false)
            ->where('seoHealth.score', 71));
    }

    private function html(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($document);
    }

    private function pageSchema(DOMXPath $dom): array
    {
        return json_decode($dom->query('//*[@id="devsprendimai-structured-data"]')->item(0)->textContent, true, 512, JSON_THROW_ON_ERROR)['@graph'];
    }
}
