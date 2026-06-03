<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BrandedTemplateMail;
use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('paneladmin.email-templates.edit');
    }

    public function edit(): View
    {
        return view('paneladmin.email-templates.edit', [
            'template' => EmailTemplate::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $template = EmailTemplate::query()->first() ?: new EmailTemplate(EmailTemplate::defaults());
        $wasNew = ! $template->exists;
        $validated = $this->validateTemplate($request);

        foreach (['logo', 'header_background', 'footer_background'] as $field) {
            if ($request->hasFile($field)) {
                if (filled($template->{$field}) && str_starts_with($template->{$field}, 'email-templates/')) {
                    Storage::disk('public')->delete($template->{$field});
                }

                $validated[$field] = $request->file($field)->store('email-templates', 'public');
            }
        }

        $template->fill($validated);
        $template->save();

        app(ActivityLogger::class)->log(
            $wasNew ? 'create' : 'update',
            'Email Template',
            $wasNew ? 'Admin membuat Email Template.' : 'Admin memperbarui Email Template.',
            $template
        );

        return back()->with('success', 'Email Template berhasil disimpan.');
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
            'test_subject' => ['required', 'string', 'max:150'],
            'test_body' => ['required', 'string', 'min:5'],
        ]);

        $emailSetting = EmailSetting::active();
        if (! $emailSetting) {
            return back()->withInput()->with('error', 'Aktifkan dan simpan konfigurasi SMTP terlebih dahulu.');
        }

        try {
            $emailSetting->applyConfiguration();
            Mail::to($validated['test_email'])->send(new BrandedTemplateMail(
                $validated['test_subject'],
                nl2br(e($validated['test_body'])),
                EmailTemplate::current()
            ));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send email template test', [
                'message' => $exception->getMessage(),
                'to' => $validated['test_email'],
            ]);

            return back()->withInput()->with('error', 'Test email gagal dikirim. Periksa konfigurasi SMTP dan alamat tujuan.');
        }

        app(ActivityLogger::class)->log('test_email', 'Email Template', 'Admin mengirim Test Email ke ' . $validated['test_email'] . '.', EmailTemplate::current(), [
            'to_email' => $validated['test_email'],
            'subject' => $validated['test_subject'],
        ]);

        return back()->with('success', 'Test email berhasil dikirim.');
    }

    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'sender_email' => ['required', 'email', 'max:255'],
            'sender_name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'header_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'footer_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'header_color' => ['required', 'string', 'max:20'],
            'footer_color' => ['required', 'string', 'max:20'],
            'button_color' => ['required', 'string', 'max:20'],
            'text_color' => ['required', 'string', 'max:20'],
            'header_html' => ['nullable', 'string'],
            'footer_html' => ['nullable', 'string'],
            'disclaimer_html' => ['nullable', 'string'],
        ]);
    }
}
