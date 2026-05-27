<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\MediaFile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_log_page_loads_and_settings_update_creates_filterable_log(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.activity-logs.index'))
            ->assertOk()
            ->assertSee('Activity Logs')
            ->assertSee('Belum ada aktivitas pada filter ini.');

        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.42', 'HTTP_USER_AGENT' => 'Audit Browser'])
            ->put(route('paneladmin.settings.update'), [
                'nama_perusahaan' => 'PT. Bina Persada Jaya Sejahtera',
                'email' => 'office@binapersadajs.co.id',
            ])
            ->assertRedirect(route('paneladmin.settings.edit'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'user_name' => 'Admin Audit',
            'module' => 'Website Settings',
            'action' => 'update',
            'ip_address' => '203.0.113.42',
        ]);

        $log = ActivityLog::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('paneladmin.activity-logs.index', [
                'module' => 'Website Settings',
                'action' => 'update',
                'user' => 'Admin',
                'q' => 'diperbarui',
            ]))
            ->assertOk()
            ->assertSee('Pengaturan website diperbarui.')
            ->assertSee('Website Settings');

        $this->actingAs($admin)
            ->get(route('paneladmin.activity-logs.show', $log))
            ->assertOk()
            ->assertSee('Detail Activity Log')
            ->assertSee('Audit Browser');
    }

    public function test_create_update_and_delete_content_module_are_logged(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('paneladmin.services.store'), [
                'title' => 'Industrial Cleaning',
                'status' => 'active',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('paneladmin.services.index'));

        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('paneladmin.services.update', $service), [
                'title' => 'Industrial Cleaning & Maintenance',
                'slug' => $service->slug,
                'status' => 'inactive',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('paneladmin.services.index'));

        $this->actingAs($admin)
            ->delete(route('paneladmin.services.destroy', $service))
            ->assertRedirect(route('paneladmin.services.index'));

        $this->assertDatabaseHas('activity_logs', ['module' => 'Services', 'action' => 'create']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Services', 'action' => 'update']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Services', 'action' => 'delete']);
    }

    public function test_media_upload_delete_and_successful_email_reply_are_logged(): void
    {
        Mail::fake();
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('paneladmin.media-library.store'), [
                'file' => UploadedFile::fake()->image('plant.jpg', 1200, 800),
                'title' => 'Plant Area',
            ])
            ->assertRedirect(route('paneladmin.media-library.index'));

        $mediaFile = MediaFile::query()->firstOrFail();
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Media Library',
            'action' => 'upload',
            'subject_id' => $mediaFile->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.media-library.destroy', $mediaFile))
            ->assertRedirect(route('paneladmin.media-library.index'));

        $message = ContactMessage::create([
            'name' => 'Pemohon Proyek',
            'email' => 'pemohon@example.com',
            'subject' => 'Permintaan layanan',
            'message' => 'Mohon informasi pekerjaan maintenance.',
            'status' => 'unread',
        ]);

        $this->actingAs($admin)
            ->patch(route('paneladmin.contact-messages.read', $message))
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch(route('paneladmin.contact-messages.replied', $message))
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('paneladmin.contact-messages.reply', $message), [
                'to_email' => 'pemohon@example.com',
                'subject' => 'Re: Permintaan layanan',
                'body' => 'Terima kasih. Tim kami akan menghubungi Anda untuk pembahasan pekerjaan.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Balasan email berhasil dikirim.');

        $this->assertDatabaseHas('activity_logs', ['module' => 'Media Library', 'action' => 'delete']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Contact Messages', 'action' => 'mark_read']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Contact Messages', 'action' => 'mark_replied']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Contact Messages', 'action' => 'email']);
    }

    public function test_smtp_log_excludes_password_and_login_logout_are_logged(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('paneladmin.email-settings.update'), [
                'mailer' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => 'smtp-user@example.com',
                'password' => 'super-secret-app-password',
                'encryption' => 'tls',
                'from_address' => 'noreply@binapersadajs.co.id',
                'from_name' => 'Bina Persada JS',
                'is_active' => 1,
            ])
            ->assertRedirect(route('paneladmin.email-settings.edit'));

        $smtpLog = ActivityLog::where('module', 'Email Settings')->firstOrFail();
        $encodedProperties = json_encode($smtpLog->properties);

        $this->assertStringNotContainsString('super-secret-app-password', $encodedProperties);
        $this->assertArrayNotHasKey('password', $smtpLog->properties);
        $this->assertSame('smtp.gmail.com', $smtpLog->properties['host']);

        auth()->logout();

        $this->post(route('paneladmin.session'), [
            'email' => $admin->email,
            'password' => 'secret',
        ])->assertRedirect('/paneladmin');

        $this->post(route('paneladmin.logout'))
            ->assertRedirect(route('paneladmin.login'));

        $this->assertDatabaseHas('activity_logs', ['module' => 'Auth', 'action' => 'login', 'user_id' => $admin->id]);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Auth', 'action' => 'logout', 'user_id' => $admin->id]);
    }

    public function test_activity_logs_are_paginated_by_twenty_five_rows(): void
    {
        $admin = $this->admin();

        foreach (range(1, 26) as $number) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'user_name' => $admin->name,
                'module' => 'Tests',
                'action' => 'update',
                'description' => 'Aktivitas #' . $number,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('paneladmin.activity-logs.index'))
            ->assertOk()
            ->assertSee('page=2', false);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Audit',
            'email' => 'audit-' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
