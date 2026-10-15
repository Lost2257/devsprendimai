<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Visit;
use App\Support\PrivacyRetention;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PrivacyRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_records_are_pruned_but_recent_records_survive(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 15)->startOfDay());
        Visit::insert([
            ['url' => '/old', 'created_at' => now()->subDays(91)],
            ['url' => '/recent', 'created_at' => now()->subDays(30)],
        ]);
        foreach ([now()->subYears(2)->subDay(), now()->subYear()] as $date) {
            $inquiry = Inquiry::create(['name' => 'Test', 'email' => 'test@example.test', 'service' => 'Kita', 'message' => 'Test privacy retention.']);
            $inquiry->forceFill(['created_at' => $date])->save();
        }
        $counts = app(PrivacyRetention::class)->runWhenDue();
        $this->assertSame(['visits' => 1, 'inquiries' => 1], $counts);
        $this->assertDatabaseHas('visits', ['url' => '/recent']);
        $this->assertDatabaseCount('inquiries', 1);
        $this->assertTrue(Cache::has(PrivacyRetention::MARKER));

        Visit::insert(['url' => '/old-later', 'created_at' => now()->subDays(91)]);
        $this->assertSame(['visits' => 0, 'inquiries' => 0], app(PrivacyRetention::class)->runWhenDue());
        $this->travel(25)->hours();
        $this->assertSame(1, app(PrivacyRetention::class)->runWhenDue()['visits']);
    }

    public function test_large_backlogs_are_bounded_and_resume_after_short_delay(): void
    {
        Visit::insert(array_fill(0, 1005, ['url' => '/old', 'created_at' => now()->subDays(91)]));
        $this->assertSame(1000, app(PrivacyRetention::class)->runWhenDue()['visits']);
        $this->assertDatabaseCount('visits', 5);
        $this->assertSame(0, app(PrivacyRetention::class)->runWhenDue()['visits']);
        $this->travel(11)->minutes();
        $this->assertSame(5, app(PrivacyRetention::class)->runWhenDue()['visits']);
    }

    public function test_request_fallback_runs_only_in_production(): void
    {
        $this->withoutDefer();
        Visit::insert(['url' => '/old', 'created_at' => now()->subDays(91)]);
        $this->get('/')->assertOk();
        $this->assertDatabaseCount('visits', 1);
        config(['app.env' => 'production']);
        $this->get('/')->assertOk();
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_retention_failure_does_not_break_the_visitor_response(): void
    {
        $this->withoutDefer();
        config(['app.env' => 'production']);
        $this->mock(PrivacyRetention::class)->shouldReceive('runWhenDue')
            ->once()->andThrow(new \RuntimeException('Simulated unavailable retention store'));
        $this->get('/')->assertOk();
        $this->assertFalse(Cache::has(PrivacyRetention::MARKER));
    }
}
