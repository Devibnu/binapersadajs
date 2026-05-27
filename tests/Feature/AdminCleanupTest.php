<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_and_navbar_only_render_real_cms_navigation(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Pengaturan Website')
            ->assertSee('Analytics Visitor')
            ->assertSee('Leads')
            ->assertSee($admin->name)
            ->assertSee('Keluar')
            ->assertDontSee('Laravel Examples')
            ->assertDontSee('Example pages')
            ->assertDontSee('Virtual Reality')
            ->assertDontSee('New message')
            ->assertDontSee('Payment successfully completed')
            ->assertDontSee('Download');
    }

    public function test_demo_template_routes_are_no_longer_available(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin();

        foreach ([
            '/paneladmin/tables',
            '/paneladmin/billing',
            '/paneladmin/virtual-reality',
            '/paneladmin/rtl',
            '/paneladmin/user-profile',
            '/paneladmin/user-management',
            '/paneladmin/static-sign-in',
            '/paneladmin/static-sign-up',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertNotFound();
        }
    }

    public function test_profile_uses_authenticated_user_and_role_without_dummy_content(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->admin('Ibnu Admin');

        $this->actingAs($admin)
            ->get(route('paneladmin.profile'))
            ->assertOk()
            ->assertSee('Profile Saya')
            ->assertSee('Ibnu Admin')
            ->assertSee('admin@cleanup.test')
            ->assertSee('Super Admin')
            ->assertSee('Login Terakhir')
            ->assertDontSee('Alec Thompson')
            ->assertDontSee('Sophie B.')
            ->assertDontSee('Conversations')
            ->assertDontSee('Platform Settings');
    }

    public function test_login_page_no_longer_exposes_template_branding_or_demo_credentials(): void
    {
        $this->get(route('paneladmin.login'))
            ->assertOk()
            ->assertSee('Bina Persada JS')
            ->assertSee('Masuk menggunakan akun administrator Anda.')
            ->assertDontSee('Creative Tim')
            ->assertDontSee('UPDIVISION')
            ->assertDontSee('admin@softui.com')
            ->assertDontSee('Password <b>secret</b>', false)
            ->assertDontSee('Sign up');
    }

    private function admin(string $name = 'Admin Cleanup'): User
    {
        return User::create([
            'name' => $name,
            'email' => 'admin@cleanup.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
