<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [

            'projects' => Project::where('featured', true)
                ->latest()
                ->get()
                ->each->useLocale(app()->getLocale()),

            'settings' => Setting::first()

        ]);
    }
}
