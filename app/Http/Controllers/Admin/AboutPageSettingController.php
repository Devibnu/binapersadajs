<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutPageSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutPageSettingController extends Controller
{
    public function edit(): View
    {
        return view('paneladmin.about-page.edit', [
            'setting' => AboutPageSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_label' => ['nullable', 'string', 'max:255'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'section_description' => ['nullable', 'string'],
            'quote_text' => ['nullable', 'string'],
            'section_description_bottom' => ['nullable', 'string'],
            'slider_1_title' => ['nullable', 'string', 'max:255'],
            'slider_1_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'slider_2_title' => ['nullable', 'string', 'max:255'],
            'slider_2_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'slider_3_title' => ['nullable', 'string', 'max:255'],
            'slider_3_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'counter_1_number' => ['nullable', 'string', 'max:30'],
            'counter_1_label' => ['nullable', 'string', 'max:255'],
            'counter_1_icon' => ['nullable', 'string', 'max:100'],
            'counter_2_number' => ['nullable', 'string', 'max:30'],
            'counter_2_label' => ['nullable', 'string', 'max:255'],
            'counter_2_icon' => ['nullable', 'string', 'max:100'],
            'counter_3_number' => ['nullable', 'string', 'max:30'],
            'counter_3_label' => ['nullable', 'string', 'max:255'],
            'counter_3_icon' => ['nullable', 'string', 'max:100'],
            'counter_4_number' => ['nullable', 'string', 'max:30'],
            'counter_4_label' => ['nullable', 'string', 'max:255'],
            'counter_4_icon' => ['nullable', 'string', 'max:100'],
            'team_label' => ['nullable', 'string', 'max:255'],
            'team_title' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = AboutPageSetting::query()->firstOrNew();
        $optimizedImage = false;

        foreach ($this->imageFields() as $field => $folder) {
            if (! $request->hasFile($field)) {
                unset($validated[$field]);

                continue;
            }

            ImageUploadHelper::deleteStoredImage($setting->{$field});
            $validated[$field] = ImageUploadHelper::uploadAndCompress(
                $request->file($field),
                $folder,
                1200
            );
            $optimizedImage = true;
        }

        $setting->fill($validated);
        $setting->save();
        app(ActivityLogger::class)->log('update', 'About Page', 'Konten halaman About diperbarui.', $setting);

        $message = 'Pengaturan halaman About berhasil disimpan.';

        if ($optimizedImage) {
            $message .= ' Gambar berhasil diupload dan dioptimasi.';
        }

        return redirect()
            ->route('paneladmin.about-page.edit')
            ->with('success', $message);
    }

    private function imageFields(): array
    {
        return [
            'slider_1_image' => 'about-page/sliders',
            'slider_2_image' => 'about-page/sliders',
            'slider_3_image' => 'about-page/sliders',
        ];
    }
}
