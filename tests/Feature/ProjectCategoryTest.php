<?php

namespace Tests\Feature;

use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_project_categories(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-category@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($admin)
            ->post(route('paneladmin.project-categories.store'), [
                'name' => 'Fabrication',
                'description' => 'Industrial fabrication projects.',
                'is_active' => 1,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('paneladmin.project-categories.index'));

        $category = ProjectCategory::where('slug', 'fabrication')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('paneladmin.project-categories.index'))
            ->assertOk()
            ->assertSee('Fabrication');

        $this->actingAs($admin)
            ->put(route('paneladmin.project-categories.update', $category), [
                'name' => 'Pipe Fabrication',
                'slug' => 'pipe-fabrication',
                'is_active' => 1,
                'sort_order' => 2,
            ])
            ->assertRedirect(route('paneladmin.project-categories.index'));

        $this->assertDatabaseHas('project_categories', [
            'id' => $category->id,
            'name' => 'Pipe Fabrication',
            'slug' => 'pipe-fabrication',
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.project-categories.destroy', $category))
            ->assertRedirect(route('paneladmin.project-categories.index'));

        $this->assertDatabaseMissing('project_categories', ['id' => $category->id]);
    }
}
