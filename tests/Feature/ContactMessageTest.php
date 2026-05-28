<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReplyMail;
use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_submit_contact_message_as_unread(): void
    {
        Mail::fake();
        WebsiteSetting::create([
            'nama_perusahaan' => 'Bina Persada JS',
            'email' => 'kontak@binapersadajs.co.id',
        ]);

        $this->get(route('website.contact'))
            ->assertOk()
            ->assertSee('Kirim Pesan')
            ->assertSee('Telepon / WhatsApp')
            ->assertSee('Lokasi Google Maps belum diatur.')
            ->assertSee('KIRIM PESAN')
            ->assertSee(route('website.contact.store'));

        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.20',
            'HTTP_USER_AGENT' => 'Contact Browser',
        ])->post(route('website.contact.store'), [
            'name' => 'Ibnu',
            'email' => 'ibnu@example.com',
            'phone' => '0812-3456-7890',
            'subject' => 'Permintaan penawaran',
            'message' => 'Kami membutuhkan dukungan pekerjaan maintenance area industri.',
        ])
            ->assertRedirect()
            ->assertSessionHas('contact_success', 'Pesan Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ibnu',
            'email' => 'ibnu@example.com',
            'status' => 'unread',
            'ip_address' => '203.0.113.20',
        ]);

        Mail::assertSent(NewContactMessageMail::class, function (NewContactMessageMail $mail) {
            return $mail->hasTo('kontak@binapersadajs.co.id')
                && $mail->contactMessage->email === 'ibnu@example.com'
                && str_contains($mail->render(), '/paneladmin/contact-messages/' . $mail->contactMessage->id);
        });
    }

    public function test_contact_honeypot_submission_looks_successful_but_is_not_stored(): void
    {
        Mail::fake();

        $this->post(route('website.contact.store'), [
            'website_url' => 'https://spam.invalid',
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Pesan spam yang seharusnya tidak disimpan.',
        ])
            ->assertRedirect()
            ->assertSessionHas('contact_success');

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_contact_submit_still_succeeds_without_a_notification_recipient(): void
    {
        Mail::fake();
        config(['mail.from.address' => null]);

        $this->post(route('website.contact.store'), [
            'name' => 'Pengunjung',
            'email' => 'pengunjung@example.com',
            'message' => 'Mohon informasi layanan fabrikasi untuk kebutuhan proyek.',
        ])
            ->assertRedirect()
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'pengunjung@example.com',
            'status' => 'unread',
        ]);
        Mail::assertNothingSent();
    }

    public function test_mail_failure_does_not_fail_contact_submission(): void
    {
        WebsiteSetting::create([
            'nama_perusahaan' => 'Bina Persada JS',
            'email' => 'notifikasi@binapersadajs.co.id',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with('notifikasi@binapersadajs.co.id')
            ->andThrow(new \RuntimeException('SMTP tidak tersedia'));
        Log::spy();

        $this->post(route('website.contact.store'), [
            'name' => 'Visitor Email Error',
            'email' => 'error@example.com',
            'message' => 'Pesan ini tetap harus masuk walaupun mail server bermasalah.',
        ])
            ->assertRedirect()
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'error@example.com',
            'status' => 'unread',
        ]);
        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Failed to send contact notification email', \Mockery::on(function (array $context) {
                return $context['message'] === 'SMTP tidak tersedia'
                    && ! empty($context['contact_message_id']);
            }));
    }

    public function test_contact_map_only_renders_iframe_embed_and_hides_plain_url(): void
    {
        $setting = WebsiteSetting::create([
            'nama_perusahaan' => 'Bina Persada JS',
            'google_maps' => 'https://maps.google.com/',
        ]);

        View::share('websiteSetting', $setting);

        $this->get(route('website.contact'))
            ->assertOk()
            ->assertSee('Lokasi Google Maps belum diatur.')
            ->assertDontSee('https://maps.google.com/');

        $setting->update([
            'google_maps' => '<iframe src="https://www.google.com/maps/embed?pb=lokasi-perusahaan" loading="lazy"></iframe>',
        ]);
        View::share('websiteSetting', $setting->fresh());

        $this->get(route('website.contact'))
            ->assertOk()
            ->assertSee('<iframe title="Lokasi PT. Bina Persada Jaya Sejahtera" src="https://www.google.com/maps/embed?pb=lokasi-perusahaan" loading="lazy"></iframe>', false)
            ->assertDontSee('Lokasi Google Maps belum diatur.');
    }

    public function test_admin_can_search_view_update_status_and_delete_contact_message(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'contact-admin@example.com',
            'password' => bcrypt('secret'),
        ]);
        $message = ContactMessage::create([
            'name' => 'PT Energi Industri',
            'email' => 'project@example.com',
            'phone' => '0812 3456 7890',
            'subject' => 'Shutdown Project',
            'message' => 'Mohon informasi ketersediaan manpower untuk pekerjaan shutdown.',
            'status' => 'unread',
        ]);
        ContactMessage::create([
            'name' => 'Pesan Lain',
            'email' => 'other@example.com',
            'message' => 'Pesan yang tidak masuk hasil pencarian admin.',
            'status' => 'read',
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.contact-messages.index', ['status' => 'unread', 'q' => 'Energi']))
            ->assertOk()
            ->assertSee('Pesan Kontak')
            ->assertSee('Belum Dibaca')
            ->assertSee('PT Energi Industri')
            ->assertDontSee('Pesan Lain');

        $this->actingAs($admin)
            ->get(route('paneladmin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee('Detail Pesan Kontak')
            ->assertSee('Shutdown Project')
            ->assertSee('Balas Email')
            ->assertSee('Riwayat Balasan Email')
            ->assertSee('Re: Shutdown Project')
            ->assertSee('https://wa.me/6281234567890', false);

        $this->actingAs($admin)
            ->patch(route('paneladmin.contact-messages.read', $message))
            ->assertRedirect()
            ->assertSessionHas('success', 'Pesan berhasil ditandai sudah dibaca.');

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'read',
        ]);

        $this->actingAs($admin)
            ->patch(route('paneladmin.contact-messages.replied', $message))
            ->assertRedirect()
            ->assertSessionHas('success', 'Pesan berhasil ditandai sudah dibalas.');

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'replied',
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.contact-messages.destroy', $message))
            ->assertRedirect(route('paneladmin.contact-messages.index'));

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_admin_can_send_email_reply_and_history_is_saved(): void
    {
        Mail::fake();
        $admin = User::create([
            'name' => 'Admin Reply',
            'email' => 'admin-reply@example.com',
            'password' => bcrypt('secret'),
        ]);
        $message = ContactMessage::create([
            'name' => 'Budi Project',
            'email' => 'budi@example.com',
            'subject' => 'Penawaran Maintenance',
            'message' => 'Kami ingin menerima penawaran maintenance.',
            'status' => 'unread',
        ]);

        $this->actingAs($admin)
            ->post(route('paneladmin.contact-messages.reply', $message), [
                'to_email' => 'budi@example.com',
                'subject' => 'Re: Penawaran Maintenance',
                'body' => 'Terima kasih. Tim kami akan menyiapkan penawaran dan menghubungi Bapak segera.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Balasan email berhasil dikirim.');

        $this->assertDatabaseHas('contact_message_replies', [
            'contact_message_id' => $message->id,
            'to_email' => 'budi@example.com',
            'subject' => 'Re: Penawaran Maintenance',
            'sent_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'replied',
        ]);

        Mail::assertSent(ContactMessageReplyMail::class, function (ContactMessageReplyMail $mail) {
            return $mail->hasTo('budi@example.com')
                && $mail->replySubject === 'Re: Penawaran Maintenance'
                && str_contains($mail->render(), 'Tim kami akan menyiapkan penawaran');
        });

        $this->actingAs($admin)
            ->get(route('paneladmin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee('Riwayat Balasan Email')
            ->assertSee('Terima kasih. Tim kami akan menyiapkan penawaran');
    }

    public function test_invalid_email_reply_does_not_send_or_store_history(): void
    {
        Mail::fake();
        $admin = User::create([
            'name' => 'Admin Validation',
            'email' => 'admin-validation@example.com',
            'password' => bcrypt('secret'),
        ]);
        $message = ContactMessage::create([
            'name' => 'Calon Klien',
            'email' => 'client@example.com',
            'message' => 'Pesan yang membutuhkan jawaban.',
            'status' => 'unread',
        ]);

        $this->actingAs($admin)
            ->from(route('paneladmin.contact-messages.show', $message))
            ->post(route('paneladmin.contact-messages.reply', $message), [
                'to_email' => 'email-tidak-valid',
                'subject' => '',
                'body' => 'pendek',
            ])
            ->assertRedirect(route('paneladmin.contact-messages.show', $message))
            ->assertSessionHasErrors(['to_email', 'subject', 'body']);

        $this->assertDatabaseCount('contact_message_replies', 0);
        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'unread',
        ]);
        Mail::assertNothingSent();
    }

    public function test_failed_reply_email_keeps_message_unreplied_without_history(): void
    {
        $admin = User::create([
            'name' => 'Admin SMTP',
            'email' => 'admin-smtp@example.com',
            'password' => bcrypt('secret'),
        ]);
        $message = ContactMessage::create([
            'name' => 'Penerima',
            'email' => 'penerima@example.com',
            'message' => 'Mohon dibalas melalui email.',
            'status' => 'unread',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with('penerima@example.com')
            ->andThrow(new \RuntimeException('SMTP balasan gagal'));
        Log::spy();

        $this->actingAs($admin)
            ->post(route('paneladmin.contact-messages.reply', $message), [
                'to_email' => 'penerima@example.com',
                'subject' => 'Re: Pesan website',
                'body' => 'Terima kasih, kami akan segera menindaklanjuti kebutuhan Anda.',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Balasan email gagal dikirim. Periksa konfigurasi SMTP.');

        $this->assertDatabaseCount('contact_message_replies', 0);
        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'unread',
        ]);
        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Failed to send contact reply email', \Mockery::on(function (array $context) {
                return $context['message'] === 'SMTP balasan gagal'
                    && ! empty($context['contact_message_id']);
            }));
    }

    public function test_admin_can_manage_contact_page_content_and_dynamic_success_message(): void
    {
        Mail::fake();

        $admin = User::create([
            'name' => 'Admin Kontak',
            'email' => 'contact-page-admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.contact-page.edit'))
            ->assertOk()
            ->assertSee('Halaman Kontak')
            ->assertSee('Sematkan Google Maps');

        $this->actingAs($admin)
            ->put(route('paneladmin.contact-page.update'), [
                'section_label' => 'Kontak Proyek',
                'heading' => 'Diskusikan Kebutuhan Anda',
                'address_title' => 'Kantor Operasional',
                'email_title' => 'Email Project',
                'phone_title' => 'Hubungi Tim',
                'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=cms-contact"></iframe>',
                'form_heading' => 'Kirim Permintaan',
                'success_message' => 'Pesan proyek berhasil diterima.',
                'submit_button_text' => 'KIRIM PERMINTAAN',
            ])
            ->assertRedirect(route('paneladmin.contact-page.edit'))
            ->assertSessionHas('success', 'Pengaturan halaman kontak berhasil disimpan.');

        $this->assertDatabaseHas('contact_page_settings', [
            'section_label' => 'Kontak Proyek',
            'submit_button_text' => 'KIRIM PERMINTAAN',
        ]);

        $this->get(route('website.contact'))
            ->assertOk()
            ->assertSee('Kontak Proyek')
            ->assertSee('Diskusikan Kebutuhan Anda')
            ->assertSee('KIRIM PERMINTAAN')
            ->assertSee('<iframe title="Lokasi PT. Bina Persada Jaya Sejahtera" loading="lazy" src="https://www.google.com/maps/embed?pb=cms-contact"></iframe>', false);

        $this->post(route('website.contact.store'), [
            'name' => 'Calon Klien',
            'email' => 'client@example.com',
            'message' => 'Kami ingin mendiskusikan kebutuhan pekerjaan lapangan.',
        ])
            ->assertRedirect()
            ->assertSessionHas('contact_success', 'Pesan proyek berhasil diterima.');
    }
}
