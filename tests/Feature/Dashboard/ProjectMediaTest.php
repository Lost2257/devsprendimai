<?php

namespace Tests\Feature\Dashboard;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_remove_individual_project_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/main.jpg', 'main');
        Storage::disk('public')->put('projects/mobile/mobile.jpg', 'mobile');
        Storage::disk('public')->put('projects/gallery/keep.jpg', 'keep');
        Storage::disk('public')->put('projects/gallery/remove.jpg', 'remove');

        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        $project = Project::create([
            'title' => 'Test project',
            'slug' => 'test-project',
            'thumbnail' => 'projects/main.jpg',
            'mobile_thumbnail' => 'projects/mobile/mobile.jpg',
            'gallery' => ['projects/gallery/keep.jpg', 'projects/gallery/remove.jpg'],
        ]);

        $response = $this->actingAs($user)->put("/dashboard/projects/{$project->id}", [
            'title' => $project->title,
            'slug' => $project->slug,
            'remove_thumbnail' => true,
            'remove_mobile_thumbnail' => true,
            'remove_gallery' => ['projects/gallery/remove.jpg'],
        ]);

        $response->assertRedirect('/dashboard/projects');

        $project->refresh();
        $this->assertNull($project->thumbnail);
        $this->assertNull($project->mobile_thumbnail);
        $this->assertSame(['projects/gallery/keep.jpg'], $project->gallery);
        Storage::disk('public')->assertMissing('projects/main.jpg');
        Storage::disk('public')->assertMissing('projects/mobile/mobile.jpg');
        Storage::disk('public')->assertMissing('projects/gallery/remove.jpg');
        Storage::disk('public')->assertExists('projects/gallery/keep.jpg');
    }
}
