<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Brief service card description.')
            ->assertDontSee('Full detail content must stay away from the services listing card.');
    }

    public function test_service_detail_page_displays_dynamic_content(): void
    {
        $service = $this->service([
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
            ->assertSee('Detailed fabrication and field execution content.')
            ->assertSee('Controlled execution planning')
            ->assertSee('How is the work prepared?')
            ->assertSee('Need project support?');
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
        $this->service(['slug' => 'inactive-service', 'is_active' => false]);

        $this->get('/services/not-available')->assertNotFound();
        $this->get('/services/inactive-service')->assertNotFound();
    }

    private function service(array $attributes = []): Service
    {
        return Service::create(array_merge([
            'title' => 'Mechanical Work',
            'slug' => 'mechanical-work',
            'short_description' => 'Mechanical support.',
            'description' => 'Mechanical detail.',
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }
}
