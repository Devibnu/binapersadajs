<?php

namespace Tests\Feature;

use App\Mail\NewContactMessageMail;
use App\Mail\SmtpTestMail;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailSettingTest extends TestCase
{
    use RefreshDatabase;

    private array $originalMailConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalMailConfig = config('mail');
    }

    protected function tearDown(): void
    {
        config(['mail' => $this->originalMailConfig]);

        parent::tearDown();
    }

    public function test_admin_can_save_smtp_setting_with_encrypted_password_and_apply_runtime_config(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.email-settings.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Email SMTP')
            ->assertSee('Kirim Test Email');

        $this->actingAs($admin)
            ->put(route('paneladmin.email-settings.update'), $this->settingData([
                'password' => 'app-password-rahasia',
            ]))
            ->assertRedirect(route('paneladmin.email-settings.edit'))
            ->assertSessionHas('success', 'Pengaturan SMTP berhasil disimpan.');

        $storedPassword = DB::table('email_settings')->value('password');
        $setting = EmailSetting::query()->firstOrFail();

        $this->assertNotSame('app-password-rahasia', $storedPassword);
        $this->assertSame('app-password-rahasia', $setting->password);
        $this->assertTrue($setting->is_active);

        $setting->applyConfiguration();

        $this->assertSame('smtp.gmail.com', config('mail.mailers.smtp.host'));
        $this->assertSame(587, config('mail.mailers.smtp.port'));
        $this->assertSame('smtp-user@binapersadajs.co.id', config('mail.mailers.smtp.username'));
        $this->assertSame('app-password-rahasia', config('mail.mailers.smtp.password'));
        $this->assertSame('noreply@binapersadajs.co.id', config('mail.from.address'));

        $this->actingAs($admin)
            ->get(route('paneladmin.email-settings.edit'))
            ->assertOk()
            ->assertDontSee('app-password-rahasia');
    }

    public function test_contact_notification_uses_active_database_mail_configuration(): void
    {
        Mail::fake();
        EmailSetting::create($this->settingData([
            'password' => 'database-smtp-password',
            'from_address' => 'smtp-fallback@binapersadajs.co.id',
        ]));

        $this->post(route('website.contact.store'), [
            'name' => 'Pemohon Project',
            'email' => 'pemohon@example.com',
            'message' => 'Kami ingin menanyakan dukungan tenaga untuk project.',
        ])->assertRedirect();

        $this->assertSame('smtp.gmail.com', config('mail.mailers.smtp.host'));
        $this->assertSame('smtp-fallback@binapersadajs.co.id', config('mail.from.address'));

        Mail::assertSent(NewContactMessageMail::class, function (NewContactMessageMail $mail) {
            return $mail->hasTo('smtp-fallback@binapersadajs.co.id');
        });
    }

    public function test_admin_can_send_smtp_test_email(): void
    {
        Mail::fake();
        $admin = $this->admin();
        EmailSetting::create($this->settingData(['password' => 'smtp-password']));

        $this->actingAs($admin)
            ->post(route('paneladmin.email-settings.test'), [
                'test_email' => 'tester@example.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'SMTP berhasil digunakan.');

        Mail::assertSent(SmtpTestMail::class, function (SmtpTestMail $mail) {
            return $mail->hasTo('tester@example.com');
        });
    }

    public function test_smtp_test_failure_returns_error_without_exposing_password(): void
    {
        $admin = $this->admin();
        EmailSetting::create($this->settingData(['password' => 'smtp-password']));
        Mail::shouldReceive('to')
            ->once()
            ->with('tester@example.com')
            ->andThrow(new \RuntimeException('Connection timeout'));
        Log::spy();

        $this->actingAs($admin)
            ->post(route('paneladmin.email-settings.test'), [
                'test_email' => 'tester@example.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'SMTP gagal. Periksa host, akun, password, dan encryption.');

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Failed to send SMTP test email', ['message' => 'Connection timeout']);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin SMTP',
            'email' => 'admin-smtp-settings@example.com',
            'password' => bcrypt('secret'),
        ]);
    }

    private function settingData(array $overrides = []): array
    {
        return array_merge([
            'mailer' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'smtp-user@binapersadajs.co.id',
            'password' => 'password-default',
            'encryption' => 'tls',
            'from_address' => 'noreply@binapersadajs.co.id',
            'from_name' => 'Bina Persada JS',
            'is_active' => true,
        ], $overrides);
    }
}
