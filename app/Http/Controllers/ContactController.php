<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // A previous response must never make an invalid/bot retry look like a lead.
        $request->session()->forget('enquiry_conversion');
        // Hash the network identifier: cache keys need not contain a raw IP address.
        $identity = hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));
        $limits = ["contact:short:{$identity}" => [5, 600], "contact:hour:{$identity}" => [20, 3600]];
        foreach ($limits as $key => [$attempts, $seconds]) {
            if (RateLimiter::tooManyAttempts($key, $attempts)) {
                throw ValidationException::withMessages([
                    'message' => app()->isLocale('en')
                        ? 'Too many attempts. Please try again later or email info@devsprendimai.lt.'
                        : 'Per daug bandymų. Pabandykite vėliau arba rašykite info@devsprendimai.lt.',
                ]);
            }
        }
        foreach ($limits as $key => [$attempts, $seconds]) {
            RateLimiter::hit($key, $seconds);
        }

        // Invisible to real visitors. Do not save, email or echo bot submissions.
        if ($request->filled('website')) {
            return $this->sent();
        }

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'service' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];

        $messages = app()->isLocale('en') ? [
                'name.required' => 'Please enter your name.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'service.required' => 'Please select a service.',
                'message.required' => 'Please describe your project.',
                'message.min' => 'The project description must contain at least 10 characters.',
                '*.max' => 'This field is too long (maximum :max characters).',
                '*.string' => 'Please enter text in this field.',
        ] : [
                'name.required' => 'Įveskite savo vardą.',
                'email.required' => 'Įveskite el. pašto adresą.',
                'email.email' => 'Įveskite teisingą el. pašto adresą.',
                'service.required' => 'Pasirinkite paslaugą.',
                'message.required' => 'Aprašykite projektą.',
                'message.min' => 'Projekto aprašymas turi būti bent 10 simbolių.',
                '*.max' => 'Šis laukas per ilgas (daugiausia :max simbolių).',
                '*.string' => 'Šiame lauke įveskite tekstą.',
        ];

        $data = $request->validate($rules, $messages);

        Inquiry::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'service' => $data['service'],
            'message' => $data['message'],
        ]);

        try {
            Mail::to('info@devsprendimai.lt')
                ->send(new ContactFormMail($data));
        } catch (\Throwable $exception) {
            report($exception);
        }

        $response = $this->sent();

        // The opaque, one-response marker is not an inquiry ID and contains no
        // contact data. Only a genuinely persisted, consented guest enquiry can
        // provide it; the browser independently checks consent again.
        if (config('app.env') === 'production'
            && !$request->user()
            && $request->cookie('devsprendimai_analytics_consent') === 'granted'
            && preg_match('/^G-[A-Z0-9]+$/', (string) config('services.google_analytics.measurement_id'))) {
            $response->with('enquiry_conversion', [
                'id' => (string) Str::uuid(),
                'form_id' => 'project_enquiry',
                'locale' => app()->isLocale('en') ? 'en' : 'lt',
            ]);
        }

        return $response;
    }

    private function sent()
    {
        return back()->with(
            'success',
            app()->isLocale('en') ? 'Thank you! Your enquiry has been sent.' : 'Ačiū! Jūsų užklausa išsiųsta.'
        );
    }
}
