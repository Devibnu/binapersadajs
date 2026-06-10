<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\ContactMessage;
use App\Models\Lead;
use App\Models\PortalConversation;
use App\Models\ProjectReport;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notification_service_collects_unread_activity(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
            'is_active' => true,
        ]);
        $projectReport = ProjectReport::create([
            'job_title' => 'Project Test',
            'visibility' => 'public',
            'is_active' => true,
        ]);
        PortalConversation::create([
            'module_type' => PortalConversation::MODULE_PROJECT_REPORT,
            'module_id' => $projectReport->id,
            'sender_type' => 'client',
            'sender_id' => 1,
            'message' => 'Mohon update project.',
            'is_read' => false,
        ]);
        ContactMessage::create([
            'name' => 'Ryan',
            'email' => 'ryan@example.test',
            'message' => 'Halo',
            'status' => 'unread',
        ]);
        Lead::create([
            'name' => 'Lead Baru',
            'email' => 'lead@example.test',
            'status' => 'new',
        ]);
        $blog = Blog::create([
            'title' => 'Blog Test',
            'slug' => 'blog-test',
            'category' => 'News',
        ]);
        BlogComment::create([
            'blog_id' => $blog->id,
            'name' => 'Komentator',
            'email' => 'comment@example.test',
            'comment' => 'Komentar pending',
            'status' => 'pending',
        ]);

        $notifications = app(AdminNotificationService::class)->forUser($admin);

        $this->assertSame(4, $notifications['totalUnread']);
        $this->assertCount(4, $notifications['latestNotifications']);
        $this->assertContains('IQM Question', $notifications['latestNotifications']->pluck('title'));
        $this->assertContains('Contact Message', $notifications['latestNotifications']->pluck('title'));
        $this->assertContains('Lead Baru', $notifications['latestNotifications']->pluck('title'));
        $this->assertContains('Blog Comment', $notifications['latestNotifications']->pluck('title'));
    }

    public function test_admin_navbar_renders_notification_bell(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('adminNotificationDropdown')
            ->assertSee('Notifikasi');
    }
}
