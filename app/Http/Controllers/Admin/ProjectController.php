<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('projectCategory')->orderBy('sort_order')->latest()->get();

        return view('paneladmin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('paneladmin.projects.create', [
            'project' => new Project([
                'status' => 'active',
                'sort_order' => 0,
            ]),
            'projectCategories' => $this->activeCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeUploads($request, $validated);
        Project::create($validated);

        return redirect()
            ->route('paneladmin.projects.index')
            ->with('success', $this->successMessage('Project berhasil ditambahkan.', $request));
    }

    public function show(Project $project)
    {
        $project->load('projectCategory');

        return view('paneladmin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('paneladmin.projects.edit', [
            'project' => $project,
            'projectCategories' => $this->activeCategories(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $this->validatedData($request, $project);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['title'], $project);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeUploads($request, $validated, $project);
        $project->update($validated);

        return redirect()
            ->route('paneladmin.projects.index')
            ->with('success', $this->successMessage('Project berhasil diperbarui.', $request));
    }

    public function destroy(Project $project)
    {
        foreach ($this->imageFields() as $field) {
            ImageUploadHelper::deleteStoredImage($project->{$field});
        }

        $project->delete();

        return redirect()
            ->route('paneladmin.projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->ignore($project),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_location' => ['nullable', 'string', 'max:255'],
            'project_year' => ['nullable', 'string', 'max:20'],
            'project_category_id' => [
                'nullable',
                Rule::exists('project_categories', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_1' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery_image_3' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function slugFor(?string $slug, string $title, ?Project $project = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'project';
        $candidate = $baseSlug;
        $counter = 1;

        while (
            Project::where('slug', $candidate)
                ->when($project, fn ($query) => $query->whereKeyNot($project->getKey()))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter++;
        }

        return $candidate;
    }

    private function storeUploads(Request $request, array &$validated, ?Project $project = null): void
    {
        foreach ($this->imageFields() as $field) {
            if (! $request->hasFile($field)) {
                unset($validated[$field]);

                continue;
            }

            if ($project?->{$field}) {
                ImageUploadHelper::deleteStoredImage($project->{$field});
            }

            $validated[$field] = ImageUploadHelper::uploadAndCompress($request->file($field), 'projects', 1920);
        }
    }

    private function imageFields(): array
    {
        return ['featured_image', 'gallery_image_1', 'gallery_image_2', 'gallery_image_3'];
    }

    private function activeCategories()
    {
        return ProjectCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function successMessage(string $message, Request $request): string
    {
        foreach ($this->imageFields() as $field) {
            if ($request->hasFile($field)) {
                return $message . ' Gambar berhasil diupload dan dioptimasi.';
            }
        }

        return $message;
    }
}
