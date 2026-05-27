<?php

namespace Tests\Feature;

use App\Models\HomepageSetting;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class HomepageSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_safe_defaults_without_a_saved_setting(): void
    {
        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('About Company')
            ->assertSee('KAPABILITAS INDUSTRI')
            ->assertSee('LAYANAN KAMI')
            ->assertSee('Quality &amp; HSE Commitment', false)
            ->assertSee('Company Updates')
            ->assertSee('data-count="100"', false)
            ->assertSee('%', false);
    }

    public function test_admin_can_open_and_update_homepage_sections(): void
    {
        $admin = User::create([
            'name' => 'Admin Homepage',
            'email' => 'homepage-admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.homepage-sections.edit'))
            ->assertOk()
            ->assertSee('Homepage Sections')
            ->assertSee('Label Section Layanan')
            ->assertSee('Judul Section Layanan')
            ->assertSee('Quality &amp; HSE Commitment', false)
            ->assertSee('Latest Blog Heading');

        $payload = HomepageSetting::defaults();
        $payload['about_title'] = 'Solusi Kontraktor Industri Terpercaya';
        $payload['counter_2_number'] = '99%';
        $payload['service_section_label'] = 'SOLUSI INDUSTRI';
        $payload['service_section_title'] = 'KEAHLIAN UTAMA';
        $payload['quality_title'] = 'Komitmen Mutu dan Keselamatan';
        $payload['cta_title'] = 'Diskusikan Kebutuhan Proyek Anda';
        $payload['blog_title'] = 'Berita Proyek Terbaru';

        $this->actingAs($admin)
            ->put(route('paneladmin.homepage-sections.update'), $payload)
            ->assertRedirect(route('paneladmin.homepage-sections.edit'))
            ->assertSessionHas('success', 'Section homepage berhasil disimpan.');

        $this->assertDatabaseHas('homepage_settings', [
            'about_title' => 'Solusi Kontraktor Industri Terpercaya',
            'counter_2_number' => '99%',
            'service_section_label' => 'SOLUSI INDUSTRI',
            'service_section_title' => 'KEAHLIAN UTAMA',
            'blog_title' => 'Berita Proyek Terbaru',
        ]);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('Solusi Kontraktor Industri Terpercaya')
            ->assertSee('SOLUSI INDUSTRI')
            ->assertSee('KEAHLIAN UTAMA')
            ->assertSee('Komitmen Mutu dan Keselamatan')
            ->assertSee('Diskusikan Kebutuhan Proyek Anda')
            ->assertSee('Berita Proyek Terbaru')
            ->assertSee('data-count="99"', false)
            ->assertSee('%', false);
    }

    public function test_empty_service_section_headings_use_indonesian_fallback(): void
    {
        HomepageSetting::create(array_merge(HomepageSetting::defaults(), [
            'service_section_label' => null,
            'service_section_title' => '',
        ]));

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('KAPABILITAS INDUSTRI')
            ->assertSee('LAYANAN KAMI');
    }

    public function test_homepage_inquiry_section_keeps_lead_action_and_uses_whatsapp_settings(): void
    {
        $setting = WebsiteSetting::create([
            'nama_perusahaan' => 'PT. Bina Persada Jaya Sejahtera',
            'telepon' => '0254-7871299',
            'whatsapp' => '0877-7482-4737',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('class="industrial-inquiry"', false)
            ->assertSee('BUTUH SUPPORT INDUSTRIAL?')
            ->assertSee('MINTA PENAWARAN')
            ->assertSee('KIRIM PENAWARAN')
            ->assertSee('action="' . route('website.leads.inquiry') . '"', false)
            ->assertSee('https://wa.me/6287774824737?text=', false);
    }
}
