<?php

namespace App\Http\Controllers;

use App\Models\PageHero;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Support\Facades\Schema;

class FrontendProjectController extends Controller
{
    public function index()
    {
        $projects = collect();
        $projectCategories = collect();
        $showFallback = true;

        if (Schema::hasTable('projects')) {
            $hasProjects = Project::exists();
            $projects = Project::with('projectCategory')
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
            $showFallback = ! $hasProjects;
        }

        if (Schema::hasTable('project_categories')) {
            $projectCategories = ProjectCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('website.projects', [
            'projects' => $projects,
            'projectCategories' => $projectCategories,
            'showProjectFallback' => $showFallback,
            'pageHero' => $this->pageHero(),
        ]);
    }

    public function show(string $slug)
    {
        $project = Project::with(['projectCategory', 'projectImages'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedProjects = Project::with('projectCategory')
            ->where('status', 'active')
            ->whereKeyNot($project->getKey())
            ->orderBy('sort_order')
            ->orderBy('title')
            ->take(3)
            ->get();

        return view('website.project-single', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
            'pageHero' => $this->pageHero(),
        ]);
    }

    private function pageHero(): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', 'projects')
            ->where('is_active', true)
            ->first();
    }
}
