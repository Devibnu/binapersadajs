<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class WhatsAppWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_displays_whatsapp_widget_with_normalized_settings_number(): void
    {
        $setting = WebsiteSetting::create([
            'nama_perusahaan' => 'PT. Bina Persada Jaya Sejahtera',
            'whatsapp' => '0877-7482-4737',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('id="whatsapp-widget"', false)
            ->assertSee('data-whatsapp-number="6287774824737"', false)
            ->assertSee('PT. Bina Persada JS')
            ->assertSee('Halo, ada yang bisa kami bantu?')
            ->assertSee('Saya ingin meminta penawaran')
            ->assertSee('Kirim ke WhatsApp')
            ->assertSee("'https://wa.me/' + widget.dataset.whatsappNumber", false);
    }

    public function test_widget_is_available_across_public_pages(): void
    {
        $setting = WebsiteSetting::create([
            'whatsapp' => '0877-7482-4737',
        ]);

        View::share('websiteSetting', $setting);

        foreach ([
            'website.home',
            'website.about',
            'services.index',
            'website.projects',
            'website.blog.index',
            'website.contact',
        ] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('id="whatsapp-widget"', false);
        }
    }

    public function test_widget_uses_telephone_as_fallback_number(): void
    {
        $setting = WebsiteSetting::create([
            'telepon' => '(0877) 7482 4737',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.contact'))
            ->assertOk()
            ->assertSee('data-whatsapp-number="6287774824737"', false);
    }

    public function test_widget_ignores_a_masked_whatsapp_value_and_uses_telephone_fallback(): void
    {
        $setting = WebsiteSetting::create([
            'whatsapp' => '087774824XXX',
            'telepon' => '0812-3000-4000',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('data-whatsapp-number="6281230004000"', false)
            ->assertDontSee('data-whatsapp-number="6287774824"', false);
    }

    public function test_widget_is_hidden_when_no_whatsapp_or_telephone_number_is_available(): void
    {
        $setting = WebsiteSetting::create([
            'nama_perusahaan' => 'PT. Bina Persada Jaya Sejahtera',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertDontSee('id="whatsapp-widget"', false);
    }

    public function test_widget_is_not_rendered_in_panel_admin_layout(): void
    {
        $setting = WebsiteSetting::create([
            'whatsapp' => '087774824737',
        ]);
        $admin = User::create([
            'name' => 'Admin Widget',
            'email' => 'admin-widget@example.com',
            'password' => bcrypt('secret'),
        ]);

        View::share('websiteSetting', $setting);

        $this->actingAs($admin)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertDontSee('id="whatsapp-widget"', false);

        auth()->logout();

        $this->get(route('paneladmin.login'))
            ->assertOk()
            ->assertDontSee('id="whatsapp-widget"', false);
    }
}
