<?php

namespace Tests\Feature;

use App\Mail\ContactFormMail;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityPrivacyTest extends TestCase
{
    use RefreshDatabase;

    private function enquiry(array $overrides = []): array
    {
        return [...[
            'name' => 'Test Visitor', 'email' => 'visitor@example.test',
            'service' => 'Interneto svetainė', 'message' => 'Reikia naujos interneto svetainės.',
            'website' => '',
        ], ...$overrides];
    }

    public function test_contact_accepts_real_lt_and_en_submissions_without_marketing_consent(): void
    {
        Mail::fake();
        $this->from('/')->post('/contact', $this->enquiry())->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->from('/en')->post('/en/contact', $this->enquiry(['service' => 'Website']))
            ->assertSessionHasNoErrors()->assertSessionHas('success', 'Thank you! Your enquiry has been sent.');
        $this->assertDatabaseCount('inquiries', 2);
        Mail::assertSent(ContactFormMail::class, 2);
    }

    public function test_honeypot_never_saves_or_sends_bot_submission(): void
    {
        Mail::fake();
        $this->post('/contact', $this->enquiry(['website' => 'spam.example']))->assertSessionHas('success');
        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_contact_rejects_unbounded_or_non_scalar_input(): void
    {
        Mail::fake();
        $this->post('/en/contact', $this->enquiry([
            'name' => str_repeat('x', 121), 'email' => str_repeat('x', 250).'@a.test',
            'phone' => str_repeat('x', 41), 'company' => ['bad'], 'message' => str_repeat('x', 5001),
        ]))->assertSessionHasErrors(['name', 'email', 'phone', 'company', 'message']);
        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_contact_rate_limit_applies_across_languages_and_expires(): void
    {
        Mail::fake();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', $this->enquiry())->assertSessionHasNoErrors();
        }
        $this->post('/en/contact', $this->enquiry())->assertSessionHasErrors('message');
        $this->assertSame(5, Inquiry::count());
        $this->travel(11)->minutes();
        $this->post('/en/contact', $this->enquiry())->assertSessionHasNoErrors();
        $this->assertSame(6, Inquiry::count());
    }

    public function test_security_headers_preserve_frontend_but_protect_private_responses(): void
    {
        config(['app.env' => 'production']);
        $this->get('https://devsprendimai.lt/')->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Strict-Transport-Security', 'max-age=15552000')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'");
        $response = $this->get('/login')->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->get('http://devsprendimai.lt/')->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_internal_statistics_require_consent_and_never_log_private_or_failed_routes(): void
    {
        $this->withoutDefer();
        $this->get('/')->assertOk();
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'denied')->get('/')->assertOk();
        $this->assertDatabaseCount('visits', 0);

        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted')
            ->get('/?utm_source=newsletter&email=private@example.test')->assertOk();
        $visit = Visit::sole();
        $this->assertNull($visit->ip);
        $this->assertNull($visit->user_agent);
        $this->assertSame('/', $visit->url);
        $this->assertSame('newsletter', $visit->source);
        $this->assertSame(64, strlen($visit->session_hash));

        $this->get('/login')->assertOk();
        $this->get('/not-a-real-page')->assertNotFound();
        $this->get('/dashboard')->assertRedirect('/login');
        $this->withHeader('Sec-Purpose', 'prefetch')->get('/')->assertOk();
        $this->flushHeaders();
        $this->actingAs(User::factory()->create())->get('/')->assertOk();
        $this->assertDatabaseCount('visits', 1);
    }

    public function test_internal_visitor_identifier_rotates_monthly(): void
    {
        $this->withoutDefer();
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted')->get('/')->assertOk();
        $before = Visit::sole()->session_hash;
        $this->travel(32)->days();
        $this->get('/')->assertOk();
        $this->assertNotSame($before, Visit::latest('id')->first()->session_hash);
    }
}
