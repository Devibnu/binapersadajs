<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutVideo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AboutVideoController extends Controller
{
    public function index()
    {
        $aboutVideos = AboutVideo::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('paneladmin.about-videos.index', compact('aboutVideos'));
    }

    public function create()
    {
        return view('paneladmin.about-videos.create', [
            'aboutVideo' => new AboutVideo([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['youtube_id'] = $this->youtubeIdFromUrl($validated['youtube_url']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeThumbnail($request, $validated);

        $aboutVideo = AboutVideo::create($validated);
        app(ActivityLogger::class)->log('create', 'About Videos', 'Video about ditambahkan: ' . $aboutVideo->displayTitle(), $aboutVideo);

        return redirect()
            ->route('paneladmin.about-videos.index')
            ->with('success', $this->successMessage('Video About berhasil ditambahkan.', $request));
    }

    public function show(AboutVideo $aboutVideo)
    {
        return view('paneladmin.about-videos.show', compact('aboutVideo'));
    }

    public function edit(AboutVideo $aboutVideo)
    {
        return view('paneladmin.about-videos.edit', compact('aboutVideo'));
    }

    public function update(Request $request, AboutVideo $aboutVideo)
    {
        $validated = $this->validatedData($request);
        $validated['youtube_id'] = $this->youtubeIdFromUrl($validated['youtube_url']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeThumbnail($request, $validated, $aboutVideo);

        $aboutVideo->update($validated);
        app(ActivityLogger::class)->log('update', 'About Videos', 'Video about diperbarui: ' . $aboutVideo->displayTitle(), $aboutVideo);

        return redirect()
            ->route('paneladmin.about-videos.index')
            ->with('success', $this->successMessage('Video About berhasil diperbarui.', $request));
    }

    public function destroy(AboutVideo $aboutVideo)
    {
        app(ActivityLogger::class)->log('delete', 'About Videos', 'Video about dihapus: ' . $aboutVideo->displayTitle(), $aboutVideo);
        ImageUploadHelper::deleteStoredImage($aboutVideo->thumbnail);
        $aboutVideo->delete();

        return redirect()
            ->route('paneladmin.about-videos.index')
            ->with('success', 'Video About berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['required', 'url', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        if (! $this->youtubeIdFromUrl($validated['youtube_url'])) {
            return back()
                ->withErrors(['youtube_url' => 'URL YouTube tidak valid atau video ID tidak ditemukan.'])
                ->withInput()
                ->throwResponse();
        }

        return $validated;
    }

    private function storeThumbnail(Request $request, array &$validated, ?AboutVideo $aboutVideo = null): void
    {
        if (! $request->hasFile('thumbnail')) {
            unset($validated['thumbnail']);

            return;
        }

        if ($aboutVideo?->thumbnail) {
            ImageUploadHelper::deleteStoredImage($aboutVideo->thumbnail);
        }

        $validated['thumbnail'] = ImageUploadHelper::uploadAndCompress(
            $request->file('thumbnail'),
            'about-videos',
            900
        );
    }

    private function youtubeIdFromUrl(string $url): ?string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if (str_contains($host, 'youtu.be')) {
            return $this->cleanYoutubeId(explode('/', $path)[0] ?? null);
        }

        if (str_contains($host, 'youtube.com')) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);

                return $this->cleanYoutubeId($query['v'] ?? null);
            }

            if (str_starts_with($path, 'embed/')) {
                return $this->cleanYoutubeId(explode('/', substr($path, 6))[0] ?? null);
            }
        }

        return null;
    }

    private function cleanYoutubeId(?string $value): ?string
    {
        $value = trim((string) $value);

        return preg_match('/^[A-Za-z0-9_-]{6,20}$/', $value) ? $value : null;
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('thumbnail')
            ? $message . ' Thumbnail berhasil diupload.'
            : $message;
    }
}
