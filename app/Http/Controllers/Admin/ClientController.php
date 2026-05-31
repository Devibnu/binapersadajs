<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('paneladmin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('paneladmin.clients.create', [
            'client' => new Client([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeLogo($request, $validated);

        $client = Client::create($validated);
        $this->clearWebsiteClientCache();
        app(ActivityLogger::class)->log('create', 'Clients', 'Client ditambahkan: ' . $client->name, $client);

        return redirect()
            ->route('paneladmin.clients.index')
            ->with('success', $this->successMessage('Client berhasil ditambahkan.', $request));
    }

    public function show(Client $client)
    {
        return view('paneladmin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('paneladmin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $this->validatedData($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeLogo($request, $validated, $client);

        $client->update($validated);
        $this->clearWebsiteClientCache();
        app(ActivityLogger::class)->log('update', 'Clients', 'Client diperbarui: ' . $client->name, $client);

        return redirect()
            ->route('paneladmin.clients.index')
            ->with('success', $this->successMessage('Client berhasil diperbarui.', $request));
    }

    public function destroy(Client $client)
    {
        app(ActivityLogger::class)->log('delete', 'Clients', 'Client dihapus: ' . $client->name, $client);
        ImageUploadHelper::deleteStoredImage($client->logo);
        $client->delete();
        $this->clearWebsiteClientCache();

        return redirect()
            ->route('paneladmin.clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);
    }

    private function storeLogo(Request $request, array &$validated, ?Client $client = null): void
    {
        if (! $request->hasFile('logo')) {
            unset($validated['logo']);

            return;
        }

        if ($client?->logo) {
            ImageUploadHelper::deleteStoredImage($client->logo);
        }

        $file = $request->file('logo');

        if ($file->getClientOriginalExtension() === 'svg') {
            $path = 'clients/' . Str::uuid() . '.svg';
            Storage::disk('public')->putFileAs('clients', $file, basename($path));
            $validated['logo'] = $path;

            return;
        }

        $validated['logo'] = ImageUploadHelper::uploadAndCompress($file, 'clients', 600);
    }

    private function clearWebsiteClientCache(): void
    {
        Cache::forget('website_clients');
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('logo')
            ? $message . ' Logo berhasil diupload.'
            : $message;
    }
}
