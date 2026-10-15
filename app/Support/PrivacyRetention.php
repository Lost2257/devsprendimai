<?php

namespace App\Support;

use App\Models\Inquiry;
use App\Models\Visit;
use Illuminate\Support\Facades\Cache;

class PrivacyRetention
{
    public const MARKER = 'privacy:retention:completed';
    private const LOCK = 'privacy:retention:lock';
    private const BATCH_SIZE = 1000;

    /** Run in small batches so shared hosting never needs a public cron endpoint. */
    public function runWhenDue(): array
    {
        $counts = ['visits' => 0, 'inquiries' => 0];
        if (Cache::has(self::MARKER)) return $counts;

        Cache::lock(self::LOCK, 60)->get(function () use (&$counts): void {
            if (Cache::has(self::MARKER)) return;

            $remaining = false;
            foreach ([
                'visits' => Visit::query()->where('created_at', '<', now()->subDays(90)),
                'inquiries' => Inquiry::query()->where('created_at', '<', now()->subYears(2)),
            ] as $name => $query) {
                $ids = (clone $query)->orderBy('id')->limit(self::BATCH_SIZE)->pluck('id');
                if ($ids->isNotEmpty()) {
                    $counts[$name] = (clone $query)->whereIn('id', $ids)->delete();
                }
                $remaining = $query->exists() || $remaining;
            }

            // Only a completed pass gets a marker. Large backlogs resume in 10 minutes.
            Cache::put(self::MARKER, true, $remaining ? now()->addMinutes(10) : now()->addDay());
        });

        return $counts;
    }
}
