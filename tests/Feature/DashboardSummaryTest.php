<?php

namespace Tests\Feature;

use App\Models\AboutTeam;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\ContactMessage;
use App\Models\ContactPageSetting;
use App\Models\EmailSetting;
use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_safe_when_cms_has_no_activity_data(): void
    {
        $this->actingAs($this->admin())
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Website')
            ->assertSee('Total Services')
            ->assertSee('data-summary="services" data-value="0"', false)
            ->assertSee('Belum ada pesan kontak.')
            ->assertSee('Belum ada komentar pending.')
            ->assertSee('Belum ada blog terbaru.')
            ->assertSee(route('paneladmin.services.create'), false)
            ->assertSee(route('paneladmin.projects.create'), false)
            ->assertSee(route('paneladmin.blogs.create'), false)
            ->assertSee(route('paneladmin.email-settings.edit'), false);
    }

    public function test_dashboard_displays_summary_counts_activity_and_setup_status(): void
    {
        Service::create(['title' => 'Mechanical Work', 'slug' => 'mechanical-work', 'status' => 'active']);
        Service::create(['title' => 'Fabrication', 'slug' => 'fabrication', 'status' => 'active']);
        Project::create(['title' => 'Shutdown Plant Area', 'slug' => 'shutdown-plant-area', 'status' => 'active']);
        $blog = Blog::create([
            'title' => 'Maintenance Area Industri',
            'slug' => 'maintenance-area-industri',
            'category' => 'Maintenance',
            'is_published' => true,
            'published_at' => now(),
        ]);
        HeroBanner::create([
            'judul' => 'Banner Utama',
            'title' => 'Banner Utama',
            'status_aktif' => true,
            'is_active' => true,
        ]);
        ContactMessage::create([
            'name' => 'PT Energi Industri',
            'email' => 'energi@example.com',
            'subject' => 'Permintaan Penawaran',
            'message' => 'Kami membutuhkan penawaran dukungan pekerjaan area plant.',
            'status' => 'unread',
        ]);
        ContactMessage::create([
            'name' => 'Pesan Lama',
            'email' => 'lama@example.com',
            'message' => 'Pesan ini telah dibaca oleh admin.',
            'status' => 'read',
        ]);
        BlogComment::create([
            'blog_id' => $blog->id,
            'name' => 'Pembaca Industri',
            'email' => 'pembaca@example.com',
            'comment' => 'Artikel ini sangat membantu perencanaan maintenance.',
            'status' => 'pending',
        ]);
        AboutTeam::create([
            'name' => 'Project Manager',
            'position' => 'Operations',
            'is_active' => true,
        ]);

        WebsiteSetting::create([
            'nama_perusahaan' => 'PT. Bina Persada Jaya Sejahtera',
            'email' => 'admin@binapersadajs.co.id',
            'telepon' => '0254-7871299',
            'whatsapp' => '087774824737',
            'alamat' => 'Cilegon, Banten',
        ]);
        EmailSetting::create([
            'mailer' => 'smtp',
            'host' => 'smtp.example.test',
            'port' => 587,
            'username' => 'mail@example.test',
            'password' => 'secret',
            'encryption' => 'tls',
            'from_address' => 'mail@example.test',
            'from_name' => 'Bina Persada JS',
            'is_active' => true,
        ]);
        ContactPageSetting::create(ContactPageSetting::defaults());
        HomepageSetting::create(HomepageSetting::defaults());

        $this->actingAs($this->admin())
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('data-summary="services" data-value="2"', false)
            ->assertSee('data-summary="projects" data-value="1"', false)
            ->assertSee('data-summary="blogs" data-value="1"', false)
            ->assertSee('data-summary="hero_banners" data-value="1"', false)
            ->assertSee('data-summary="contact_messages" data-value="2"', false)
            ->assertSee('data-summary="unread_messages" data-value="1"', false)
            ->assertSee('data-summary="pending_comments" data-value="1"', false)
            ->assertSee('data-summary="active_about_teams" data-value="1"', false)
            ->assertSee('PT Energi Industri')
            ->assertSee('Permintaan Penawaran')
            ->assertSee('Pembaca Industri')
            ->assertSee('Maintenance Area Industri')
            ->assertSee('Website Settings lengkap')
            ->assertSee('SMTP aktif')
            ->assertSee('WhatsApp tersedia')
            ->assertSee('Lengkap');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Dashboard',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('secret'),
        ]);
    }
}
