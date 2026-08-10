<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Inquiry;
use App\Models\Visit;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => Visit::where('created_at', '<', now()->subDays(90))->delete())
    ->name('privacy:prune-visits')
    ->dailyAt('02:10')
    ->withoutOverlapping();

Schedule::call(fn () => Inquiry::where('created_at', '<', now()->subYears(2))->delete())
    ->name('privacy:prune-inquiries')
    ->dailyAt('02:20')
    ->withoutOverlapping();
