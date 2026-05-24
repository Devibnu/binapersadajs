<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\PageHero;
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

        PageHero::create($validated);

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

        return redirect()
            ->route('paneladmin.page-heroes.index')
            ->with('success', $this->successMessage('Page hero berhasil diperbarui.', $request->hasFile('background_image')));
    }

    public function destroy(PageHero $pageHero)
    {
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
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'overlay_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'text_position' => ['nullable', Rule::in(['center', 'left', 'right'])],
            'is_active' => ['required', 'boolean'],
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

        $validated['background_image'] = ImageUploadHelper::uploadAndCompress($request->file('background_image'), 'page-heroes', 1600);
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
