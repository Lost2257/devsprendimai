<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PageViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.env' => 'production']);
        $this->withCredentials();
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted');
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone) AppleWebKit/605.1 Mobile Safari/605.1');
    }

    private function payload(array $overrides = []): array
    {
        return [...['event_id' => (string) Str::uuid(), 'url' => '/', 'referrer' => ''], ...$overrides];
    }

    public function test_first_consented_visible_page_and_cached_navigation_are_recorded_without_google(): void
    {
        config(['services.google_analytics.measurement_id' => null]);
        $this->get('/')->assertOk();
        $this->withHeader('Purpose', 'prefetch')->get('/interneto-svetaines')->assertOk();
        $this->assertDatabaseCount('visits', 0);
        $this->flushHeaders();

        $this->postJson('/analytics/page-view', $this->payload())->assertNoContent()
            ->assertHeader('Cache-Control', 'no-store, private');
        // The second page was displayed from Inertia's cache; no fresh GET.
        $this->postJson('/analytics/page-view', $this->payload([
            'url' => '/interneto-svetaines', 'referrer' => 'https://localhost',
        ]))->assertNoContent();
        $this->assertDatabaseCount('visits', 2);
        $this->assertSame(['/', '/interneto-svetaines'], Visit::orderBy('id')->pluck('url')->all());
    }

    public function test_same_event_id_is_idempotent_and_cannot_rewrite_the_original_record(): void
    {
        $payload = $this->payload();
        $this->postJson('/analytics/page-view', $payload)->assertNoContent();
        $this->postJson('/analytics/page-view', [...$payload, 'url' => '/en', 'referrer' => 'https://google.com'])->assertNoContent();
        $this->postJson('/analytics/page-view', [...$payload, 'event_id' => strtoupper($payload['event_id'])])->assertNoContent();
        $this->assertDatabaseCount('visits', 1);
        $this->assertSame('/', Visit::sole()->url);
        $this->assertSame('Tiesiogiai', Visit::sole()->source);

        // A second actual display, including Back, has a new event identifier.
        $this->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->assertDatabaseCount('visits', 2);
    }

    public function test_database_unique_index_handles_race_conflicts_and_preserves_legacy_nulls(): void
    {
        Visit::create(['url' => '/legacy-one']);
        Visit::create(['url' => '/legacy-two']);
        $id = (string) Str::uuid();
        $original = Visit::create(['event_id' => $id, 'url' => '/']);
        // Eloquent's concurrent insert fallback must return the existing row.
        $recovered = Visit::query()->createOrFirst(['event_id' => $id], ['url' => '/en']);
        $this->assertSame($original->id, $recovered->id);
        $this->assertSame('/', $recovered->url);
        $this->assertDatabaseCount('visits', 3);
        $this->expectException(UniqueConstraintViolationException::class);
        DB::table('visits')->insert(['event_id' => $id, 'url' => '/should-fail']);
    }

    public function test_missing_denied_and_withdrawn_consent_never_write(): void
    {
        foreach (['', 'denied', 'true', 'GRANTED'] as $choice) {
            $this->withUnencryptedCookie('devsprendimai_analytics_consent', $choice)
                ->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        }
        $this->assertDatabaseCount('visits', 0);
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted')
            ->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'denied')
            ->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->assertDatabaseCount('visits', 1);
    }

    public function test_authenticated_visitors_and_nonproduction_requests_are_excluded(): void
    {
        foreach (['testing', 'local', 'staging'] as $environment) {
            config(['app.env' => $environment]);
            $this->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        }
        config(['app.env' => 'production']);
        $this->actingAs(User::factory()->create())
            ->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_crawlers_and_prefetch_requests_are_excluded(): void
    {
        foreach (['Googlebot', 'bingbot', 'facebookexternalhit', 'GPTBot', 'ClaudeBot', 'AhrefsBot'] as $agent) {
            $this->withHeader('User-Agent', $agent)->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        }
        $this->flushHeaders();
        $this->withHeader('Purpose', 'prefetch')->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->flushHeaders();
        $this->withHeader('Sec-Purpose', 'prefetch;prerender')->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_only_existing_public_lt_and_en_pages_are_accepted(): void
    {
        Project::create(['title' => 'Test project', 'slug' => 'public-project']);
        $paths = [
            '/', '/interneto-svetaines', '/el-parduotuves', '/crm-sistemos', '/automatizacija',
            '/aplikaciju-kurimas', '/svetainiu-prieziura', '/privatumo-politika', '/slapuku-politika',
            '/paslaugu-teikimo-salygos', '/projektai/public-project',
        ];
        foreach (['', '/en'] as $locale) {
            foreach ($paths as $path) {
                $this->postJson('/analytics/page-view', $this->payload([
                    'url' => $locale.($path === '/' && $locale ? '' : $path),
                ]))->assertNoContent();
            }
        }
        $this->assertDatabaseCount('visits', 22);
    }

    public function test_private_missing_external_and_malformed_paths_are_rejected(): void
    {
        foreach ([
            '/dashboard', '/en/dashboard/projects/1/edit', '/login', '/en/login', '/register',
            '/forgot-password', '/reset-password/private', '/profile', '/contact', '/analytics/page-view',
            '/sitemap.xml', '/robots.txt', '/unknown', '/projektai/missing', '/en/projektai/missing',
            'https://devsprendimai.lt/', 'https://attacker.test/', '//attacker.test/',
            '/\\attacker.test/', '/%2e%2e/dashboard', '/interneto-svetaines/extra',
            '/?x=ok#private-fragment', "/?x=\nsecret",
        ] as $url) {
            $this->postJson('/analytics/page-view', $this->payload(['url' => $url]))
                ->assertUnprocessable()->assertJsonValidationErrors('url');
        }
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_invalid_payload_fields_are_rejected_without_writes(): void
    {
        foreach ([
            ['event_id' => 'not-an-id'], ['event_id' => 'b3ce4c48-dc1e-13b2-9070-d3a2ab67a995'],
            ['event_id' => ['unsafe']], ['url' => ['unsafe']], ['url' => str_repeat('a', 2049)],
            ['referrer' => ['unsafe']], ['referrer' => 'javascript:alert(1)'], ['referrer' => 'ftp://example.test'],
            ['referrer' => str_repeat('a', 2049)],
        ] as $overrides) {
            $this->postJson('/analytics/page-view', $this->payload($overrides))->assertUnprocessable();
        }
        $this->postJson('/analytics/page-view', [])->assertUnprocessable();
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_attribution_uses_displayed_page_and_never_stores_unrelated_queries_or_identifiers(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.75']);
        $this->postJson('/analytics/page-view', $this->payload([
            'url' => '/en?utm_source=newsletter&utm_medium=email&utm_campaign=autumn&email=secret%40example.test&name=Private',
            'referrer' => 'https://www.google.com/search?q=private@example.test#secret',
            'ip' => '203.0.113.1', 'user_agent' => 'spoofed', 'session_hash' => 'spoofed',
        ]))->assertNoContent();
        $visit = Visit::sole();
        $this->assertSame('/en', $visit->url);
        $this->assertSame('newsletter', $visit->source);
        $this->assertSame('email', $visit->medium);
        $this->assertSame('autumn', $visit->campaign);
        $this->assertSame('https://www.google.com', $visit->referrer);
        $this->assertSame('www.google.com', $visit->referrer_host);
        $this->assertTrue($visit->is_entry);
        $this->assertSame('Telefonas', $visit->device);
        $this->assertNull($visit->ip);
        $this->assertNull($visit->user_agent);
        $this->assertSame(64, strlen($visit->session_hash));
        foreach (['private@example.test', 'secret@example.test', 'Private', '198.51.100.75', '203.0.113.1', 'spoofed'] as $private) {
            $this->assertStringNotContainsString($private, $visit->toJson());
        }
    }

    public function test_external_entry_sources_internal_navigation_and_monthly_rotation(): void
    {
        $this->postJson('/analytics/page-view', $this->payload(['referrer' => 'https://www.google.com']))->assertNoContent();
        $first = Visit::sole();
        $this->assertSame('Google', $first->source);
        $this->assertSame('organic', $first->medium);
        $this->assertTrue($first->is_entry);
        $this->postJson('/analytics/page-view', $this->payload(['url' => '/en', 'referrer' => 'http://localhost']))->assertNoContent();
        $second = Visit::latest('id')->first();
        $this->assertFalse($second->is_entry);
        $this->assertSame($first->session_hash, $second->session_hash);
        $this->travel(32)->days();
        $this->postJson('/analytics/page-view', $this->payload())->assertNoContent();
        $this->assertNotSame($first->session_hash, Visit::latest('id')->first()->session_hash);
    }

    public function test_endpoint_has_csrf_protection_and_post_only_routing(): void
    {
        $route = Route::getRoutes()->getByName('analytics.page-view');
        $this->assertContains('web', $route->gatherMiddleware());
        $this->assertContains('throttle:120,1', $route->gatherMiddleware());
        $this->get('/analytics/page-view')->assertStatus(405);

        // Disable Laravel's test-only CSRF bypass for this regression check.
        $this->app['env'] = 'production';
        try {
            $this->postJson('/analytics/page-view', $this->payload())->assertStatus(419);
            $token = Str::random(40);
            $this->withSession(['_token' => $token])
                ->postJson('/analytics/page-view', [...$this->payload(), '_token' => $token])->assertNoContent();
        } finally {
            $this->app['env'] = 'testing';
        }
        $this->assertDatabaseCount('visits', 1);
    }

    public function test_endpoint_rate_limit_prevents_unbounded_duplicate_requests(): void
    {
        $payload = $this->payload();
        for ($i = 0; $i < 120; $i++) {
            $this->postJson('/analytics/page-view', $payload)->assertNoContent();
        }
        $this->postJson('/analytics/page-view', $payload)->assertTooManyRequests();
        $this->assertDatabaseCount('visits', 1);
    }

    public function test_internal_tracking_configuration_is_independent_of_google_and_never_exposed_on_private_pages(): void
    {
        config(['services.google_analytics.measurement_id' => null]);
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('analytics.pageViews', true)->where('analytics.measurementId', null));
        $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('analytics.pageViews', true));
        $this->get('/login')->assertInertia(fn (Assert $page) => $page->where('analytics.pageViews', false));
        config(['app.env' => 'local']);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('analytics.pageViews', false));
        config(['app.env' => 'production']);
        $this->actingAs(User::factory()->create())->get('/')
            ->assertInertia(fn (Assert $page) => $page->where('analytics.pageViews', false));
    }
}
