<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicPlaylist;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MusicPlaylistController extends Controller
{
    public function index()
    {
        $musicPlaylists = MusicPlaylist::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('paneladmin.music-playlists.index', compact('musicPlaylists'));
    }

    public function create()
    {
        return view('paneladmin.music-playlists.create', [
            'musicPlaylist' => new MusicPlaylist([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeAudioFile($request, $validated);

        $musicPlaylist = MusicPlaylist::create($validated);
        app(ActivityLogger::class)->log('create', 'Music Playlist', 'Music playlist ditambahkan: ' . $musicPlaylist->title, $musicPlaylist);

        return redirect()
            ->route('paneladmin.music-playlists.index')
            ->with('success', $this->successMessage('Music playlist berhasil ditambahkan.', $request));
    }

    public function show(MusicPlaylist $musicPlaylist)
    {
        return view('paneladmin.music-playlists.show', compact('musicPlaylist'));
    }

    public function edit(MusicPlaylist $musicPlaylist)
    {
        return view('paneladmin.music-playlists.edit', compact('musicPlaylist'));
    }

    public function update(Request $request, MusicPlaylist $musicPlaylist)
    {
        $validated = $this->validatedData($request, $musicPlaylist);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeAudioFile($request, $validated, $musicPlaylist);

        $musicPlaylist->update($validated);
        app(ActivityLogger::class)->log('update', 'Music Playlist', 'Music playlist diperbarui: ' . $musicPlaylist->title, $musicPlaylist);

        return redirect()
            ->route('paneladmin.music-playlists.index')
            ->with('success', $this->successMessage('Music playlist berhasil diperbarui.', $request));
    }

    public function destroy(MusicPlaylist $musicPlaylist)
    {
        app(ActivityLogger::class)->log('delete', 'Music Playlist', 'Music playlist dihapus: ' . $musicPlaylist->title, $musicPlaylist);
        $this->deleteAudioFile($musicPlaylist->audio_file);
        $musicPlaylist->delete();

        return redirect()
            ->route('paneladmin.music-playlists.index')
            ->with('success', 'Music playlist berhasil dihapus.');
    }

    private function validatedData(Request $request, ?MusicPlaylist $musicPlaylist = null): array
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3', 'mimetypes:audio/mpeg,audio/mp3', 'max:10240'],
            'audio_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $validator->after(function ($validator) use ($request, $musicPlaylist) {
            $hasExistingFile = (bool) $musicPlaylist?->audio_file;
            $hasExistingUrl = (bool) $musicPlaylist?->audio_url;

            if (! $request->hasFile('audio_file') && ! $request->filled('audio_url') && ! $hasExistingFile && ! $hasExistingUrl) {
                $validator->errors()->add('audio_file', 'Upload MP3 atau URL Audio wajib diisi salah satu.');
                $validator->errors()->add('audio_url', 'Upload MP3 atau URL Audio wajib diisi salah satu.');
            }
        });

        return $validator->validate();
    }

    private function storeAudioFile(Request $request, array &$validated, ?MusicPlaylist $musicPlaylist = null): void
    {
        if (! $request->hasFile('audio_file')) {
            unset($validated['audio_file']);

            return;
        }

        $this->deleteAudioFile($musicPlaylist?->audio_file);
        $validated['audio_file'] = $request->file('audio_file')->store('music', 'public');
    }

    private function deleteAudioFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('audio_file')
            ? $message . ' File MP3 berhasil diupload.'
            : $message;
    }
}
