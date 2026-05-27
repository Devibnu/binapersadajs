<?php

namespace Tests\Feature;

use App\Models\AboutPageSetting;
use App\Models\AboutTeam;
use App\Models\PageHero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_safe_cms_fallback_without_template_team_data(): void
    {
        $this->get(route('website.about'))
            ->assertOk()
            ->assertSee('About')
            ->assertSee('Who We Are')
            ->assertSee('Professional Team')
            ->assertSee('data-count="1789"', false)
            ->assertSee('Informasi tim akan segera tersedia.')
            ->assertDontSee('Nats Stenman');
    }

    public function test_admin_updates_about_content_while_hero_is_managed_from_page_heroes(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        PageHero::create([
            'page_key' => 'about',
            'title' => 'Tentang Perusahaan Kami',
            'breadcrumb_text' => 'Profil Perusahaan',
            'background_image' => 'web/images/banner/banner2.jpg',
            'overlay_opacity' => 0.7,
            'text_position' => 'left',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.about-page.edit'))
            ->assertOk()
            ->assertSee('About Page')
            ->assertSee('Right Slider')
            ->assertSee('Team Heading')
            ->assertSee('Banner hero halaman About dikelola melalui menu Page Heroes.')
            ->assertSee(route('paneladmin.page-heroes.index'), false)
            ->assertDontSee('name="hero_title"', false)
            ->assertDontSee('name="hero_breadcrumb"', false)
            ->assertDontSee('name="hero_image"', false);

        $payload = AboutPageSetting::defaults();
        unset(
            $payload['hero_image'],
            $payload['slider_1_image'],
            $payload['slider_2_image'],
            $payload['slider_3_image']
        );
        $payload['section_label'] = 'Tentang Kami';
        $payload['quote_text'] = 'Keselamatan dan mutu adalah prioritas kami.';
        $payload['team_title'] = 'Tim Lapangan Kami';
        $payload['slider_1_image'] = UploadedFile::fake()->image('about-slider.jpg', 1600, 900)->size(8192);

        $this->actingAs($admin)
            ->put(route('paneladmin.about-page.update'), $payload)
            ->assertRedirect(route('paneladmin.about-page.edit'))
            ->assertSessionHas('success', 'Pengaturan halaman About berhasil disimpan. Gambar berhasil diupload dan dioptimasi.');

        $setting = AboutPageSetting::query()->firstOrFail();

        $this->assertNull($setting->hero_title);
        $this->assertNull($setting->hero_breadcrumb);
        $this->assertNull($setting->hero_image);
        $this->assertStringStartsWith('about-page/sliders/', $setting->slider_1_image);
        Storage::disk('public')->assertExists($setting->slider_1_image);

        $this->get(route('website.about'))
            ->assertOk()
            ->assertSee('Tentang Perusahaan Kami')
            ->assertSee('Profil Perusahaan')
            ->assertSee('/web/images/banner/banner2.jpg', false)
            ->assertSee('Tentang Kami')
            ->assertSee('Keselamatan dan mutu adalah prioritas kami.')
            ->assertSee('Tim Lapangan Kami')
            ->assertSee('/storage/' . $setting->slider_1_image, false);
    }

    public function test_admin_can_manage_about_team_and_only_active_team_is_public(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.about-teams.create'))
            ->assertOk()
            ->assertSee('Tambah Anggota Tim')
            ->assertSee('Anggota nonaktif tidak akan tampil di halaman About.');

        $this->actingAs($admin)
            ->post(route('paneladmin.about-teams.store'), [
                'name' => 'Budi Site',
                'position' => 'Site Supervisor',
                'description' => 'Koordinator lapangan industri.',
                'image' => UploadedFile::fake()->image('budi.jpg', 1200, 1000)->size(5000),
                'linkedin_url' => 'https://linkedin.com/in/budi-site',
                'sort_order' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('paneladmin.about-teams.index'));

        AboutTeam::create([
            'name' => 'Tim Nonaktif',
            'position' => 'Tidak Ditampilkan',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $team = AboutTeam::where('name', 'Budi Site')->firstOrFail();
        Storage::disk('public')->assertExists($team->image);

        $this->actingAs($admin)
            ->get(route('paneladmin.about-teams.index', ['q' => 'Budi']))
            ->assertOk()
            ->assertSee('Budi Site')
            ->assertSee('Site Supervisor')
            ->assertDontSee('Tim Nonaktif');

        $this->get(route('website.about'))
            ->assertOk()
            ->assertSee('Budi Site')
            ->assertSee('Site Supervisor')
            ->assertSee('Koordinator lapangan industri.')
            ->assertDontSee('Tim Nonaktif');

        $this->actingAs($admin)
            ->put(route('paneladmin.about-teams.update', $team), [
                'name' => 'Budi Site',
                'position' => 'Project Coordinator',
                'description' => 'Koordinator pelaksanaan proyek.',
                'sort_order' => 1,
                'is_active' => 0,
            ])
            ->assertRedirect(route('paneladmin.about-teams.index'));

        $this->get(route('website.about'))
            ->assertOk()
            ->assertDontSee('Budi Site');

        $this->actingAs($admin)
            ->delete(route('paneladmin.about-teams.destroy', $team))
            ->assertRedirect(route('paneladmin.about-teams.index'));

        $this->assertDatabaseMissing('about_teams', ['id' => $team->id]);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin About',
            'email' => 'about-admin@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
