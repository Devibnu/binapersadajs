<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\VisitorAnalytic;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsVisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_visit_is_recorded_with_device_and_referer_information(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '192.168.10.42',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/537.36 Chrome/136.0 Safari/537.36',
            'HTTP_REFERER' => 'https://www.google.com/search?q=bina+persada',
        ])->get(route('website.about'))->assertOk();

        $this->assertDatabaseHas('visitor_analytics', [
            'path' => '/about',
            'page_title' => 'Tentang Kami',
            'ip_address' => '192.168.10.42',
            'browser' => 'Chrome',
            'platform' => 'macOS',
            'device_type' => 'desktop',
        ]);
    }

    public function test_admin_route_and_bot_requests_are_not_tracked(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');

        $this->actingAs($admin)->get(route('paneladmin.analytics.index'))->assertOk();
        $this->withHeader('User-Agent', 'Googlebot/2.1 (+http://www.google.com/bot.html)')
            ->get(route('website.home'))
            ->assertOk();

        $this->assertDatabaseCount('visitor_analytics', 0);
    }

    public function test_analytics_dashboard_displays_aggregates_referer_chart_data_and_masked_ip(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');

        VisitorAnalytic::create([
            'session_id' => 'visitor-a',
            'ip_address' => '192.168.100.25',
            'url' => 'https://binapersadajs.co.id/services',
            'path' => '/services',
            'page_title' => 'Layanan',
            'referer' => 'https://www.google.com/',
            'browser' => 'Chrome',
            'platform' => 'Android',
            'device_type' => 'mobile',
            'visited_at' => now(),
        ]);
        VisitorAnalytic::create([
            'session_id' => 'visitor-a',
            'ip_address' => '192.168.100.25',
            'url' => 'https://binapersadajs.co.id/blog',
            'path' => '/blog',
            'page_title' => 'Blog',
            'browser' => 'Chrome',
            'platform' => 'Android',
            'device_type' => 'mobile',
            'visited_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.analytics.index', ['period' => 'today']))
            ->assertOk()
            ->assertSee('Analytics Visitor')
            ->assertSee('Page Views')
            ->assertSee('Visitor Hari Ini')
            ->assertSee('data-chart="visitor-7-days"', false)
            ->assertSee('/services')
            ->assertSee('Google')
            ->assertSee('192.168.xxx.xxx')
            ->assertDontSee('192.168.100.25');
    }

    public function test_analytics_permission_and_sidebar_are_available_for_content_but_not_support(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $content = $this->userWithRole('admin-content', 'Admin Konten');
        $support = $this->userWithRole('admin-support', 'Admin Support');

        $this->actingAs($content)
            ->get(route('paneladmin.analytics.index'))
            ->assertOk();
        $this->actingAs($content)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('Analytics Visitor');

        $this->actingAs($support)
            ->get(route('paneladmin.analytics.index'))
            ->assertRedirect(route('paneladmin.dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses.');
        $this->actingAs($support)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertDontSee('Analytics Visitor');
    }

    private function userWithRole(string $slug, string $name = 'Analytics Admin'): User
    {
        return User::create([
            'name' => $name,
            'email' => str_replace(' ', '.', strtolower($name)) . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', $slug)->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
