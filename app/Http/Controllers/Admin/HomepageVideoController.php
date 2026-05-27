<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\HomepageVideo;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageVideoController extends Controller
{
    public function index(): View
    {
        return $this->edit();
    }

    public function edit(): View
    {
        return view('paneladmin.homepage-video.edit', [
            'setting' => HomepageVideo::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_label' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'youtube_url' => [
                'required',
                'url',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! HomepageVideo::videoIdFromUrl($value)) {
                        $fail('Link YouTube harus berupa URL video YouTube yang valid.');
                    }
                },
            ],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'url', 'max:255'],
            'feature_1' => ['nullable', 'string', 'max:255'],
            'feature_2' => ['nullable', 'string', 'max:255'],
            'feature_3' => ['nullable', 'string', 'max:255'],
            'feature_4' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ], [
            'youtube_url.required' => 'Link YouTube wajib diisi.',
            'youtube_url.url' => 'Link YouTube harus berupa URL yang valid.',
            'thumbnail_image.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail_image.mimes' => 'Thumbnail harus berformat JPG, JPEG, PNG, atau WEBP.',
            'thumbnail_image.max' => 'Ukuran thumbnail maksimal 10MB.',
        ]);

        $setting = HomepageVideo::query()->firstOrNew();
        $optimizedImage = false;

        if ($request->hasFile('thumbnail_image')) {
            ImageUploadHelper::deleteStoredImage($setting->thumbnail_image);
            $validated['thumbnail_image'] = ImageUploadHelper::uploadAndCompress(
                $request->file('thumbnail_image'),
                'homepage-video',
                1600
            );
            $optimizedImage = true;
        } else {
            unset($validated['thumbnail_image']);
        }

        $setting->fill($validated);
        $setting->save();

        app(ActivityLogger::class)->log('update', 'Homepage Video', 'Video company profile homepage diperbarui.', $setting);

        $message = 'Homepage Video berhasil disimpan.';

        if ($optimizedImage) {
            $message .= ' Thumbnail berhasil diupload dan dioptimasi.';
        }

        return redirect()
            ->route('paneladmin.homepage-video.edit')
            ->with('success', $message);
    }
}
