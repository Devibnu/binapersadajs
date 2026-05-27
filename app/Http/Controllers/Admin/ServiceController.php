<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), ['active', 'inactive'], true)
            ? $request->query('status')
            : null;

        $services = Service::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('sort_order')
            ->latest()
            ->get();

        $counts = Service::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('paneladmin.services.index', compact('services', 'counts', 'status'));
    }

    public function create()
    {
        return view('paneladmin.services.create', [
            'service' => new Service([
                'is_active' => true,
                'status' => 'active',
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $validated['status'] === 'active';

        $this->storeUploads($request, $validated);

        $service = Service::create($validated);
        app(ActivityLogger::class)->log('create', 'Services', 'Layanan ditambahkan: ' . $service->title, $service);

        return redirect()
            ->route('paneladmin.services.index')
            ->with('success', $this->successMessage('Layanan berhasil ditambahkan.', $request));
    }

    public function edit(Service $service)
    {
        return view('paneladmin.services.edit', compact('service'));
    }

    public function show(Service $service)
    {
        return view('paneladmin.services.show', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $this->validatedData($request, $service);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title'], $service);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $validated['status'] === 'active';

        $this->storeUploads($request, $validated, $service);

        $service->update($validated);
        app(ActivityLogger::class)->log('update', 'Services', 'Layanan diperbarui: ' . $service->title, $service);

        return redirect()
            ->route('paneladmin.services.index')
            ->with('success', $this->successMessage('Layanan berhasil diperbarui.', $request));
    }

    public function destroy(Service $service)
    {
        app(ActivityLogger::class)->log('delete', 'Services', 'Layanan dihapus: ' . $service->title, $service);
        foreach ($this->uploadFields() as $field) {
            if ($service->{$field}) {
                ImageUploadHelper::deleteStoredImage($service->{$field});
            }
        }

        $service->delete();

        return redirect()
            ->route('paneladmin.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($service),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'short_content' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'feature_1' => ['nullable', 'string', 'max:255'],
            'feature_2' => ['nullable', 'string', 'max:255'],
            'feature_3' => ['nullable', 'string', 'max:255'],
            'feature_4' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_1' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_3' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'faq_1_question' => ['nullable', 'string', 'max:255'],
            'faq_1_answer' => ['nullable', 'string'],
            'faq_2_question' => ['nullable', 'string', 'max:255'],
            'faq_2_answer' => ['nullable', 'string'],
            'faq_3_question' => ['nullable', 'string', 'max:255'],
            'faq_3_answer' => ['nullable', 'string'],
            'cta_text' => ['nullable', 'string', 'max:255'],
            'cta_button_text' => ['nullable', 'string', 'max:255'],
            'cta_button_link' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function slugFor(?string $slug, string $title, ?Service $service = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'service';
        $candidate = $baseSlug;
        $counter = 1;

        while (
            Service::where('slug', $candidate)
                ->when($service, fn ($query) => $query->whereKeyNot($service->getKey()))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter++;
        }

        return $candidate;
    }

    private function storeUploads(Request $request, array &$validated, ?Service $service = null): void
    {
        foreach ($this->uploadFields() as $field) {
            if (! $request->hasFile($field)) {
                unset($validated[$field]);

                continue;
            }

            if ($service?->{$field}) {
                ImageUploadHelper::deleteStoredImage($service->{$field});
            }

            $validated[$field] = ImageUploadHelper::uploadAndCompress(
                $request->file($field),
                'services',
                $this->targetWidth($field)
            );
        }
    }

    private function uploadFields(): array
    {
        return ['icon', 'image', 'gallery_image_1', 'gallery_image_2', 'gallery_image_3'];
    }

    private function targetWidth(string $field): int
    {
        return str_starts_with($field, 'gallery_image_') ? 1400 : 800;
    }

    private function successMessage(string $message, Request $request): string
    {
        foreach ($this->uploadFields() as $field) {
            if ($request->hasFile($field)) {
                return $message . ' Gambar berhasil diupload dan dioptimasi.';
            }
        }

        return $message;
    }
}
