<?php

namespace Tests\Feature;

use App\Models\PageHero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageHeroUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_page_hero_with_large_optimized_background_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->post(route('paneladmin.page-heroes.store'), [
            'page_key' => 'services',
            'title' => 'Layanan',
            'breadcrumb_text' => 'Layanan Industri',
            'background_image' => UploadedFile::fake()->image('hero-services.jpg', 2600, 1300)->size(19000),
            'overlay_opacity' => 0.7,
            'text_position' => 'center',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('paneladmin.page-heroes.index'));

        $pageHero = PageHero::where('page_key', 'services')->firstOrFail();

        Storage::disk('public')->assertExists($pageHero->background_image);
        $this->assertStringEndsWith('.webp', $pageHero->background_image);

        [$width, $height] = getimagesize(Storage::disk('public')->path($pageHero->background_image));

        $this->assertSame(1920, $width);
        $this->assertSame(960, $height);
    }

    public function test_admin_can_update_page_hero_and_replace_old_background_with_optimized_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('page-heroes/old.webp', 'old-file');

        $pageHero = PageHero::create([
            'page_key' => 'about',
            'title' => 'Tentang Kami',
            'background_image' => 'page-heroes/old.webp',
            'overlay_opacity' => 1,
            'text_position' => 'center',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())->put(route('paneladmin.page-heroes.update', $pageHero), [
            'page_key' => 'about',
            'title' => 'Tentang Perusahaan',
            'background_image' => UploadedFile::fake()->image('hero-about.png', 3840, 1400)->size(19000),
            'overlay_opacity' => 0.75,
            'text_position' => 'left',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('paneladmin.page-heroes.index'));

        $pageHero->refresh();
        Storage::disk('public')->assertMissing('page-heroes/old.webp');
        Storage::disk('public')->assertExists($pageHero->background_image);

        [$width] = getimagesize(Storage::disk('public')->path($pageHero->background_image));

        $this->assertSame(1920, $width);
    }

    public function test_page_hero_rejects_background_image_larger_than_twenty_megabytes_in_indonesian(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->from(route('paneladmin.page-heroes.create'))
            ->post(route('paneladmin.page-heroes.store'), [
                'page_key' => 'projects',
                'title' => 'Project',
                'background_image' => UploadedFile::fake()->image('terlalu-besar.jpg', 2400, 1200)->size(20481),
                'overlay_opacity' => 1,
                'text_position' => 'center',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('paneladmin.page-heroes.create'))
            ->assertSessionHasErrors([
                'background_image' => 'Ukuran gambar terlalu besar. Maksimal 20MB.',
            ]);

        $this->assertDatabaseCount('page_heroes', 0);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Page Hero',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('secret'),
        ]);
    }
}
