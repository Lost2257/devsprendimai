<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\User;
use App\Models\Project;
use App\Models\Visit;
use Carbon\Carbon;
use App\Models\Inquiry;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController as PublicProjectController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Dashboard\InquiryController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

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

        $visitSeries = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('d.m'),
                'count' => Visit::whereDate('created_at', $date)->count(),
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => [

                'users' => User::count(),

                'projects' => Project::count(),

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
            ],
            'recentProjects' => Project::latest()->take(4)->get([
                'id', 'title', 'slug', 'thumbnail', 'services', 'featured', 'updated_at',
            ]),
            'recentInquiries' => Inquiry::latest()->take(5)->get([
                'id', 'name', 'company', 'service', 'is_read', 'created_at',
            ]),
            'visitSeries' => $visitSeries,
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

    Route::get('/users/{user}', [UserController::class, 'show']);
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
