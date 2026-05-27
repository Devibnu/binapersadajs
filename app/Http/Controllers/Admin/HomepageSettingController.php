<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSettingController extends Controller
{
    public function edit(): View
    {
        return view('paneladmin.homepage-sections.edit', [
            'setting' => HomepageSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_label' => ['nullable', 'string', 'max:255'],
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_description' => ['nullable', 'string'],
            'about_feature_1_title' => ['nullable', 'string', 'max:255'],
            'about_feature_1_icon' => ['nullable', 'string', 'max:100'],
            'about_feature_2_title' => ['nullable', 'string', 'max:255'],
            'about_feature_2_icon' => ['nullable', 'string', 'max:100'],
            'about_feature_3_title' => ['nullable', 'string', 'max:255'],
            'about_feature_3_icon' => ['nullable', 'string', 'max:100'],
            'about_feature_4_title' => ['nullable', 'string', 'max:255'],
            'about_feature_4_icon' => ['nullable', 'string', 'max:100'],
            'values_title' => ['nullable', 'string', 'max:255'],
            'values_description' => ['nullable', 'string'],
            'value_1_title' => ['nullable', 'string', 'max:255'],
            'value_1_description' => ['nullable', 'string'],
            'value_2_title' => ['nullable', 'string', 'max:255'],
            'value_2_description' => ['nullable', 'string'],
            'value_3_title' => ['nullable', 'string', 'max:255'],
            'value_3_description' => ['nullable', 'string'],
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
            'service_section_label' => ['nullable', 'string', 'max:255'],
            'service_section_title' => ['nullable', 'string', 'max:255'],
            'quality_title' => ['nullable', 'string', 'max:255'],
            'quality_description' => ['nullable', 'string'],
            'quality_sub_description' => ['nullable', 'string'],
            'quality_item_1' => ['nullable', 'string', 'max:255'],
            'quality_item_2' => ['nullable', 'string', 'max:255'],
            'quality_item_3' => ['nullable', 'string', 'max:255'],
            'quality_item_4' => ['nullable', 'string', 'max:255'],
            'cta_phone_label' => ['nullable', 'string', 'max:255'],
            'cta_phone' => ['nullable', 'string', 'max:50'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string'],
            'cta_button_text' => ['nullable', 'string', 'max:100'],
            'cta_button_link' => ['nullable', 'string', 'max:255'],
            'blog_label' => ['nullable', 'string', 'max:255'],
            'blog_title' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = HomepageSetting::query()->firstOrNew();
        $setting->fill($validated);
        $setting->save();
        app(ActivityLogger::class)->log('update', 'Homepage Sections', 'Section homepage diperbarui.', $setting);

        return redirect()
            ->route('paneladmin.homepage-sections.edit')
            ->with('success', 'Section homepage berhasil disimpan.');
    }
}
