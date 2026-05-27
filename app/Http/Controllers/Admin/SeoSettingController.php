<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoSettingController extends Controller
{
    public function edit(): View
    {
        return view('paneladmin.seo-settings.edit', [
            'setting' => SeoSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_index' => ['required', 'boolean'],
            'robots_follow' => ['required', 'boolean'],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:40', 'regex:/^G-[A-Z0-9]+$/i'],
            'google_tag_manager' => ['nullable', 'string', 'max:40', 'regex:/^GTM-[A-Z0-9]+$/i'],
            'schema_company_name' => ['nullable', 'string', 'max:255'],
            'schema_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'schema_phone' => ['nullable', 'string', 'max:50'],
            'schema_email' => ['nullable', 'email', 'max:255'],
            'schema_address' => ['nullable', 'string'],
            'schema_city' => ['nullable', 'string', 'max:100'],
            'schema_country' => ['nullable', 'string', 'max:100'],
            'schema_postal_code' => ['nullable', 'string', 'max:20'],
            'twitter_card_type' => ['nullable', 'in:summary,summary_large_image'],
            'twitter_site' => ['nullable', 'string', 'max:100'],
        ], [
            'google_analytics_id.regex' => 'Google Analytics ID harus memakai format GA4, contoh: G-XXXXXXXXXX.',
            'google_tag_manager.regex' => 'Google Tag Manager ID harus memakai format GTM-XXXXXXX.',
        ]);

        $setting = SeoSetting::query()->firstOrNew();

        foreach (['og_image', 'schema_logo'] as $field) {
            if ($request->hasFile($field)) {
                ImageUploadHelper::deleteStoredImage($setting->{$field});
                $validated[$field] = ImageUploadHelper::uploadAndCompress(
                    $request->file($field),
                    'seo',
                    1600
                );
            } else {
                unset($validated[$field]);
            }
        }

        $setting->fill($validated);
        $setting->save();
        app(ActivityLogger::class)->log('update', 'SEO Settings', 'Pengaturan SEO diperbarui.', $setting);

        $message = 'Pengaturan SEO berhasil disimpan.';

        if ($request->hasFile('og_image') || $request->hasFile('schema_logo')) {
            $message .= ' Gambar berhasil diupload dan dioptimasi.';
        }

        return redirect()
            ->route('paneladmin.seo-settings.edit')
            ->with('success', $message);
    }
}
