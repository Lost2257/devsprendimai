<?php

namespace Tests\Feature;

use App\Mail\ContactFormMail;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LeadConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['app.env' => 'production', 'services.google_analytics.measurement_id' => 'G-LYW20LSJG9']);
    }

    private function enquiry(array $overrides = []): array
    {
        return [...[
            'name' => 'Private Visitor', 'email' => 'private@example.test', 'phone' => '+37060000000',
            'company' => 'Private company', 'service' => 'Custom private service',
            'message' => 'Private enquiry text that must never go to Google.', 'website' => '',
        ], ...$overrides];
    }

    public function test_saved_consented_lt_and_en_enquiries_receive_a_private_one_time_marker(): void
    {
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted');
        $ids = [];
        foreach (['lt' => '', 'en' => '/en'] as $locale => $prefix) {
            $this->from($prefix ?: '/')->post($prefix.'/contact', $this->enquiry())
                ->assertSessionHasNoErrors()->assertSessionHas('success')->assertSessionHas('enquiry_conversion');
            $marker = session('enquiry_conversion');
            $this->assertSame(['id', 'form_id', 'locale'], array_keys($marker));
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $marker['id']);
            $this->assertSame('project_enquiry', $marker['form_id']);
            $this->assertSame($locale, $marker['locale']);
            $ids[] = $marker['id'];
            foreach ($this->enquiry() as $value) {
                if ($value !== '') $this->assertStringNotContainsString($value, json_encode($marker));
            }
            $this->get($prefix ?: '/')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', $marker));
            $this->get($prefix ?: '/')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', null));
        }
        $this->assertCount(2, array_unique($ids));
        $this->assertDatabaseCount('inquiries', 2);
        Mail::assertSent(ContactFormMail::class, 2);
    }

    public function test_missing_or_refused_consent_does_not_block_enquiries_or_issue_markers(): void
    {
        foreach (['', 'denied'] as $choice) {
            $this->withUnencryptedCookie('devsprendimai_analytics_consent', $choice)
                ->post('/contact', $this->enquiry())->assertSessionHas('success')->assertSessionMissing('enquiry_conversion');
        }
        $this->assertDatabaseCount('inquiries', 2);
        Mail::assertSent(ContactFormMail::class, 2);
    }

    public function test_invalid_honeypot_and_rate_limited_submissions_never_produce_a_conversion(): void
    {
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted');
        // Even a previous unconsumed success must not survive a rejected retry.
        $this->withSession(['enquiry_conversion' => ['id' => 'stale']])
            ->post('/contact', $this->enquiry(['message' => 'short']))
            ->assertSessionHasErrors('message')->assertSessionMissing('enquiry_conversion');
        $this->post('/contact', $this->enquiry(['website' => 'spam.example']))
            ->assertSessionHas('success')->assertSessionMissing('enquiry_conversion');
        for ($i = 0; $i < 3; $i++) {
            $this->post('/contact', $this->enquiry(['website' => 'spam.example']));
        }
        $this->post('/contact', $this->enquiry())->assertSessionHasErrors('message')->assertSessionMissing('enquiry_conversion');
        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_logged_in_and_non_production_enquiries_have_no_conversion_marker(): void
    {
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted');
        config(['app.env' => 'testing']);
        $this->post('/contact', $this->enquiry())->assertSessionHas('success')->assertSessionMissing('enquiry_conversion');
        config(['app.env' => 'production']);
        $this->actingAs(User::factory()->create())->post('/contact', $this->enquiry())
            ->assertSessionHas('success')->assertSessionMissing('enquiry_conversion');
        $this->assertDatabaseCount('inquiries', 2);
    }

    public function test_revoked_consent_and_private_responses_discard_pending_markers(): void
    {
        $marker = ['id' => 'b3ce4c48-dc1e-43b2-9070-d3a2ab67a995', 'form_id' => 'project_enquiry', 'locale' => 'lt'];
        $this->withSession(['enquiry_conversion' => $marker])
            ->withUnencryptedCookie('devsprendimai_analytics_consent', 'denied')
            ->get('/')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', null));
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted')
            ->get('/')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', null));
        $this->withSession(['enquiry_conversion' => $marker])
            ->get('/login')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', null));
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('flash.enquiryConversion', null));
    }

    public function test_email_delivery_failure_does_not_remove_a_saved_enquiry_or_its_marker(): void
    {
        Mail::shouldReceive('to')->once()->with('info@devsprendimai.lt')->andThrow(new \RuntimeException('Mock transport failure'));
        $this->withUnencryptedCookie('devsprendimai_analytics_consent', 'granted')
            ->post('/contact', $this->enquiry())->assertSessionHas('success')->assertSessionHas('enquiry_conversion');
        $this->assertSame(1, Inquiry::count());
    }
}
