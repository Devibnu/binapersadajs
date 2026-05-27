<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServicePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_page_lists_active_service_and_detail_link(): void
    {
        $service = $this->service([
            'short_description' => 'Brief service card description.',
            'description' => 'Full detail content must stay away from the services listing card.',
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk()
            ->assertSee($service->title)
            ->assertSee(route('services.show', $service->slug))
            ->assertSee('Layanan Kami')
            ->assertSee('Selengkapnya')
            ->assertSee('Brief service card description.')
            ->assertDontSee('Full detail content must stay away from the services listing card.');
    }

    public function test_service_detail_page_displays_dynamic_content(): void
    {
        $service = $this->service([
            'short_content' => 'Ringkasan dukungan pekerjaan industri.',
            'description' => '<p>Deskripsi <strong>layanan profesional</strong>.</p>',
            'content' => 'Detailed fabrication and field execution content.',
            'feature_1' => 'Controlled execution planning',
            'gallery_image_1' => 'web/images/projects/project1.jpg',
            'faq_1_question' => 'How is the work prepared?',
            'faq_1_answer' => 'Through survey and coordination.',
            'cta_text' => 'Need project support?',
        ]);

        $response = $this->get(route('services.show', $service->slug));

        $response->assertOk()
            ->assertSee($service->title)
            ->assertSee('Ringkasan dukungan pekerjaan industri.')
            ->assertSee('<strong>layanan profesional</strong>', false)
            ->assertSee('Detailed fabrication and field execution content.')
            ->assertSee('Controlled execution planning')
            ->assertSee('Keunggulan Layanan')
            ->assertSee('How is the work prepared?')
            ->assertSee('Need project support?')
            ->assertSee('Hubungi Kami')
            ->assertSee('web/images/services/service1.jpg');
    }

    public function test_detail_translates_legacy_default_cta_from_existing_service_data(): void
    {
        $service = $this->service([
            'cta_text' => 'Interested with this service?',
            'cta_button_text' => 'Get A Quote',
        ]);

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertSee('Butuh dukungan layanan untuk proyek industri Anda?')
            ->assertSee('Hubungi Kami')
            ->assertDontSee('Interested with this service?');
    }

    public function test_service_slug_access_displays_selected_active_service(): void
    {
        $selected = $this->service(['title' => 'Selected Piping', 'slug' => 'selected-piping']);
        $this->service(['title' => 'Another Service', 'slug' => 'another-service']);

        $this->get('/services/' . $selected->slug)
            ->assertOk()
            ->assertSee('Selected Piping');
    }

    public function test_invalid_or_inactive_service_slug_returns_not_found(): void
    {
        $this->service(['title' => 'Inactive Service', 'slug' => 'inactive-service', 'status' => 'inactive']);

        $this->get('/services/not-available')->assertNotFound();
        $this->get('/services/inactive-service')->assertNotFound();
        $this->get(route('services.index'))->assertDontSee('Inactive Service');
        $this->get(route('website.home'))->assertDontSee('Inactive Service');
    }

    public function test_admin_can_set_and_filter_service_status(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'service-admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $active = $this->service([
            'title' => 'Active Service',
            'slug' => 'active-service',
            'short_content' => 'Ringkasan admin layanan.',
            'content' => 'Deskripsi admin layanan.',
            'feature_1' => 'Tim terlatih.',
            'faq_1_question' => 'Apakah tersedia survey?',
            'faq_1_answer' => 'Ya, tersedia.',
        ]);
        $inactive = $this->service([
            'title' => 'Inactive Service',
            'slug' => 'inactive-service',
            'status' => 'inactive',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.services.create'))
            ->assertOk()
            ->assertSee('Tambah Layanan')
            ->assertSee('name="status"', false)
            ->assertSee('value="active" selected', false)
            ->assertSee('Layanan nonaktif tidak akan tampil di website publik.')
            ->assertSee('service-main-image-preview')
            ->assertSee('URL.createObjectURL')
            ->assertSee('service-description-editor')
            ->assertSee('tinymce@8')
            ->assertSee('images_upload_handler');

        $uploadResponse = $this->actingAs($admin)
            ->post(route('paneladmin.editor.upload-image'), [
                'file' => UploadedFile::fake()->image('editor-layanan.jpg', 2400, 1200)->size(8192),
            ])
            ->assertOk()
            ->assertJsonStructure(['location']);

        $this->assertStringContainsString('/storage/editor/content/', $uploadResponse->json('location'));

        $this->actingAs($admin)
            ->get(route('paneladmin.services.edit', $inactive))
            ->assertOk()
            ->assertSee('value="inactive" selected', false);

        $this->actingAs($admin)
            ->get(route('paneladmin.services.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee($active->title)
            ->assertSee('Ringkasan admin layanan.')
            ->assertSee(route('paneladmin.services.show', $active))
            ->assertDontSee($inactive->title);

        $this->actingAs($admin)
            ->get(route('paneladmin.services.show', $active))
            ->assertOk()
            ->assertSee('Detail Layanan')
            ->assertSee('Aktif')
            ->assertSee('Ringkasan admin layanan.')
            ->assertSee('Deskripsi admin layanan.')
            ->assertSee('Tim terlatih.')
            ->assertSee('Apakah tersedia survey?')
            ->assertSee('Ya, tersedia.');

        $this->actingAs($admin)
            ->put(route('paneladmin.services.update', $active), [
                'title' => $active->title,
                'slug' => $active->slug,
                'description' => '<p>Format <strong>tebal</strong> tersimpan.</p>',
                'status' => 'inactive',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('paneladmin.services.index'));

        $this->assertDatabaseHas('services', [
            'id' => $active->id,
            'description' => '<p>Format <strong>tebal</strong> tersimpan.</p>',
            'status' => 'inactive',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('paneladmin.services.store'), [
                'title' => 'Invalid Status',
                'status' => 'hidden',
            ])
            ->assertSessionHasErrors('status');
    }

    private function service(array $attributes = []): Service
    {
        return Service::create(array_merge([
            'title' => 'Mechanical Work',
            'slug' => 'mechanical-work',
            'short_description' => 'Mechanical support.',
            'description' => 'Mechanical detail.',
            'is_active' => true,
            'status' => 'active',
            'sort_order' => 1,
        ], $attributes));
    }
}
