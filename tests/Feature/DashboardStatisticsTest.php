<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_keep_day_month_and_week_windows_separate(): void
    {
        $this->travelTo(Carbon::create(2026, 10, 17, 12, 0, 0, 'Europe/Vilnius'));
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        foreach ([
            ['2025-10-17 10:00:00', 'old-year'],
            ['2026-09-30 23:59:59', 'old-month'],
            ['2026-10-01 00:00:00', null],
            ['2026-10-10 23:59:59', 'visitor-a'],
            ['2026-10-11 00:00:00', 'visitor-a'],
            ['2026-10-16 23:59:59', 'visitor-b'],
            ['2026-10-17 00:00:00', 'visitor-b'],
            ['2026-10-17 11:59:00', 'visitor-a'],
        ] as [$createdAt, $visitor]) {
            Visit::insert(['url' => '/', 'session_hash' => $visitor, 'created_at' => $createdAt]);
        }

        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.today_visits', 2)
            ->where('stats.month_visits', 6)
            ->where('stats.unique_visitors', 2)
            ->has('visitSeries', 7)
            ->where('visitSeries.0.label', '11.10')
            ->where('visitSeries.0.count', 1)
            ->where('visitSeries.1.count', 0)
            ->where('visitSeries.5.count', 1)
            ->where('visitSeries.6.label', '17.10')
            ->where('visitSeries.6.count', 2)
            ->where('analyticsSummary.refreshedAt', '2026-10-17T12:00:00+03:00')
            ->where('analyticsSummary.lastVisitAt', '2026-10-17T11:59:00+03:00')
            ->where('analyticsSummary.timeZone', 'Europe/Vilnius')
        );
        $this->assertDatabaseCount('visits', 8);
    }

    public function test_empty_statistics_can_be_refreshed_without_creating_admin_views(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $initial = $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->get('/dashboard', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $initial->viewData('page')['version'],
            'X-Inertia-Partial-Component' => 'Dashboard',
            'X-Inertia-Partial-Data' => 'stats,visitSeries,analyticsSummary',
        ])->assertOk()
            ->assertJsonPath('props.stats.today_visits', 0)
            ->assertJsonPath('props.stats.month_visits', 0)
            ->assertJsonPath('props.analyticsSummary.lastVisitAt', null)
            ->assertJsonCount(7, 'props.visitSeries')
            ->assertJsonMissingPath('props.recentInquiries');
        $this->assertDatabaseCount('visits', 0);
    }
}
