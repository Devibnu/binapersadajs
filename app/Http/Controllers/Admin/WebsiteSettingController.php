<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        return view('paneladmin.settings.edit', [
            'setting' => WebsiteSetting::firstOrNew(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => ['nullable', 'string', 'max:255'],
            'deskripsi_perusahaan' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'email' => ['nullable', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'google_maps' => ['nullable', 'string'],
            'certificate_label' => ['nullable', 'string', 'max:255'],
            'certificate_value' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
        ]);

        $setting = WebsiteSetting::firstOrNew();

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                if ($setting->{$field}) {
                    Storage::disk('public')->delete($setting->{$field});
                }

                $validated[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        $setting->fill($validated);
        $setting->save();
        app(ActivityLogger::class)->log('update', 'Website Settings', 'Pengaturan website diperbarui.', $setting);

        return redirect()
            ->route('paneladmin.settings.edit')
            ->with('success', 'Pengaturan website berhasil disimpan.');
    }
}
