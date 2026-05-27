<?php

namespace Tests\Feature;

use App\Models\HomepageVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepageVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_active_video_defaults_in_modal_section(): void
    {
        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('class="homepage-video"', false)
            ->assertSee('VIDEO COMPANY PROFILE')
            ->assertSee('Aktivitas Project Industri Kami')
            ->assertSee('data-video-url="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&amp;rel=0"', false)
            ->assertSee('Tonton Video');
    }

    public function test_inactive_homepage_video_is_not_rendered_publicly(): void
    {
        HomepageVideo::create(array_merge(HomepageVideo::defaults(), [
            'is_active' => false,
        ]));

        $this->get(route('website.home'))
            ->assertOk()
            ->assertDontSee('class="homepage-video"', false)
            ->assertDontSee('Aktivitas Project Industri Kami');
    }

    public function test_admin_can_update_homepage_video_with_optimized_thumbnail(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.homepage-video.index'))
            ->assertOk()
            ->assertSee('Homepage Video')
            ->assertSee('Gunakan link YouTube agar website tetap ringan dan cepat.')
            ->assertSee('Upload Thumbnail');

        $this->actingAs($admin)
            ->put(route('paneladmin.homepage-video.update'), [
                'section_label' => 'PROFIL VIDEO',
                'title' => 'Pekerjaan Fabrikasi Kami',
                'description' => 'Video kegiatan tim di lokasi proyek.',
                'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
                'thumbnail_image' => UploadedFile::fake()->image('video-cover.jpg', 2400, 1300)->size(8192),
                'button_text' => 'Putar Sekarang',
                'button_link' => 'https://www.youtube.com/',
                'feature_1' => 'Fabrication',
                'feature_2' => 'Maintenance',
                'feature_3' => 'HSE',
                'feature_4' => 'Manpower',
                'is_active' => 1,
            ])
            ->assertRedirect(route('paneladmin.homepage-video.edit'))
            ->assertSessionHas('success', 'Homepage Video berhasil disimpan. Thumbnail berhasil diupload dan dioptimasi.');

        $setting = HomepageVideo::firstOrFail();

        $this->assertStringStartsWith('homepage-video/', $setting->thumbnail_image);
        Storage::disk('public')->assertExists($setting->thumbnail_image);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('PROFIL VIDEO')
            ->assertSee('Pekerjaan Fabrikasi Kami')
            ->assertSee('/storage/' . $setting->thumbnail_image, false);
    }

    public function test_admin_cannot_save_non_youtube_video_url(): void
    {
        $this->actingAs($this->admin())
            ->from(route('paneladmin.homepage-video.edit'))
            ->put(route('paneladmin.homepage-video.update'), array_merge(HomepageVideo::defaults(), [
                'youtube_url' => 'https://example.com/video.mp4',
            ]))
            ->assertRedirect(route('paneladmin.homepage-video.edit'))
            ->assertSessionHasErrors('youtube_url');

        $this->assertDatabaseCount('homepage_videos', 0);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Video',
            'email' => 'video-admin@example.test',
            'password' => bcrypt('secret'),
        ]);
    }
}
