<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\HomepageSetting;
use App\Models\Project;
use App\Models\ProjectCategory;
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

    public function test_homepage_displays_latest_published_blog_articles(): void
    {
        Blog::create([
            'title' => 'Published Article One',
            'slug' => 'published-article-one',
            'excerpt' => 'Excerpt one',
            'content' => 'Content one',
            'featured_image' => 'web/images/news/news1.jpg',
            'category' => 'General',
            'published_at' => now()->subDays(1),
            'is_published' => true,
        ]);

        Blog::create([
            'title' => 'Published Article Two',
            'slug' => 'published-article-two',
            'excerpt' => 'Excerpt two',
            'content' => 'Content two',
            'featured_image' => 'web/images/news/news2.jpg',
            'category' => 'General',
            'published_at' => now()->subDays(2),
            'is_published' => true,
        ]);

        $response = $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('Published Article One')
            ->assertSee('Published Article Two')
            ->assertSee(route('website.blog.index'))
            ->assertSee('See All Posts');

        $response->assertSeeInOrder(['Published Article One', 'Published Article Two']);
    }

    public function test_homepage_does_not_show_unpublished_blog_articles(): void
    {
        Blog::create([
            'title' => 'Draft Article',
            'slug' => 'draft-article',
            'excerpt' => 'Draft excerpt',
            'content' => 'Draft content',
            'category' => 'General',
            'published_at' => now()->subDays(3),
            'is_published' => false,
        ]);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertDontSee('Draft Article');
    }

    public function test_homepage_blog_articles_are_ordered_by_latest_published_date(): void
    {
        Blog::create([
            'title' => 'Older Published Article',
            'slug' => 'older-published-article',
            'excerpt' => 'Older article excerpt',
            'content' => 'Older article content',
            'category' => 'General',
            'published_at' => now()->subDays(10),
            'is_published' => true,
        ]);

        Blog::create([
            'title' => 'Newer Published Article',
            'slug' => 'newer-published-article',
            'excerpt' => 'Newer article excerpt',
            'content' => 'Newer article content',
            'category' => 'General',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $response = $this->get(route('website.home'))
            ->assertOk();

        $response->assertSeeInOrder(['Newer Published Article', 'Older Published Article']);
    }

    public function test_homepage_project_section_uses_active_database_categories_and_published_projects(): void
    {
        $activeCategory = ProjectCategory::create([
            'name' => 'Fabrication',
            'slug' => 'fabrication',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $inactiveCategory = ProjectCategory::create([
            'name' => 'Civil',
            'slug' => 'civil',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        Project::create([
            'title' => 'Active Fabrication Project',
            'slug' => 'active-fabrication-project',
            'project_category_id' => $activeCategory->id,
            'status' => 'active',
            'sort_order' => 1,
            'featured_image' => 'web/images/projects/project2.jpg',
        ]);

        Project::create([
            'title' => 'Inactive Fabrication Project',
            'slug' => 'inactive-fabrication-project',
            'project_category_id' => $activeCategory->id,
            'status' => 'inactive',
            'sort_order' => 2,
            'featured_image' => 'web/images/projects/project5.jpg',
        ]);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('project-category-fabrication')
            ->assertDontSee('project-category-civil')
            ->assertSee('Active Fabrication Project')
            ->assertDontSee('Inactive Fabrication Project')
            ->assertSeeInOrder(['Fabrication']);
    }

    public function test_projects_page_continue_to_use_active_database_categories(): void
    {
        $activeCategory = ProjectCategory::create([
            'name' => 'Mechanical',
            'slug' => 'mechanical',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ProjectCategory::create([
            'name' => 'Civil',
            'slug' => 'civil',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        Project::create([
            'title' => 'Active Mechanical Project',
            'slug' => 'active-mechanical-project',
            'project_category_id' => $activeCategory->id,
            'status' => 'active',
            'sort_order' => 1,
            'featured_image' => 'web/images/projects/project5.jpg',
        ]);

        $this->get(route('website.projects'))
            ->assertOk()
            ->assertSee('project-category-mechanical')
            ->assertDontSee('project-category-civil')
            ->assertSee('Active Mechanical Project');
    }

    public function test_admin_can_update_homepage_project_headings(): void
    {
        $admin = User::create([
            'name' => 'Admin Project Heading',
            'email' => 'project-heading-admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $payload = array_merge(HomepageSetting::defaults(), [
            'project_section_label' => 'PROJECT ACTIVITY',
            'project_section_title' => 'INDUSTRIAL WORKS',
        ]);

        $this->actingAs($admin)
            ->put(route('paneladmin.homepage-sections.update'), $payload)
            ->assertRedirect(route('paneladmin.homepage-sections.edit'))
            ->assertSessionHas('success', 'Section homepage berhasil disimpan.');

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee('PROJECT ACTIVITY')
            ->assertSee('INDUSTRIAL WORKS');
    }
}
