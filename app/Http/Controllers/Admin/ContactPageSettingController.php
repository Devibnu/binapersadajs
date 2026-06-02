<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactPageSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactPageSettingController extends Controller
{
    public function edit(): View
    {
        return view('paneladmin.contact-page.edit', [
            'setting' => ContactPageSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_label' => ['nullable', 'string', 'max:255'],
            'heading' => ['nullable', 'string', 'max:255'],
            'address_title' => ['nullable', 'string', 'max:255'],
            'email_title' => ['nullable', 'string', 'max:255'],
            'phone_title' => ['nullable', 'string', 'max:255'],
            'form_heading' => ['nullable', 'string', 'max:255'],
            'success_message' => ['nullable', 'string', 'max:500'],
            'submit_button_text' => ['nullable', 'string', 'max:100'],
        ]);

        $setting = ContactPageSetting::query()->firstOrNew();
        $setting->fill($validated);
        $setting->save();
        app(ActivityLogger::class)->log('update', 'Contact Page', 'Pengaturan halaman kontak diperbarui.', $setting);

        return redirect()
            ->route('paneladmin.contact-page.edit')
            ->with('success', 'Pengaturan halaman kontak berhasil disimpan.');
    }
}
