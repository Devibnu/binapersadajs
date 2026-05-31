<?php

namespace App\Http\Controllers;

use App\Models\ContactPageSetting;
use App\Models\AboutPageSetting;
use App\Models\AboutTeam;
use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\HomepageVideo;
use App\Models\PageHero;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Blog;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class WebsiteController extends Controller
{
    public function home()
    {
        $latestBlogs = collect();

        if (Schema::hasTable('blogs')) {
            $latestBlogs = Blog::published()
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->take(3)
                ->get();
        }
        $heroBanners = collect();

        if (Schema::hasTable('hero_banners')) {
            $activeColumn = Schema::hasColumn('hero_banners', 'is_active') ? 'is_active' : 'status_aktif';
            $sortColumn = Schema::hasColumn('hero_banners', 'sort_order') ? 'sort_order' : 'urutan';

            $heroBanners = HeroBanner::where($activeColumn, true)
                ->orderBy($sortColumn)
                ->orderByDesc('created_at')
                ->get();
        }

        [$services, $showServiceFallback] = $this->servicesForWebsite();
        [$projectCategories, $projects, $showProjectFallback] = $this->projectsForWebsite();
        $clients = $this->clientsForWebsite();

        return view('website.home', [
            'heroBanners' => $heroBanners,
            'services' => $services,
            'showServiceFallback' => $showServiceFallback,
            'homepageSetting' => HomepageSetting::current(),
            'homepageVideo' => HomepageVideo::current(),
            'projectCategories' => $projectCategories,
            'projects' => $projects,
            'showProjectFallback' => $showProjectFallback,
            'clients' => $clients,
            'latestBlogs' => $latestBlogs,
        ]);
    }

    private function clientsForWebsite()
    {
        if (! Schema::hasTable('clients')) {
            return collect();
        }

        return Cache::remember('website_clients', 3600, function () {
            return Client::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    private function projectsForWebsite(): array
    {
        if (! Schema::hasTable('projects')) {
            return [collect(), collect(), true];
        }

        // Cache projects data for 1 hour
        $projects = Cache::remember('website_projects', 3600, function () {
            return Project::with('projectCategory')
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        });

        // Cache project categories for 1 hour
        $projectCategories = Cache::remember('website_project_categories', 3600, function () {
            if (!Schema::hasTable('project_categories')) {
                return collect();
            }
            return ProjectCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        $hasProjects = $projects->count() > 0;
        return [$projectCategories, $projects, ! $hasProjects];
    }

    public function about()
    {
        return view('website.about', [
            'pageHero' => $this->pageHero('about'),
            'aboutPageSetting' => AboutPageSetting::current(),
            'aboutTeams' => Schema::hasTable('about_teams')
                ? AboutTeam::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function services()
    {
        [$services, $showServiceFallback] = $this->servicesForWebsite();

        return view('website.services', [
            'services' => $services,
            'showServiceFallback' => $showServiceFallback,
            'pageHero' => $this->pageHero('services'),
        ]);
    }

    public function serviceSingle()
    {
        return view('website.service-single');
    }

    public function projects()
    {
        return view('website.projects', [
            'pageHero' => $this->pageHero('projects'),
        ]);
    }

    public function contact()
    {
        return view('website.contact', [
            'pageHero' => $this->pageHero('contact'),
            'contactPageSetting' => ContactPageSetting::current(),
        ]);
    }

    private function servicesForWebsite(): array
    {
        if (! Schema::hasTable('services')) {
            return [collect(), true];
        }

        // Cache services data for 1 hour
        $services = Cache::remember('website_services', 3600, function () {
            return Service::where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        });

        $hasServices = $services->count() > 0;
        return [$services, ! $hasServices];
    }

    private function pageHero(string $pageKey): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', $pageKey)
            ->where('is_active', true)
            ->first();
    }
}
