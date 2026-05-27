<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_every_protected_module(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');

        $this->actingAs($admin)->get(route('paneladmin.roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('paneladmin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('paneladmin.services.index'))->assertOk();
        $this->assertTrue($admin->canAccess('any.future.permission'));
    }

    public function test_content_and_support_roles_only_see_and_open_their_allowed_modules(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $content = $this->userWithRole('admin-content', 'Content Admin');
        $support = $this->userWithRole('admin-support', 'Support Admin');

        $this->actingAs($content)->get(route('paneladmin.services.index'))->assertOk();
        $this->actingAs($content)->get(route('paneladmin.homepage-video.index'))->assertOk();
        $this->actingAs($content)
            ->get(route('paneladmin.contact-messages.index'))
            ->assertRedirect(route('paneladmin.dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses.');

        $this->actingAs($content)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('Homepage Video')
            ->assertSee('Services')
            ->assertDontSee('Contact Messages');

        $this->actingAs($support)->get(route('paneladmin.contact-messages.index'))->assertOk();
        $this->actingAs($support)
            ->get(route('paneladmin.services.index'))
            ->assertRedirect(route('paneladmin.dashboard'));
    }

    public function test_role_permission_checklist_and_user_assignment_are_saved_and_logged(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $servicesView = Permission::where('slug', 'services.view')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('paneladmin.roles.store'), [
                'name' => 'Viewer Layanan',
                'slug' => 'viewer-layanan',
                'description' => 'Hanya melihat layanan.',
                'is_active' => 1,
                'permissions' => [$servicesView->id],
            ])
            ->assertRedirect(route('paneladmin.roles.index'));

        $role = Role::where('slug', 'viewer-layanan')->firstOrFail();
        $this->assertTrue($role->permissions->contains($servicesView));

        $this->actingAs($admin)
            ->post(route('paneladmin.users.store'), [
                'name' => 'Editor Baru',
                'email' => 'editor@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $role->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('paneladmin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'editor@example.test', 'role_id' => $role->id]);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Roles', 'action' => 'create']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Users', 'action' => 'create']);
    }

    public function test_last_super_admin_cannot_be_reassigned_disabled_or_deleted(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $contentRole = Role::where('slug', 'admin-content')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('paneladmin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role_id' => $contentRole->id,
                'is_active' => 1,
            ])
            ->assertSessionHas('error', 'User Super Admin terakhir tidak dapat dinonaktifkan atau dipindahkan role.');

        $this->assertTrue($admin->fresh()->isSuperAdmin());

        $this->actingAs($admin)
            ->delete(route('paneladmin.users.destroy', $admin))
            ->assertSessionHas('error', 'User Super Admin terakhir tidak dapat dihapus.');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $superRole = Role::where('slug', 'super-admin')->firstOrFail();
        $this->actingAs($admin)
            ->delete(route('paneladmin.roles.destroy', $superRole))
            ->assertSessionHas('error', 'Role Super Admin tidak dapat dihapus.');
    }

    private function userWithRole(string $slug, string $name = 'Super Admin User'): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', $slug)->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
