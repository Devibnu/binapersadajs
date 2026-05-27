<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\PageHero;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageHeroController extends Controller
{
    public function index()
    {
        $pageHeroes = PageHero::orderBy('page_key')->get();

        return view('paneladmin.page-heroes.index', compact('pageHeroes'));
    }

    public function create()
    {
        return view('paneladmin.page-heroes.create', [
            'pageHero' => new PageHero([
                'overlay_opacity' => 1,
                'text_position' => 'center',
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $this->storeBackgroundImage($request, $validated);

        $pageHero = PageHero::create($validated);
        app(ActivityLogger::class)->log('create', 'Page Heroes', 'Page hero ditambahkan: ' . $pageHero->page_key, $pageHero);

        return redirect()
            ->route('paneladmin.page-heroes.index')
            ->with('success', $this->successMessage('Page hero berhasil ditambahkan.', $request->hasFile('background_image')));
    }

    public function edit(PageHero $pageHero)
    {
        return view('paneladmin.page-heroes.edit', compact('pageHero'));
    }

    public function update(Request $request, PageHero $pageHero)
    {
        $validated = $this->validatedData($request, $pageHero);
        $this->storeBackgroundImage($request, $validated, $pageHero);

        $pageHero->update($validated);
        app(ActivityLogger::class)->log('update', 'Page Heroes', 'Page hero diperbarui: ' . $pageHero->page_key, $pageHero);

        return redirect()
            ->route('paneladmin.page-heroes.index')
            ->with('success', $this->successMessage('Page hero berhasil diperbarui.', $request->hasFile('background_image')));
    }

    public function destroy(PageHero $pageHero)
    {
        app(ActivityLogger::class)->log('delete', 'Page Heroes', 'Page hero dihapus: ' . $pageHero->page_key, $pageHero);
        if ($pageHero->background_image && ! $this->isPublicAsset($pageHero->background_image)) {
            ImageUploadHelper::deleteStoredImage($pageHero->background_image);
        }

        $pageHero->delete();

        return redirect()
            ->route('paneladmin.page-heroes.index')
            ->with('success', 'Page hero berhasil dihapus.');
    }

    private function validatedData(Request $request, ?PageHero $pageHero = null): array
    {
        return $request->validate([
            'page_key' => [
                'required',
                Rule::in(['services', 'projects', 'about', 'contact', 'blog']),
                Rule::unique('page_heroes', 'page_key')->ignore($pageHero),
            ],
            'title' => ['required', 'string', 'max:255'],
            'breadcrumb_text' => ['nullable', 'string', 'max:255'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'overlay_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'text_position' => ['nullable', Rule::in(['center', 'left', 'right'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'background_image.uploaded' => 'Gambar gagal diunggah. Ukuran gambar terlalu besar. Maksimal 20MB.',
            'background_image.image' => 'File background harus berupa gambar.',
            'background_image.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'background_image.max' => 'Ukuran gambar terlalu besar. Maksimal 20MB.',
        ]);
    }

    private function storeBackgroundImage(Request $request, array &$validated, ?PageHero $pageHero = null): void
    {
        if (! $request->hasFile('background_image')) {
            unset($validated['background_image']);

            return;
        }

        if ($pageHero?->background_image && ! $this->isPublicAsset($pageHero->background_image)) {
            ImageUploadHelper::deleteStoredImage($pageHero->background_image);
        }

        $validated['background_image'] = ImageUploadHelper::uploadAndCompress(
            $request->file('background_image'),
            'page-heroes',
            1920,
            80
        );
    }

    private function isPublicAsset(string $path): bool
    {
        return str_starts_with($path, 'web/')
            || str_starts_with($path, 'assets/')
            || str_starts_with($path, '/');
    }

    private function successMessage(string $message, bool $optimizedImage): string
    {
        return $optimizedImage ? $message . ' Gambar berhasil diupload dan dioptimasi.' : $message;
    }
}
