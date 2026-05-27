<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutTeam;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutTeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $aboutTeams = AboutTeam::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('position', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('paneladmin.about-teams.index', compact('aboutTeams', 'search'));
    }

    public function create(): View
    {
        return view('paneladmin.about-teams.create', [
            'aboutTeam' => new AboutTeam([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $this->storeImage($request, $validated);

        $aboutTeam = AboutTeam::create($validated);
        app(ActivityLogger::class)->log('create', 'About Teams', 'Anggota tim ditambahkan: ' . $aboutTeam->name, $aboutTeam);

        return redirect()
            ->route('paneladmin.about-teams.index')
            ->with('success', $this->successMessage('Anggota tim berhasil ditambahkan.', $request));
    }

    public function edit(AboutTeam $aboutTeam): View
    {
        return view('paneladmin.about-teams.edit', compact('aboutTeam'));
    }

    public function update(Request $request, AboutTeam $aboutTeam): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $this->storeImage($request, $validated, $aboutTeam);

        $aboutTeam->update($validated);
        app(ActivityLogger::class)->log('update', 'About Teams', 'Anggota tim diperbarui: ' . $aboutTeam->name, $aboutTeam);

        return redirect()
            ->route('paneladmin.about-teams.index')
            ->with('success', $this->successMessage('Anggota tim berhasil diperbarui.', $request));
    }

    public function destroy(AboutTeam $aboutTeam): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'About Teams', 'Anggota tim dihapus: ' . $aboutTeam->name, $aboutTeam);
        ImageUploadHelper::deleteStoredImage($aboutTeam->image);
        $aboutTeam->delete();

        return redirect()
            ->route('paneladmin.about-teams.index')
            ->with('success', 'Anggota tim berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function storeImage(Request $request, array &$validated, ?AboutTeam $aboutTeam = null): void
    {
        if (! $request->hasFile('image')) {
            unset($validated['image']);

            return;
        }

        ImageUploadHelper::deleteStoredImage($aboutTeam?->image);
        $validated['image'] = ImageUploadHelper::uploadAndCompress(
            $request->file('image'),
            'about-teams',
            800
        );
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('image')
            ? $message . ' Gambar berhasil diupload dan dioptimasi.'
            : $message;
    }
}
