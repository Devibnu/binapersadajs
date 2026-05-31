<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $projectCategories = ProjectCategory::withCount('projects')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('paneladmin.project-categories.index', compact('projectCategories'));
    }

    public function create()
    {
        return view('paneladmin.project-categories.create', [
            'projectCategory' => new ProjectCategory([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $projectCategory = ProjectCategory::create($validated);
        $this->clearWebsiteProjectCache();
        app(ActivityLogger::class)->log('create', 'Project Categories', 'Kategori project ditambahkan: ' . $projectCategory->name, $projectCategory);

        return redirect()
            ->route('paneladmin.project-categories.index')
            ->with('success', 'Kategori project berhasil ditambahkan.');
    }

    public function edit(ProjectCategory $projectCategory)
    {
        return view('paneladmin.project-categories.edit', compact('projectCategory'));
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $validated = $this->validatedData($request, $projectCategory);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['name'], $projectCategory);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $projectCategory->update($validated);
        $this->clearWebsiteProjectCache();
        app(ActivityLogger::class)->log('update', 'Project Categories', 'Kategori project diperbarui: ' . $projectCategory->name, $projectCategory);

        return redirect()
            ->route('paneladmin.project-categories.index')
            ->with('success', 'Kategori project berhasil diperbarui.');
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        app(ActivityLogger::class)->log('delete', 'Project Categories', 'Kategori project dihapus: ' . $projectCategory->name, $projectCategory);
        $projectCategory->delete();
        $this->clearWebsiteProjectCache();

        return redirect()
            ->route('paneladmin.project-categories.index')
            ->with('success', 'Kategori project berhasil dihapus.');
    }

    private function validatedData(Request $request, ?ProjectCategory $projectCategory = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('project_categories', 'slug')->ignore($projectCategory),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function slugFor(?string $slug, string $name, ?ProjectCategory $projectCategory = null): string
    {
        $baseSlug = Str::slug($slug ?: $name) ?: 'kategori-project';
        $candidate = $baseSlug;
        $counter = 1;

        while (
            ProjectCategory::where('slug', $candidate)
                ->when($projectCategory, fn ($query) => $query->whereKeyNot($projectCategory->getKey()))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter++;
        }

        return $candidate;
    }

    private function clearWebsiteProjectCache(): void
    {
        Cache::forget('website_projects');
        Cache::forget('website_project_categories');
    }
}
