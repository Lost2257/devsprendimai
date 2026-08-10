<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function show(Project $project): Response
    {
        $project->useLocale(app()->getLocale());
        $nextProject = Project::query()
            ->where('id', '!=', $project->id)
            ->where('featured', true)
            ->latest()
            ->first(['title', 'title_en', 'slug', 'thumbnail', 'services', 'services_en']);
        $nextProject?->useLocale(app()->getLocale());

        return Inertia::render('Project/Show', [
            'project' => $project,
            'nextProject' => $nextProject,
        ]);
    }
}
