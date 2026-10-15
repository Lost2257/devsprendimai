<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Projects/Index', [
            'projects' => Project::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Projects/Create');
    }

    public function store(Request $request)
    {
        $this->normalizeProjectAddress($request);
        $data = $request->validate($this->rules());
        $data['featured'] = $request->boolean('featured');

        $this->persistProject($request, $data);

        return redirect('/dashboard/projects')
            ->with('success', 'Projektas sukurtas.');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Dashboard/Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->normalizeProjectAddress($request);
        $data = $request->validate($this->rules($project));
        $data['featured'] = $request->boolean('featured');

        $this->persistProject($request, $data, $project);

        return redirect('/dashboard/projects')
            ->with('success', 'Projektas atnaujintas.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        $this->deleteMedia($project);

        return redirect('/dashboard/projects')
            ->with('success', 'Projektas ištrintas.');
    }

    private function rules(?Project $project = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash:ascii', Rule::unique('projects', 'slug')->ignore($project?->id)],
            'client' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'industry_en' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'duration' => ['nullable', 'string', 'max:100'],
            'duration_en' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'url:http,https', 'max:500'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'mobile_thumbnail' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:6'],
            'gallery.*' => ['image', 'max:5120'],
            'remove_thumbnail' => ['sometimes', 'boolean'],
            'remove_mobile_thumbnail' => ['sometimes', 'boolean'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['string', Rule::in($project?->gallery ?? [])],
            'featured' => ['sometimes', 'boolean'],
            'services' => ['nullable', 'string'],
            'services_en' => ['nullable', 'string'],
            'technologies' => ['nullable', 'string'],
            'accent_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'challenge' => ['nullable', 'string'],
            'challenge_en' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'solution_en' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'result_en' => ['nullable', 'string'],
            'metric_one_value' => ['nullable', 'string', 'max:100'],
            'metric_one_label' => ['nullable', 'string', 'max:255'],
            'metric_two_value' => ['nullable', 'string', 'max:100'],
            'metric_two_label' => ['nullable', 'string', 'max:255'],
            'metric_three_value' => ['nullable', 'string', 'max:100'],
            'metric_three_label' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function normalizeProjectAddress(Request $request): void
    {
        $slug = trim((string) $request->input('slug'));
        $url = trim((string) $request->input('url'));

        if (filter_var($slug, FILTER_VALIDATE_URL)) {
            $url = $url ?: $slug;
            $slug = (string) parse_url($slug, PHP_URL_HOST);
        }

        $request->merge([
            'slug' => Str::slug(preg_replace('/^www\./i', '', $slug)),
            'url' => $url ?: null,
        ]);
    }

    private function persistProject(Request $request, array $data, ?Project $project = null): void
    {
        $media = $this->storeMedia($request, $data, $project);
        try {
            $target = $project ?? new Project;
            $target->fill($data);
            if (!$target->save()) {
                throw new \RuntimeException('Nepavyko išsaugoti projekto.');
            }
        } catch (\Throwable $exception) {
            $this->deleteFiles($media['stored']);
            throw $exception;
        }

        // Old media remains available until every upload and the DB save succeed.
        $this->deleteFiles($media['remove']);
    }

    private function storeMedia(Request $request, array &$data, ?Project $project = null): array
    {
        $existingGallery = $project?->gallery ?? [];
        $removedGallery = $request->input('remove_gallery') ?? [];
        $remainingGallery = array_values(array_diff($existingGallery, $removedGallery));
        $newGalleryFiles = $request->file('gallery') ?? [];
        unset($data['remove_gallery']);

        // Validate the final collection before touching any cover or gallery files.
        if (count($remainingGallery) + count($newGalleryFiles) > 6) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'gallery' => 'Galerijoje gali būti daugiausia 6 vaizdai.',
            ]);
        }

        $stored = [];
        $remove = $removedGallery;
        $store = function ($file, string $directory) use (&$stored): string {
            $path = $file->store($directory, 'public');
            if (!is_string($path) || $path === '') {
                throw new \RuntimeException('Nepavyko įkelti projekto vaizdo. Pabandykite dar kartą.');
            }
            $stored[] = $path;
            return $path;
        };

        try {
            foreach (['thumbnail' => 'projects', 'mobile_thumbnail' => 'projects/mobile'] as $field => $directory) {
                $removeField = "remove_{$field}";
                $shouldRemove = $request->boolean($removeField);
                unset($data[$removeField]);

                if ($request->hasFile($field)) {
                    $data[$field] = $store($request->file($field), $directory);
                    if ($project?->{$field}) $remove[] = $project->{$field};
                } elseif ($shouldRemove && $project?->{$field}) {
                    $remove[] = $project->{$field};
                    $data[$field] = null;
                } else {
                    unset($data[$field]);
                }
            }

            if ($newGalleryFiles || $removedGallery) {
                $newGallery = [];
                foreach ($newGalleryFiles as $file) {
                    $newGallery[] = $store($file, 'projects/gallery');
                }
                $data['gallery'] = [...$remainingGallery, ...$newGallery];
            } else {
                unset($data['gallery']);
            }
        } catch (\Throwable $exception) {
            $this->deleteFiles($stored);
            throw $exception;
        }

        return ['stored' => $stored, 'remove' => $remove];
    }

    private function deleteFiles(array $files): void
    {
        if (!$files) return;
        try {
            if (!Storage::disk('public')->delete(array_values(array_unique($files)))) {
                report(new \RuntimeException('Nepavyko pašalinti nebenaudojamų projekto vaizdų.'));
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function deleteMedia(Project $project): void
    {
        $files = array_filter([
            $project->thumbnail,
            $project->mobile_thumbnail,
            ...($project->gallery ?? []),
        ]);

        $this->deleteFiles($files);
    }
}
