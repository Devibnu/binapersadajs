<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoAdvancedTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_global_seo_settings_and_optimized_images(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin SEO',
            'email' => 'seo-admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.seo-settings.edit'))
            ->assertOk()
            ->assertSee('SEO Settings')
            ->assertSee('Google Analytics GA4 ID')
            ->assertSee('Schema Organization');

        $this->actingAs($admin)
            ->put(route('paneladmin.seo-settings.update'), [
                'meta_title' => 'Bina Persada Industrial Contractor',
                'meta_description' => 'Kontraktor dan fabrication industri di Cilegon.',
                'meta_keywords' => 'contractor, fabrication',
                'canonical_url' => 'https://binapersadajs.co.id',
                'robots_index' => '1',
                'robots_follow' => '1',
                'google_analytics_id' => 'G-ABC1234567',
                'google_tag_manager' => 'GTM-ABC1234',
                'schema_company_name' => 'PT. Bina Persada Jaya Sejahtera',
                'schema_email' => 'info@binapersadajs.co.id',
                'schema_country' => 'ID',
                'twitter_card_type' => 'summary_large_image',
                'og_image' => UploadedFile::fake()->image('og.jpg', 2400, 1200)->size(8192),
                'schema_logo' => UploadedFile::fake()->image('schema.png', 1200, 600)->size(4096),
            ])
            ->assertRedirect(route('paneladmin.seo-settings.edit'))
            ->assertSessionHas('success');

        $setting = SeoSetting::firstOrFail();

        Storage::disk('public')->assertExists($setting->og_image);
        Storage::disk('public')->assertExists($setting->schema_logo);
        $this->assertStringEndsWith('.webp', $setting->og_image);
        $this->assertSame('https://binapersadajs.co.id', $setting->canonical_url);
    }

    public function test_global_meta_canonical_schema_and_optional_google_scripts_render_once(): void
    {
        $this->seoSetting([
            'google_site_verification' => 'google-verification-code',
            'google_analytics_id' => 'G-ABC1234567',
        ]);

        $response = $this->get(route('website.home'))->assertOk();
        $html = $response->getContent();

        $response->assertSee('<meta name="description" content="SEO description production.">', false)
            ->assertSee('<meta name="keywords" content="industrial, contractor">', false)
            ->assertSee('<link rel="canonical" href="https://binapersadajs.co.id/">', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('name="google-site-verification" content="google-verification-code"', false)
            ->assertSee('googletagmanager.com/gtag/js?id=G-ABC1234567', false)
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"LocalBusiness"', false);

        $this->assertSame(1, substr_count($html, '<meta name="description"'));
        $this->assertSame(1, substr_count($html, '<meta property="og:title"'));
        $this->assertSame(1, substr_count($html, '<link rel="canonical"'));
    }

    public function test_blog_detail_renders_article_and_breadcrumb_schema_without_duplicate_meta(): void
    {
        $this->seoSetting();
        $blog = Blog::create([
            'title' => 'Maintenance Industri Terencana',
            'slug' => 'maintenance-industri-terencana',
            'excerpt' => 'Ringkasan maintenance industri.',
            'content' => '<p>Isi artikel maintenance untuk area industri.</p>',
            'category' => 'Maintenance',
            'author_name' => 'Tim BPJS',
            'published_at' => now(),
            'is_published' => true,
            'meta_title' => 'SEO Maintenance Industri',
            'meta_description' => 'Deskripsi artikel untuk mesin pencari.',
            'meta_keywords' => 'maintenance, industri',
        ]);

        $response = $this->get(route('website.blog.show', $blog->slug))->assertOk();
        $html = $response->getContent();

        $response->assertSee('<title>SEO Maintenance Industri</title>', false)
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('https://binapersadajs.co.id/blog/maintenance-industri-terencana', false)
            ->assertSee('"@type":"Article"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('"headline":"Maintenance Industri Terencana"', false);

        $this->assertSame(1, substr_count($html, '<meta property="og:title"'));
        $this->assertSame(1, substr_count($html, '<meta name="twitter:title"'));
    }

    public function test_sitemap_and_robots_respect_active_content_and_index_setting(): void
    {
        $this->seoSetting(['robots_index' => false, 'robots_follow' => false]);
        Service::create(['title' => 'Mechanical Work', 'slug' => 'mechanical-work', 'status' => 'active']);
        Service::create(['title' => 'Hidden Work', 'slug' => 'hidden-work', 'status' => 'inactive']);
        Blog::create([
            'title' => 'Artikel Aktif',
            'slug' => 'artikel-aktif',
            'category' => 'News',
            'is_published' => true,
            'published_at' => now(),
        ]);
        Blog::create([
            'title' => 'Artikel Draft',
            'slug' => 'artikel-draft',
            'category' => 'News',
            'is_published' => false,
        ]);

        $this->get(route('website.sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('<loc>https://binapersadajs.co.id/services/mechanical-work</loc>', false)
            ->assertSee('<loc>https://binapersadajs.co.id/blog/artikel-aktif</loc>', false)
            ->assertDontSee('hidden-work')
            ->assertDontSee('artikel-draft');

        $this->get(route('website.robots'))
            ->assertOk()
            ->assertSee("User-agent: *")
            ->assertSee("Disallow: /")
            ->assertSee('Sitemap: https://binapersadajs.co.id/sitemap.xml');
    }

    public function test_empty_seo_setting_uses_safe_defaults_without_optional_google_scripts(): void
    {
        $response = $this->get(route('website.home'))->assertOk();

        $response->assertSee('name="robots" content="index, follow"', false)
            ->assertSee('"@type":"Organization"', false)
            ->assertDontSee('google-site-verification')
            ->assertDontSee('googletagmanager.com/gtag/js');
    }

    private function seoSetting(array $attributes = []): SeoSetting
    {
        return SeoSetting::create(array_merge([
            'meta_title' => 'SEO Global BPJS',
            'meta_description' => 'SEO description production.',
            'meta_keywords' => 'industrial, contractor',
            'canonical_url' => 'https://binapersadajs.co.id',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_company_name' => 'PT. Bina Persada Jaya Sejahtera',
            'schema_phone' => '0254-7871299',
            'schema_email' => 'info@binapersadajs.co.id',
            'schema_address' => 'Cilegon, Banten',
            'schema_city' => 'Cilegon',
            'schema_country' => 'ID',
            'schema_postal_code' => '42441',
            'twitter_card_type' => 'summary_large_image',
        ], $attributes));
    }
}
