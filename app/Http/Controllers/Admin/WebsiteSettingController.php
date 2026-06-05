<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use App\Services\PwaIconGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
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
        $logoWasUploaded = $request->hasFile('logo');
        $oldFiles = [];
        $pwaIconWarning = null;

        File::ensureDirectoryExists(Storage::disk('public')->path('settings'));
        File::ensureDirectoryExists(public_path('icons'));

        try {
            foreach (['logo', 'favicon'] as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $uploadedFile = $request->file($field);
                Log::info("Uploading {$field}...", [
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'size' => $uploadedFile->getSize(),
                ]);

                if ($field === 'favicon' && $this->isIcoFile($uploadedFile)) {
                    Log::info('Favicon is .ico, storing without resize or image processing.');
                }

                $oldFiles[$field] = $setting->{$field};
                $validated[$field] = $uploadedFile->store('settings', 'public');
            }
        } catch (\Throwable $e) {
            Log::error('Website settings file upload failed.', [
                'exception' => $e,
            ]);

            return redirect()
                ->route('paneladmin.settings.edit')
                ->withInput()
                ->with('error', 'Upload logo/favicon gagal. Pengaturan belum disimpan. Silakan cek file dan coba lagi.');
        }

        $setting->fill($validated);

        try {
            $setting->save();
        } catch (\Throwable $e) {
            foreach (array_intersect_key($validated, array_flip(['logo', 'favicon'])) as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Website settings save failed.', [
                'exception' => $e,
            ]);

            return redirect()
                ->route('paneladmin.settings.edit')
                ->withInput()
                ->with('error', 'Pengaturan website gagal disimpan. Silakan coba lagi.');
        }

        foreach ($oldFiles as $field => $oldPath) {
            if ($oldPath && $oldPath !== $setting->{$field}) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        if ($logoWasUploaded) {
            Log::info('Generating PWA icons...', [
                'logo' => $setting->logo,
            ]);

            try {
                app(PwaIconGenerator::class)->generateFromWebsiteSetting($setting);
            } catch (\Throwable $e) {
                Log::error('PWA icon generation failed after website settings update.', [
                    'exception' => $e,
                    'logo' => $setting->logo,
                ]);

                $pwaIconWarning = 'Pengaturan berhasil disimpan, tetapi icon PWA gagal dibuat ulang. Icon PWA lama tetap digunakan.';
            }
        }

        app(ActivityLogger::class)->log('update', 'Website Settings', 'Pengaturan website diperbarui.', $setting);

        $redirect = redirect()
            ->route('paneladmin.settings.edit')
            ->with('success', 'Pengaturan website berhasil disimpan.');

        return $pwaIconWarning ? $redirect->with('warning', $pwaIconWarning) : $redirect;
    }

    private function isIcoFile(UploadedFile $file): bool
    {
        return strtolower($file->getClientOriginalExtension()) === 'ico'
            || in_array($file->getClientMimeType(), ['image/x-icon', 'image/vnd.microsoft.icon'], true);
    }
}
