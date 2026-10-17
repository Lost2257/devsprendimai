<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\User;
use App\Models\Project;
use App\Models\Visit;
use Carbon\Carbon;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController as PublicProjectController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageViewController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Dashboard\InquiryController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// Only displayed, explicitly consented pages are counted. Prefetched GETs are not.
Route::post('/analytics/page-view', PageViewController::class)
    ->middleware('throttle:120,1')->name('analytics.page-view');

$publicRoutes = function (): void {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/interneto-svetaines', fn () => Inertia::render('Services/Websites'));
    Route::get('/el-parduotuves', fn () => Inertia::render('Services/Ecommerce'));
    Route::get('/crm-sistemos', fn () => Inertia::render('Services/CRM'));
    Route::get('/automatizacija', fn () => Inertia::render('Services/Automation'));
    Route::get('/aplikaciju-kurimas', fn () => Inertia::render('Services/Applications'));
    Route::get('/svetainiu-prieziura', fn () => Inertia::render('Services/Maintenance'));
    Route::get('/privatumo-politika', fn () => Inertia::render('Legal/Privacy'));
    Route::get('/slapuku-politika', fn () => Inertia::render('Legal/Cookies'));
    Route::get('/paslaugu-teikimo-salygos', fn () => Inertia::render('Legal/Terms'));
    Route::post('/contact', [ContactController::class, 'send']);
    Route::get('/projektai/{project:slug}', [PublicProjectController::class, 'show']);
};

$publicRoutes();
Route::prefix('en')->group($publicRoutes);

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-user|admin'
])
->prefix('dashboard')
->group(function () {

    Route::get('/', function () {

        $monthVisits = Visit::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);

        $trafficSources = (clone $monthVisits)
            ->where('is_entry', true)
            ->select('source', 'medium', DB::raw('COUNT(*) as visits'), DB::raw('COUNT(DISTINCT session_hash) as visitors'))
            ->groupBy('source', 'medium')
            ->orderByDesc('visits')
            ->limit(8)
            ->get();

        $topPages = (clone $monthVisits)
            ->select('url', DB::raw('COUNT(*) as visits'), DB::raw('COUNT(DISTINCT session_hash) as visitors'))
            ->groupBy('url')
            ->orderByDesc('visits')
            ->limit(8)
            ->get();

        $devices = (clone $monthVisits)
            ->select('device', DB::raw('COUNT(*) as visits'))
            ->groupBy('device')
            ->orderByDesc('visits')
            ->get();

        $campaigns = (clone $monthVisits)
            ->whereNotNull('campaign')
            ->select('campaign', 'source', DB::raw('COUNT(*) as visits'))
            ->groupBy('campaign', 'source')
            ->orderByDesc('visits')
            ->limit(6)
            ->get();

        $settings = Setting::first();
        $projectCount = Project::count();
        $completeProjects = Project::query()
            ->whereNotNull('description')
            ->whereRaw('TRIM(description) <> ?', [''])
            ->whereNotNull('thumbnail')
            ->where('thumbnail', '!=', '')
            ->count();
        $homeMetadata = app(\App\Support\PageSeo::class)->forPage(
            \Illuminate\Http\Request::create(rtrim(config('app.url'), '/').'/'),
            ['props' => []],
        )['seo'];
        $socialImageExists = filled($settings?->og_image)
            ? \Illuminate\Support\Facades\Storage::disk('public')->exists($settings->og_image)
            : is_file(public_path('og.png'));
        $robotsFile = public_path('robots.txt');
        $hasVerificationTag = filled($settings?->google_site_verification) || filled(config('seo.google_site_verification'));
        $verificationFile = public_path('googlec4c5c24cccbf3309.html');
        $expectedVerificationContent = 'google-site-verification: googlec4c5c24cccbf3309.html';
        $verificationContent = is_file($verificationFile) && is_readable($verificationFile)
            ? file_get_contents($verificationFile)
            : null;
        $hasVerificationFile = in_array($verificationContent, [
            $expectedVerificationContent,
            $expectedVerificationContent."\n",
            $expectedVerificationContent."\r\n",
        ], true);
        $verificationMethods = [];
        if ($hasVerificationFile) {
            $verificationMethods[] = 'HTML failas (turinys patikrintas aplikacijos public kataloge)';
        }
        if ($hasVerificationTag) {
            $verificationMethods[] = 'HTML žyma';
        }
        $verificationDetail = $verificationMethods
            ? 'Paruošta: '.implode(' ir ', $verificationMethods).'. Tai nėra viešo pasiekiamumo ar Google patvirtintos nuosavybės patikra. Būseną tikrinkite Search Console; DNS patvirtinimas taip pat galioja.'
            : 'Tinkamo HTML failo aplikacijos public kataloge ir HTML žymos nerasta. Galimas DNS patvirtinimas. Tikrąją nuosavybės būseną tikrinkite Search Console.';
        $seoChecks = [
            ['label' => 'Pradžios SEO pavadinimas', 'ok' => filled($homeMetadata['title']), 'detail' => filled($settings?->site_title) ? 'Naudojamas jūsų nustatytas tekstas.' : 'Naudojamas paruoštas paslaugų pavadinimas.'],
            ['label' => 'Pradžios meta aprašymas', 'ok' => filled($homeMetadata['description']), 'detail' => filled($settings?->site_description) ? 'Naudojamas jūsų nustatytas aprašymas.' : 'Paruoštas aprašymas galioja ir neužpildžius nustatymų.'],
            ['label' => 'Socialinio vaizdo failas', 'ok' => $socialImageExists],
            ['label' => 'Indeksavimas leidžiamas', 'ok' => (bool) config('seo.indexable'), 'detail' => 'Svetainės konfigūracija, ne Google indeksavimo patvirtinimas.'],
            ['label' => 'Pradinis HTML turinys', 'ok' => is_file(resource_path('data/seo-content.json')) && view()->exists('seo-content'), 'detail' => 'Paslaugų, projektų ir politikų turinys pateikiamas dar prieš JavaScript.'],
            ['label' => 'Projektų aprašymai ir vaizdų nuorodos', 'ok' => $projectCount > 0 && $completeProjects === $projectCount, 'detail' => $completeProjects.' iš '.$projectCount.' projektų užpildyta.'],
            ['label' => 'Sitemap ir robots konfigūracija', 'ok' => Route::has('sitemap') && is_file($robotsFile) && str_contains(file_get_contents($robotsFile), 'Sitemap: '.rtrim(config('app.url'), '/').'/sitemap.xml')],
            ['label' => 'Search Console patvirtinimo priemonė', 'ok' => $hasVerificationFile || $hasVerificationTag, 'informational' => true, 'detail' => $verificationDetail],
        ];
        $readinessChecks = collect($seoChecks)->reject(fn ($check) => $check['informational'] ?? false);
        $seoScore = (int) round($readinessChecks->where('ok', true)->count() / max($readinessChecks->count(), 1) * 100);

        $visitSeries = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('d.m'),
                'count' => Visit::whereDate('created_at', $date)->count(),
            ];
        });

        return Inertia::render('Dashboard', [
            'analyticsSummary' => [
                'refreshedAt' => now()->toIso8601String(),
                'lastVisitAt' => ($lastVisitAt = Visit::max('created_at'))
                    ? Carbon::parse($lastVisitAt)->toIso8601String()
                    : null,
                'timeZone' => config('app.timezone'),
            ],
            'mailNotificationsEnabled' => !in_array(config('mail.default'), ['log', 'array', null], true),
            'stats' => [

                'users' => User::count(),

                'projects' => $projectCount,

                'inquiries' => Inquiry::count(),
                'new_inquiries' => Inquiry::where('is_read', false)->count(),

                'today_visits' => Visit::whereDate(
                    'created_at',
                    Carbon::today()
                )->count(),

                'month_visits' => Visit::whereMonth(
                    'created_at',
                    now()->month
                )
                ->whereYear(
                    'created_at',
                    now()->year
                )
                ->count(),

                'unique_visitors' => (clone $monthVisits)
                    ->whereNotNull('session_hash')
                    ->distinct('session_hash')
                    ->count('session_hash'),
            ],
            'recentProjects' => Project::latest()->take(4)->get([
                'id', 'title', 'slug', 'thumbnail', 'services', 'featured', 'updated_at',
            ]),
            'recentInquiries' => Inquiry::latest()->take(5)->get([
                'id', 'name', 'company', 'service', 'is_read', 'created_at',
            ]),
            'visitSeries' => $visitSeries,
            'trafficSources' => $trafficSources,
            'topPages' => $topPages,
            'devices' => $devices,
            'campaigns' => $campaigns,
            'seoHealth' => [
                'score' => $seoScore,
                'checks' => $seoChecks,
            ],
        ]);

    })->name('dashboard');

    Route::get(
        '/settings',
        [SettingController::class, 'edit']
    );

    Route::put(
        '/settings',
        [SettingController::class, 'update']
    );

    Route::get('/inquiries', [InquiryController::class, 'index']);
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show']);

    Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/create', [UserController::class, 'create']);
    Route::post('/users', [UserController::class, 'store']);

    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('password.confirm');
    Route::get('/users/{user}/edit', [UserController::class, 'edit']);

    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::get('/projects', [ProjectController::class, 'index']);

    Route::get('/projects/create', [ProjectController::class, 'create']);
    Route::post('/projects', [ProjectController::class, 'store']);

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit']);

    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
