<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SearchConsoleReadinessTest extends TestCase
{
    use RefreshDatabase;

    private string $testPublicPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->testPublicPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'devsprendimai-verification-'.bin2hex(random_bytes(12));
        File::makeDirectory($this->testPublicPath, 0700, true);
        $this->app->usePublicPath($this->testPublicPath);
        config([
            'app.url' => 'https://devsprendimai.lt',
            'seo.indexable' => true,
            'seo.google_site_verification' => null,
        ]);
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    protected function tearDown(): void
    {
        if (isset($this->testPublicPath)) {
            File::deleteDirectory($this->testPublicPath);
        }
        parent::tearDown();
    }

    public function test_exact_owner_provided_file_is_reported_as_a_method_not_verified_ownership(): void
    {
        File::put(public_path('googlec4c5c24cccbf3309.html'), "google-site-verification: googlec4c5c24cccbf3309.html\n");

        $this->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('seoHealth.checks.7.label', 'Search Console patvirtinimo priemonė')
            ->where('seoHealth.checks.7.ok', true)
            ->where('seoHealth.checks.7.informational', true)
            ->where('seoHealth.checks.7.detail', fn ($detail) => str_contains($detail, 'HTML failas')
                && str_contains($detail, 'aplikacijos public kataloge')
                && str_contains($detail, 'Tai nėra viešo pasiekiamumo ar Google patvirtintos nuosavybės patikra.')));
    }

    public function test_incorrect_content_and_unrelated_verification_file_are_not_accepted(): void
    {
        File::put(public_path('googlec4c5c24cccbf3309.html'), 'google-site-verification: google-other.html');
        File::put(public_path('google-other.html'), 'google-site-verification: google-other.html');

        $this->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('seoHealth.checks.7.ok', false)
            ->where('seoHealth.checks.7.informational', true)
            ->where('seoHealth.checks.7.detail', fn ($detail) => str_contains($detail, 'nerasta')
                && str_contains($detail, 'DNS patvirtinimas')
                && str_contains($detail, 'Tikrąją nuosavybės būseną tikrinkite Search Console.')));
    }

    public function test_meta_tag_remains_a_supported_method_when_html_file_is_missing(): void
    {
        Setting::create(['google_site_verification' => 'owner-supplied-test-token']);

        $this->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('seoHealth.checks.7.ok', true)
            ->where('seoHealth.checks.7.informational', true)
            ->where('seoHealth.checks.7.detail', fn ($detail) => str_contains($detail, 'HTML žyma')
                && ! str_contains($detail, 'HTML failas')
                && str_contains($detail, 'Būseną tikrinkite Search Console')));
    }

    public function test_missing_method_is_informational_and_does_not_lower_the_score(): void
    {
        $withoutMethod = $this->get('/dashboard')->assertOk();
        $withoutMethod->assertInertia(fn (Assert $page) => $page
            ->where('seoHealth.checks.7.ok', false)
            ->where('seoHealth.checks.7.informational', true)
            ->where('seoHealth.checks.7.detail', fn ($detail) => str_contains($detail, 'DNS patvirtinimas')));
        $scoreWithoutMethod = $withoutMethod->viewData('page')['props']['seoHealth']['score'];

        config(['seo.google_site_verification' => 'owner-supplied-test-token']);
        $this->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('seoHealth.checks.7.ok', true)
            ->where('seoHealth.score', $scoreWithoutMethod));
    }
}
