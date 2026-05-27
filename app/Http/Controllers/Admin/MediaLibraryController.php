<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $type = in_array($request->query('type'), ['images', 'documents'], true)
            ? (string) $request->query('type')
            : 'all';

        $mediaFiles = MediaFile::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('original_name', 'like', '%' . $search . '%')
                        ->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('alt_text', 'like', '%' . $search . '%');
                });
            })
            ->when($type === 'images', fn ($query) => $query->where('mime_type', 'like', 'image/%'))
            ->when($type === 'documents', fn ($query) => $query->where('mime_type', 'not like', 'image/%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('paneladmin.media-library.index', compact('mediaFiles', 'search', 'type'));
    }

    public function create(): View
    {
        return view('paneladmin.media-library.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $upload = $request->file('file');
        $folder = 'media-library/' . now()->format('Y/m');
        $isImage = str_starts_with((string) $upload->getMimeType(), 'image/');

        $path = $isImage
            ? ImageUploadHelper::uploadAndCompress($upload, $folder, 1920)
            : $upload->store($folder, 'public');

        $disk = Storage::disk('public');
        [$width, $height] = $isImage
            ? (getimagesize($disk->path($path)) ?: [null, null])
            : [null, null];

        $mediaFile = MediaFile::create([
            'original_name' => $upload->getClientOriginalName(),
            'file_name' => basename($path),
            'disk' => 'public',
            'path' => $path,
            'url' => $disk->url($path),
            'mime_type' => $isImage ? 'image/webp' : 'application/pdf',
            'extension' => $isImage ? 'webp' : 'pdf',
            'size' => $disk->size($path),
            'width' => $width,
            'height' => $height,
            'alt_text' => $validated['alt_text'] ?? null,
            'title' => $validated['title'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);
        app(ActivityLogger::class)->log('upload', 'Media Library', 'Media diupload: ' . $mediaFile->original_name, $mediaFile, [
            'mime_type' => $mediaFile->mime_type,
            'size' => $mediaFile->size,
        ]);

        return redirect()
            ->route('paneladmin.media-library.index')
            ->with('success', $isImage ? 'Media berhasil diupload dan dioptimasi.' : 'Media berhasil diupload.');
    }

    public function show(MediaFile $mediaFile): View
    {
        $mediaFile->load('uploader');

        return view('paneladmin.media-library.show', compact('mediaFile'));
    }

    public function download(MediaFile $mediaFile): StreamedResponse
    {
        $disk = Storage::disk($mediaFile->disk);

        abort_unless($disk->exists($mediaFile->path), 404);

        return $disk->download($mediaFile->path, $mediaFile->original_name);
    }

    public function destroy(MediaFile $mediaFile): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'Media Library', 'Media dihapus: ' . $mediaFile->original_name, $mediaFile);
        Storage::disk($mediaFile->disk)->delete($mediaFile->path);
        $mediaFile->delete();

        return redirect()
            ->route('paneladmin.media-library.index')
            ->with('success', 'Media berhasil dihapus.');
    }
}
