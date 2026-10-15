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
        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user(),
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],

            'appUrl' => $request->getSchemeAndHttpHost(),
            'locale' => app()->getLocale(),

            // Never enable third-party analytics for signed-in users or private pages.
            'analytics' => [
                'measurementId' => config('app.env') === 'production'
                    && !$request->user()
                    && !$request->is('dashboard*', 'login', 'register', 'forgot-password', 'reset-password*', 'verify-email*', 'confirm-password', 'profile*')
                        ? config('services.google_analytics.measurement_id')
                        : null,
            ],

            'newInquiries' => fn () => $request->user()
                ? Inquiry::where('is_read', false)->count()
                : 0,
        ];
    }
}
