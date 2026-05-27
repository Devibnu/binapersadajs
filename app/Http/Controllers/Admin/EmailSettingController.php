<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SmtpTestMail;
use App\Models\EmailSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailSettingController extends Controller
{
    public function edit(): View
    {
        return view('paneladmin.email-settings.edit', [
            'setting' => EmailSetting::query()->firstOrNew([
                'mailer' => 'smtp',
                'port' => 587,
                'encryption' => 'tls',
                'is_active' => true,
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = EmailSetting::query()->firstOrNew();
        $validated = $this->validateSetting($request);

        if (! $setting->exists && ! $request->filled('password')) {
            return back()->withInput()->withErrors([
                'password' => 'Password SMTP wajib diisi saat membuat pengaturan.',
            ]);
        }

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $setting->fill($validated);
        $setting->save();

        if ($setting->is_active) {
            $setting->applyConfiguration();
        }

        app(ActivityLogger::class)->log('update', 'Email Settings', 'Pengaturan SMTP diperbarui.', $setting, [
            'host' => $setting->host,
            'port' => $setting->port,
            'from_address' => $setting->from_address,
            'is_active' => $setting->is_active,
        ]);

        return redirect()
            ->route('paneladmin.email-settings.edit')
            ->with('success', 'Pengaturan SMTP berhasil disimpan.');
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);
        $setting = EmailSetting::active();

        if (! $setting) {
            return back()->with('error', 'Aktifkan dan simpan pengaturan SMTP terlebih dahulu.');
        }

        try {
            $setting->applyConfiguration();
            Mail::to($validated['test_email'])->send(new SmtpTestMail($setting));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send SMTP test email', [
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'SMTP gagal. Periksa host, akun, password, dan encryption.');
        }

        app(ActivityLogger::class)->log('email', 'Email Settings', 'Email pengujian SMTP berhasil dikirim.', $setting, [
            'host' => $setting->host,
            'port' => $setting->port,
            'from_address' => $setting->from_address,
            'is_active' => $setting->is_active,
        ]);

        return back()->with('success', 'SMTP berhasil digunakan.');
    }

    private function validateSetting(Request $request): array
    {
        return $request->validate([
            'mailer' => ['required', 'in:smtp'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:1000'],
            'encryption' => ['nullable', 'in:tls,ssl'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
        ]);
    }
}
