<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::first();

        return Inertia::render(
            'Dashboard/Settings/Edit',
            [
                'settings' => $settings
            ]
        );
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => ['nullable', 'string', 'max:70'],
            'site_description' => ['nullable', 'string', 'max:170'],
            'og_image' => ['nullable', 'image', 'max:5120'],
            'site_keywords' => ['nullable', 'string', 'max:1000'],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'bing_site_verification' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create([]);
        }

        foreach (['google_site_verification', 'bing_site_verification'] as $verificationField) {
            $value = trim((string) ($data[$verificationField] ?? ''));
            if (preg_match('/content=["\']([^"\']+)["\']/', $value, $matches)) {
                $value = $matches[1];
            }
            $data[$verificationField] = $value ?: null;
        }

        if ($request->hasFile('og_image')) {

            if ($settings->og_image) {
                Storage::disk('public')->delete($settings->og_image);
            }

            $data['og_image'] = $request
                ->file('og_image')
                ->store('seo', 'public');
        }

        $settings->update($data);

        Cache::forget('seo.settings');

        return back()->with('success', 'SEO nustatymai išsaugoti.');
    }
}
