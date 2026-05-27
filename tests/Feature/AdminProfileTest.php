<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_displays_real_account_data_and_professional_sections(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.profile'))
            ->assertOk()
            ->assertSee('Profile Saya')
            ->assertSee('Ibnu Admin')
            ->assertSee('profile@binapersada.test')
            ->assertSee('Super Admin')
            ->assertSee('Informasi Akun')
            ->assertSee('Keamanan Akun')
            ->assertSee('Ringkasan Aktivitas')
            ->assertSee('Aksi Cepat')
            ->assertSee('Akun Aktif')
            ->assertDontSee('Alec Thompson')
            ->assertDontSee('Sophie B.')
            ->assertDontSee('Conversations');
    }

    public function test_profile_displays_only_the_current_users_recent_activity_and_login_ip(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();
        $otherUser = User::create([
            'name' => 'Admin Lain',
            'email' => 'other@binapersada.test',
            'password' => bcrypt('secret'),
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'action' => 'login',
            'module' => 'Auth',
            'description' => 'Administrator masuk ke panel.',
            'ip_address' => '203.0.113.20',
        ]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'action' => 'update',
            'module' => 'Website Settings',
            'description' => 'Pengaturan website diperbarui.',
        ]);
        ActivityLog::create([
            'user_id' => $otherUser->id,
            'user_name' => $otherUser->name,
            'action' => 'update',
            'module' => 'Website Settings',
            'description' => 'Aktivitas milik pengguna lain.',
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.profile'))
            ->assertOk()
            ->assertSee('2 aktivitas')
            ->assertSee('Pengaturan website diperbarui.')
            ->assertSee('203.0.113.20')
            ->assertDontSee('Aktivitas milik pengguna lain.');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Ibnu Admin',
            'email' => 'profile@binapersada.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
