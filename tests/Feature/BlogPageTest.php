<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_list_displays_published_article_with_indonesian_sidebar_and_detail_link(): void
    {
        $blog = $this->blog();
        $this->blog([
            'title' => 'Artikel Draft',
            'slug' => 'artikel-draft',
            'is_published' => false,
        ]);

        $this->get(route('website.blog.index'))
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee('Artikel Terbaru')
            ->assertSee('Kategori')
            ->assertSee('Arsip')
            ->assertSee('Tag')
            ->assertSee('Lanjutkan Membaca')
            ->assertSee(route('website.blog.show', $blog->slug))
            ->assertDontSee('Artikel Draft');
    }

    public function test_blog_detail_displays_article_content_tags_and_sidebar(): void
    {
        $blog = $this->blog([
            'meta_title' => 'SEO Maintenance Area Industri',
            'meta_description' => 'Deskripsi SEO artikel maintenance.',
            'meta_keywords' => 'maintenance, industri',
            'og_image' => 'web/images/news/news2.jpg',
        ]);

        $this->get(route('website.blog.show', $blog->slug))
            ->assertOk()
            ->assertSee('<title>SEO Maintenance Area Industri</title>', false)
            ->assertSee('<meta name="description" content="Deskripsi SEO artikel maintenance.">', false)
            ->assertSee('<meta name="keywords" content="maintenance, industri">', false)
            ->assertSee('property="og:title" content="SEO Maintenance Area Industri"', false)
            ->assertSee('property="og:image" content="' . asset('web/images/news/news2.jpg') . '"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee($blog->title)
            ->assertSee('Paragraf isi artikel.')
            ->assertSee('<strong>format tebal</strong>', false)
            ->assertSee('Maintenance')
            ->assertSee('Artikel Terbaru')
            ->assertDontSee('Comments');
    }

    public function test_blog_detail_uses_seo_fallbacks_and_sidebar_shows_only_five_latest_posts(): void
    {
        $current = $this->blog();

        foreach (range(1, 6) as $number) {
            $this->blog([
                'title' => 'Artikel Terbaru ' . $number,
                'slug' => 'artikel-terbaru-' . $number,
                'published_at' => now()->subDays($number)->format('Y-m-d H:i:s'),
                'sort_order' => 10 - $number,
            ]);
        }

        $response = $this->get(route('website.blog.show', $current->slug))
            ->assertOk()
            ->assertSee('<title>' . $current->title . '</title>', false)
            ->assertSee('<meta name="description" content="' . $current->excerpt . '">', false)
            ->assertSee('property="og:image" content="' . asset('web/images/news/news1.jpg') . '"', false);

        $response->assertSee('Artikel Terbaru 1')
            ->assertSee('Artikel Terbaru 5')
            ->assertDontSee('Artikel Terbaru 6');
    }

    public function test_blog_empty_state_and_unpublished_article_protection(): void
    {
        $draft = $this->blog(['is_published' => false]);

        $this->get(route('website.blog.index'))
            ->assertOk()
            ->assertSee('Belum ada artikel yang tersedia.');

        $this->get(route('website.blog.show', $draft->slug))->assertNotFound();
        $this->get('/blog/artikel-tidak-ada')->assertNotFound();
    }

    public function test_admin_can_create_update_and_delete_blog_with_optimized_image(): void
    {
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-blog@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.blogs.create'))
            ->assertOk()
            ->assertSee('Judul Artikel')
            ->assertSee('Tanggal Publish')
            ->assertSee('Gambar Utama')
            ->assertSee('Meta Title')
            ->assertSee('Meta Description')
            ->assertSee('Meta Keywords')
            ->assertSee('OG Image')
            ->assertSee('SEO Analyzer')
            ->assertSee('Preview Google Search')
            ->assertSee('seo-score-progress')
            ->assertSee('seo-meta-title-counter')
            ->assertSee('updateAnalyzer')
            ->assertSee('Isi artikel kurang dari 300 kata')
            ->assertSee('blog-content-editor')
            ->assertSee('tinymce@8')
            ->assertSee('images_upload_handler');

        $this->actingAs($admin)
            ->post(route('paneladmin.blogs.store'), [
                'title' => 'Artikel Maintenance',
                'excerpt' => 'Ringkasan artikel maintenance.',
                'content' => 'Konten lengkap.',
                'category' => 'Maintenance',
                'tags' => 'Maintenance, Safety',
                'author_name' => 'Admin',
                'published_at' => '2026-05-22 08:00:00',
                'featured_image' => UploadedFile::fake()->image('blog.jpg', 2400, 1200)->size(8192),
                'meta_title' => 'SEO Artikel Maintenance',
                'meta_description' => 'Meta description maintenance.',
                'meta_keywords' => 'maintenance, industri',
                'og_image' => UploadedFile::fake()->image('og-blog.jpg', 2400, 1200)->size(8192),
                'is_published' => '1',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('paneladmin.blogs.index'));

        $blog = Blog::where('slug', 'artikel-maintenance')->firstOrFail();
        Storage::disk('public')->assertExists($blog->featured_image);
        $this->assertStringEndsWith('.webp', $blog->featured_image);
        Storage::disk('public')->assertExists($blog->og_image);
        $this->assertStringContainsString('blogs/og/', $blog->og_image);
        $this->assertStringEndsWith('.webp', $blog->og_image);
        $this->assertSame('SEO Artikel Maintenance', $blog->meta_title);

        $uploadResponse = $this->actingAs($admin)
            ->post(route('paneladmin.blogs.upload-image'), [
                'file' => UploadedFile::fake()->image('isi-artikel.jpg', 2400, 1200)->size(8192),
            ])
            ->assertOk()
            ->assertJsonStructure(['location']);

        $contentImageLocation = $uploadResponse->json('location');
        $this->assertStringContainsString('/storage/blogs/content/', $contentImageLocation);

        $this->actingAs($admin)
            ->get(route('paneladmin.blogs.index'))
            ->assertOk()
            ->assertSee('Artikel Maintenance');

        $this->actingAs($admin)
            ->get(route('paneladmin.blogs.edit', $blog))
            ->assertOk()
            ->assertSee('Edit Artikel')
            ->assertSee('Artikel Maintenance');

        $this->actingAs($admin)
            ->put(route('paneladmin.blogs.update', $blog), [
                'title' => 'Artikel Maintenance Berkala',
                'slug' => 'artikel-maintenance-berkala',
                'category' => 'Maintenance',
                'is_published' => '1',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('paneladmin.blogs.index'));

        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'slug' => 'artikel-maintenance-berkala',
            'sort_order' => 2,
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.blogs.destroy', $blog))
            ->assertRedirect(route('paneladmin.blogs.index'));

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    private function blog(array $attributes = []): Blog
    {
        return Blog::create(array_merge([
            'title' => 'Pentingnya Maintenance Berkala',
            'slug' => 'pentingnya-maintenance-berkala',
            'excerpt' => 'Ringkasan artikel.',
            'content' => '<p>Paragraf isi artikel. <strong>format tebal</strong>.</p><p>Paragraf kedua.</p>',
            'category' => 'Maintenance',
            'tags' => 'Maintenance, Safety',
            'author_name' => 'Admin',
            'published_at' => '2026-05-22 08:00:00',
            'is_published' => true,
            'sort_order' => 1,
        ], $attributes));
    }
}
