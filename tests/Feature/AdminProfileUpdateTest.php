<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_edit_profile_and_active_actions_are_visible(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.profile'))
            ->assertOk()
            ->assertSee(route('paneladmin.profile.edit'), false)
            ->assertSee(route('paneladmin.profile.password'), false);

        $this->actingAs($admin)
            ->get(route('paneladmin.profile.edit'))
            ->assertOk()
            ->assertSee('Edit Profil')
            ->assertSee($admin->name)
            ->assertSee($admin->email);
    }

    public function test_user_can_update_own_name_and_email_without_changing_access_data(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();
        $roleId = $admin->role_id;

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.update'), [
                'name' => 'Ibnu Profile Baru',
                'email' => 'profile.baru@binapersada.test',
            ])
            ->assertRedirect(route('paneladmin.profile'))
            ->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $admin->refresh();
        $this->assertSame('Ibnu Profile Baru', $admin->name);
        $this->assertSame('profile.baru@binapersada.test', $admin->email);
        $this->assertSame($roleId, $admin->role_id);
        $this->assertTrue($admin->is_active);

        $log = ActivityLog::where('action', 'profile.updated')->firstOrFail();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('Profile', $log->module);
        $this->assertArrayNotHasKey('password', $log->properties ?? []);
    }

    public function test_profile_email_must_remain_unique_except_for_current_user(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();
        User::create([
            'name' => 'User Sudah Ada',
            'email' => 'used@binapersada.test',
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.update'), [
                'name' => $admin->name,
                'email' => 'used@binapersada.test',
            ])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.update'), [
                'name' => $admin->name,
                'email' => $admin->email,
            ])
            ->assertRedirect(route('paneladmin.profile'));
    }

    public function test_user_can_open_password_form_and_update_password_securely(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.profile.password'))
            ->assertOk()
            ->assertSee('Ubah Password')
            ->assertSee('Password Saat Ini')
            ->assertSee('Password Baru');

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.password.update'), [
                'current_password' => 'secret123',
                'password' => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertRedirect(route('paneladmin.profile'))
            ->assertSessionHas('success', 'Password berhasil diperbarui.');

        $this->assertTrue(Hash::check('PasswordBaru123!', $admin->refresh()->password));
        $log = ActivityLog::where('action', 'password.updated')->firstOrFail();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertNull($log->properties);
    }

    public function test_wrong_current_password_is_rejected_without_updating_password(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.password.update'), [
                'current_password' => 'password-salah',
                'password' => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('secret123', $admin->refresh()->password));
        $this->assertDatabaseMissing('activity_logs', ['action' => 'password.updated']);
    }

    public function test_new_password_cannot_be_the_same_as_current_password(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('paneladmin.profile.password.update'), [
                'current_password' => 'secret123',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('secret123', $admin->refresh()->password));
        $this->assertDatabaseMissing('activity_logs', ['action' => 'password.updated']);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Ibnu Admin',
            'email' => 'profile@binapersada.test',
            'password' => bcrypt('secret123'),
            'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
