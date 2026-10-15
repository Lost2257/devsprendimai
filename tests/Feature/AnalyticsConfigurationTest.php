<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnalyticsConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_id_is_exposed_only_to_public_production_visitors(): void
    {
        config(['services.google_analytics.measurement_id' => 'G-LYW20LSJG9']);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('analytics.measurementId', null));

        config(['app.env' => 'production']);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('analytics.measurementId', 'G-LYW20LSJG9'));
        $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('analytics.measurementId', 'G-LYW20LSJG9'));
        $this->get('/login')->assertInertia(fn (Assert $page) => $page->where('analytics.measurementId', null));
        $this->actingAs(User::factory()->create())->get('/')
            ->assertInertia(fn (Assert $page) => $page->where('analytics.measurementId', null));
    }
}
