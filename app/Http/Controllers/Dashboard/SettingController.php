<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'site_title' => ['nullable'],
            'site_description' => ['nullable'],
            'og_image' => ['nullable', 'image'],
            'site_keywords' => ['nullable']
        ]);

        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create([]);
        }

        if ($request->hasFile('og_image')) {

            $data['og_image'] = $request
                ->file('og_image')
                ->store('seo', 'public');
        }

        $settings->update($data);

        return back();
    }
}