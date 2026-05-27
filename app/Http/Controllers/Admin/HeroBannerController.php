<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HeroBannerController extends Controller
{
    public function index()
    {
        $heroBanners = HeroBanner::orderBy('sort_order')
            ->latest()
            ->get();

        return view('paneladmin.hero-banners.index', compact('heroBanners'));
    }

    public function create()
    {
        return view('paneladmin.hero-banners.create', [
            'heroBanner' => new HeroBanner([
                'is_active' => true,
                'sort_order' => 0,
                'content_position' => null,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $validated['image'] = ImageUploadHelper::uploadAndCompress($request->file('image'), 'hero-banners', 1600);
        }

        $heroBanner = HeroBanner::create($this->withLegacyFields($validated));
        app(ActivityLogger::class)->log('create', 'Hero Banner', 'Hero banner ditambahkan: ' . $heroBanner->title, $heroBanner);

        return redirect()
            ->route('paneladmin.hero-banners.index')
            ->with('success', $this->successMessage('Hero banner berhasil ditambahkan.', $request->hasFile('image')));
    }

    public function edit(HeroBanner $heroBanner)
    {
        return view('paneladmin.hero-banners.edit', compact('heroBanner'));
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('image')) {
            foreach (array_filter([$heroBanner->image, $heroBanner->gambar_background]) as $oldImage) {
                ImageUploadHelper::deleteStoredImage($oldImage);
            }

            $validated['image'] = ImageUploadHelper::uploadAndCompress($request->file('image'), 'hero-banners', 1600);
        }

        $heroBanner->update($this->withLegacyFields($validated));
        app(ActivityLogger::class)->log('update', 'Hero Banner', 'Hero banner diperbarui: ' . $heroBanner->title, $heroBanner);

        return redirect()
            ->route('paneladmin.hero-banners.index')
            ->with('success', $this->successMessage('Hero banner berhasil diperbarui.', $request->hasFile('image')));
    }

    public function destroy(HeroBanner $heroBanner)
    {
        app(ActivityLogger::class)->log('delete', 'Hero Banner', 'Hero banner dihapus: ' . $heroBanner->title, $heroBanner);
        foreach (array_filter([$heroBanner->image, $heroBanner->gambar_background]) as $image) {
            ImageUploadHelper::deleteStoredImage($image);
        }

        $heroBanner->delete();

        return redirect()
            ->route('paneladmin.hero-banners.index')
            ->with('success', 'Hero banner berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'small_text' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'content_position' => ['nullable', 'in:center,left,right'],
        ]);
    }

    private function withLegacyFields(array $data): array
    {
        $legacyMap = [
            'judul' => 'title',
            'sub_judul' => 'small_text',
            'teks_tombol' => 'button_text',
            'link_tombol' => 'button_link',
            'gambar_background' => 'image',
            'status_aktif' => 'is_active',
            'urutan' => 'sort_order',
        ];

        foreach ($legacyMap as $legacyColumn => $newColumn) {
            if (Schema::hasColumn('hero_banners', $legacyColumn) && array_key_exists($newColumn, $data)) {
                $data[$legacyColumn] = $data[$newColumn];
            }
        }

        return $data;
    }

    private function successMessage(string $message, bool $optimizedImage): string
    {
        return $optimizedImage ? $message . ' Gambar berhasil diupload dan dioptimasi.' : $message;
    }
}
