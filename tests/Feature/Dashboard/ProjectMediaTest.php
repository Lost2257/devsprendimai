<?php

namespace Tests\Feature\Dashboard;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
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

    private function image(string $name = 'cover.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z4QAAAABJRU5ErkJggg=='
        ));
    }

    private function admin(): User
    {
        Role::create(['name' => 'admin']);
        return User::factory()->create()->assignRole('admin');
    }

    public function test_rejected_seventh_gallery_image_does_not_remove_or_replace_the_cover(): void
    {
        Storage::fake('public');
        $gallery = array_map(fn ($i) => "projects/gallery/{$i}.png", range(1, 6));
        foreach (['projects/cover.png', ...$gallery] as $path) Storage::disk('public')->put($path, 'original');
        $project = Project::create([
            'title' => 'Test', 'slug' => 'test', 'thumbnail' => 'projects/cover.png', 'gallery' => $gallery,
        ]);

        $this->actingAs($this->admin())->put("/dashboard/projects/{$project->id}", [
            'title' => 'Changed', 'slug' => 'test', 'thumbnail' => $this->image(),
            'gallery' => [$this->image('gallery.png')],
        ])->assertSessionHasErrors('gallery');

        $this->assertSame('projects/cover.png', $project->fresh()->thumbnail);
        $this->assertSame('Test', $project->fresh()->title);
        $this->assertSame($gallery, $project->fresh()->gallery);
        Storage::disk('public')->assertExists(['projects/cover.png', ...$gallery]);
        $this->assertCount(7, Storage::disk('public')->allFiles());
    }

    public function test_failed_second_upload_cleans_new_files_but_keeps_originals(): void
    {
        $disk = Storage::fake('public');
        $disk->put('projects/cover.png', 'original');
        $disk->put('projects/mobile.png', 'original');
        $project = Project::create([
            'title' => 'Test', 'slug' => 'test',
            'thumbnail' => 'projects/cover.png', 'mobile_thumbnail' => 'projects/mobile.png',
        ]);
        $writes = 0;
        $failingDisk = Mockery::mock($disk)->makePartial();
        $failingDisk->shouldReceive('putFileAs')->twice()->andReturnUsing(function (...$args) use (&$writes, $disk) {
            return ++$writes === 2 ? false : $disk->putFileAs(...$args);
        });
        Storage::set('public', $failingDisk);

        $this->actingAs($this->admin())->put("/dashboard/projects/{$project->id}", [
            'title' => 'Changed', 'slug' => 'test', 'thumbnail' => $this->image(),
            'mobile_thumbnail' => $this->image('mobile.png'),
        ])->assertServerError();

        $this->assertSame('Test', $project->fresh()->title);
        $this->assertSame('projects/cover.png', $project->fresh()->thumbnail);
        $disk->assertExists(['projects/cover.png', 'projects/mobile.png']);
        $this->assertCount(2, $disk->allFiles());
    }

    public function test_svg_and_unowned_gallery_removal_are_rejected(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/other.png', 'other project');
        $project = Project::create(['title' => 'Test', 'slug' => 'test']);
        $this->actingAs($this->admin())->put("/dashboard/projects/{$project->id}", [
            'title' => 'Test', 'slug' => 'test',
            'thumbnail' => UploadedFile::fake()->createWithContent('active.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            'remove_gallery' => ['projects/other.png'],
        ])->assertSessionHasErrors(['thumbnail', 'remove_gallery.0']);
        Storage::disk('public')->assertExists('projects/other.png');
        $this->assertCount(1, Storage::disk('public')->allFiles());
    }

    public function test_project_links_accept_only_http_and_https(): void
    {
        $project = Project::create(['title' => 'Test', 'slug' => 'test']);
        $this->actingAs($this->admin());
        foreach (['file://example.test/private', 'ftp://example.test/file', 'javascript://example.test/alert(1)'] as $url) {
            $this->put("/dashboard/projects/{$project->id}", [
                'title' => 'Test', 'slug' => 'test', 'url' => $url,
            ])->assertSessionHasErrors('url');
            $this->assertNull($project->fresh()->url);
        }
        $this->put("/dashboard/projects/{$project->id}", [
            'title' => 'Test', 'slug' => 'test', 'url' => 'https://example.test/project',
        ])->assertSessionHasNoErrors()->assertRedirect('/dashboard/projects');
        $this->assertSame('https://example.test/project', $project->fresh()->url);
    }
}
