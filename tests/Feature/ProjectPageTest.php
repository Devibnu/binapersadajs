<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_page_lists_active_project_as_gallery_item_without_detail_link(): void
    {
        $category = $this->category();
        $this->category(['name' => 'Hidden Category', 'slug' => 'hidden-category', 'is_active' => false]);
        $project = $this->project([
            'project_category_id' => $category->id,
            'client_name' => 'PT Energi Industri Nusantara',
            'project_location' => 'Cilegon, Banten',
            'project_year' => '2025',
        ]);

        $this->get(route('website.projects'))
            ->assertOk()
            ->assertSee($project->title)
            ->assertDontSee($project->short_description)
            ->assertSee('Maintenance')
            ->assertSee('value="maintenance"', false)
            ->assertSee('data-groups=\'["maintenance"]\'', false)
            ->assertDontSee('Hidden Category')
            ->assertSee('Client:')
            ->assertSee('PT Energi Industri Nusantara')
            ->assertSee('Lokasi:')
            ->assertSee('Cilegon, Banten')
            ->assertSee('Tahun:')
            ->assertSee('2025')
            ->assertSee('gallery-popup')
            ->assertDontSee(route('projects.show', $project->slug))
            ->assertDontSee('View Project');
    }

    public function test_project_detail_displays_metadata_gallery_and_related_project(): void
    {
        $category = $this->category();
        $project = $this->project([
            'description' => 'Complete industrial project execution description.',
            'client_name' => 'PT Sankyu',
            'project_location' => 'Cilegon',
            'project_year' => '2026',
            'project_category_id' => $category->id,
            'gallery_image_1' => 'web/images/projects/project2.jpg',
        ]);
        $related = $this->project(['title' => 'Pipe Installation', 'slug' => 'pipe-installation']);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee($project->title)
            ->assertSee('Complete industrial project execution description.')
            ->assertSee('PT Sankyu')
            ->assertSee('Cilegon')
            ->assertSee('2026')
            ->assertSee($related->title);
    }

    public function test_inactive_or_unknown_project_is_not_available_on_frontend(): void
    {
        $this->project(['slug' => 'inactive-project', 'status' => 'inactive']);

        $this->get('/projects/not-found')->assertNotFound();
        $this->get('/projects/inactive-project')->assertNotFound();
    }

    public function test_admin_can_create_project_with_optimized_featured_image(): void
    {
        Storage::fake('public');
        $category = $this->category(['name' => 'Fabrication', 'slug' => 'fabrication']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-project@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($admin)->post(route('paneladmin.projects.store'), [
            'title' => 'Fabrication Project',
            'short_description' => 'Fabrication work for an industrial site.',
            'featured_image' => UploadedFile::fake()->image('project.jpg', 2400, 1200)->size(8192),
            'project_category_id' => $category->id,
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('paneladmin.projects.index'));

        $project = Project::where('slug', 'fabrication-project')->firstOrFail();
        $this->assertSame($category->id, $project->project_category_id);
        $this->assertNull($project->short_description);
        Storage::disk('public')->assertExists($project->featured_image);
        $this->assertStringEndsWith('.webp', $project->featured_image);

        [$width, $height] = getimagesize(Storage::disk('public')->path($project->featured_image));
        $this->assertSame(1920, $width);
        $this->assertSame(960, $height);
    }

    public function test_admin_project_form_only_shows_simple_grid_fields(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-simple-project@example.com',
            'password' => bcrypt('secret'),
        ]);
        $this->category();

        $this->actingAs($admin)
            ->get(route('paneladmin.projects.create'))
            ->assertOk()
            ->assertSee('Nama Project')
            ->assertSee('Kategori')
            ->assertSee('Featured Image')
            ->assertDontSee('Deskripsi Singkat')
            ->assertDontSee('Deskripsi Lengkap')
            ->assertDontSee('Gallery 1')
            ->assertDontSee('gallery_image_1');

        $project = $this->project();

        $this->actingAs($admin)
            ->get(route('paneladmin.projects.edit', $project))
            ->assertOk()
            ->assertDontSee('Deskripsi Singkat')
            ->assertDontSee('Deskripsi Lengkap')
            ->assertDontSee('Gallery 1');
    }

    public function test_admin_can_view_update_and_delete_project(): void
    {
        $category = $this->category();
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-crud-project@example.com',
            'password' => bcrypt('secret'),
        ]);
        $project = $this->project();

        $this->actingAs($admin)
            ->get(route('paneladmin.projects.show', $project))
            ->assertOk()
            ->assertSee($project->title);

        $this->actingAs($admin)
            ->put(route('paneladmin.projects.update', $project), [
                'title' => 'Updated Project',
                'slug' => 'updated-project',
                'project_category_id' => $category->id,
                'status' => 'active',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('paneladmin.projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project',
            'slug' => 'updated-project',
            'project_category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.projects.destroy', $project))
            ->assertRedirect(route('paneladmin.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    private function project(array $attributes = []): Project
    {
        return Project::create(array_merge([
            'title' => 'Project Maintenance PT Sankyu',
            'slug' => 'project-maintenance-pt-sankyu',
            'short_description' => 'Maintenance support for industrial operations.',
            'description' => 'Project detail content.',
            'status' => 'active',
            'sort_order' => 1,
        ], $attributes));
    }

    private function category(array $attributes = []): ProjectCategory
    {
        return ProjectCategory::create(array_merge([
            'name' => 'Maintenance',
            'slug' => 'maintenance',
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }
}
