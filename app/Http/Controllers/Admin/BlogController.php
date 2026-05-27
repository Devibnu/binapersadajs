<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('sort_order')->latest('published_at')->latest()->get();

        return view('paneladmin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('paneladmin.blogs.create', [
            'blog' => new Blog([
                'author_name' => 'Admin',
                'published_at' => now(),
                'is_published' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $this->storeImages($request, $validated);

        $blog = Blog::create($validated);
        app(ActivityLogger::class)->log('create', 'Blogs', 'Artikel ditambahkan: ' . $blog->title, $blog);

        return redirect()
            ->route('paneladmin.blogs.index')
            ->with('success', $this->successMessage('Artikel berhasil ditambahkan.', $request));
    }

    public function edit(Blog $blog)
    {
        return view('paneladmin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $this->validatedData($request, $blog);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title'], $blog);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $this->storeImages($request, $validated, $blog);

        $blog->update($validated);
        app(ActivityLogger::class)->log('update', 'Blogs', 'Artikel diperbarui: ' . $blog->title, $blog);

        return redirect()
            ->route('paneladmin.blogs.index')
            ->with('success', $this->successMessage('Artikel berhasil diperbarui.', $request));
    }

    public function destroy(Blog $blog)
    {
        app(ActivityLogger::class)->log('delete', 'Blogs', 'Artikel dihapus: ' . $blog->title, $blog);
        ImageUploadHelper::deleteStoredImage($blog->featured_image);
        ImageUploadHelper::deleteStoredImage($blog->og_image);
        $blog->delete();

        return redirect()
            ->route('paneladmin.blogs.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $path = ImageUploadHelper::uploadAndCompress(
            $request->file('file'),
            'blogs/content',
            1600
        );

        return response()->json([
            'location' => asset(Storage::url($path)),
        ]);
    }

    private function validatedData(Request $request, ?Blog $blog = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($blog),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'category' => ['required', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:500'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);
    }

    private function slugFor(?string $slug, string $title, ?Blog $blog = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'artikel';
        $candidate = $baseSlug;
        $counter = 1;

        while (
            Blog::where('slug', $candidate)
                ->when($blog, fn ($query) => $query->whereKeyNot($blog->getKey()))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter++;
        }

        return $candidate;
    }

    private function storeImages(Request $request, array &$validated, ?Blog $blog = null): void
    {
        if ($request->hasFile('featured_image')) {
            ImageUploadHelper::deleteStoredImage($blog?->featured_image);
            $validated['featured_image'] = ImageUploadHelper::uploadAndCompress(
                $request->file('featured_image'),
                'blogs',
                1600
            );
        } else {
            unset($validated['featured_image']);
        }

        if ($request->hasFile('og_image')) {
            ImageUploadHelper::deleteStoredImage($blog?->og_image);
            $validated['og_image'] = ImageUploadHelper::uploadAndCompress(
                $request->file('og_image'),
                'blogs/og',
                1600
            );
        } else {
            unset($validated['og_image']);
        }
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('featured_image') || $request->hasFile('og_image')
            ? $message . ' Gambar berhasil diupload dan dioptimasi.'
            : $message;
    }
}
