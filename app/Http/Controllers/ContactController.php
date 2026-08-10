<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'service' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ];

        $messages = app()->isLocale('en') ? [
                'name.required' => 'Please enter your name.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'service.required' => 'Please select a service.',
                'message.required' => 'Please describe your project.',
                'message.min' => 'The project description must contain at least 10 characters.',
        ] : [
                'name.required' => 'Įveskite savo vardą.',
                'email.required' => 'Įveskite el. pašto adresą.',
                'email.email' => 'Įveskite teisingą el. pašto adresą.',
                'service.required' => 'Pasirinkite paslaugą.',
                'message.required' => 'Aprašykite projektą.',
                'message.min' => 'Projekto aprašymas turi būti bent 10 simbolių.',
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

        return back()->with(
            'success',
            app()->isLocale('en') ? 'Thank you! Your enquiry has been sent.' : 'Ačiū! Jūsų užklausa išsiųsta.'
        );
    }
}
