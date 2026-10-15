<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Inquiry;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Protect private Inertia props stored in browser history. HTTPS and
        // localhost provide the secure context required by Web Crypto.
        \Inertia\Inertia::encryptHistory((bool) $request->user());

        $analyticsId = config('app.env') === 'production'
            && !$request->user()
            && !preg_match('#^(?:en/)?(?:dashboard|login|register|forgot-password|reset-password|verify-email|confirm-password|profile)(?:/|$)#', $request->path())
                ? config('services.google_analytics.measurement_id')
                : null;

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user()?->only(['id', 'name', 'email', 'email_verified_at']),
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                // Consume once, never re-emit on a later request or consent change.
                'enquiryConversion' => function () use ($request, $analyticsId) {
                    $marker = $request->session()->pull('enquiry_conversion');

                    return $analyticsId && $request->cookie('devsprendimai_analytics_consent') === 'granted'
                        ? $marker
                        : null;
                },
            ],

            'appUrl' => $request->getSchemeAndHttpHost(),
            'locale' => app()->getLocale(),

            // Never enable third-party analytics for signed-in users or private pages.
            'analytics' => [
                'measurementId' => $analyticsId,
            ],

            'newInquiries' => fn () => $request->user()?->hasAnyRole(['super-user', 'admin'])
                ? Inquiry::where('is_read', false)->count()
                : 0,
        ];
    }
}
