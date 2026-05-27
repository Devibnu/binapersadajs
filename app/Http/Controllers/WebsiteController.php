<?php

namespace App\Http\Controllers;

use App\Models\ContactPageSetting;
use App\Models\AboutPageSetting;
use App\Models\AboutTeam;
use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\HomepageVideo;
use App\Models\PageHero;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class WebsiteController extends Controller
{
    public function home()
    {
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

        return view('website.home', [
            'heroBanners' => $heroBanners,
            'services' => $services,
            'showServiceFallback' => $showServiceFallback,
            'homepageSetting' => HomepageSetting::current(),
            'homepageVideo' => HomepageVideo::current(),
        ]);
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

        $hasServices = Service::exists();
        $services = Service::where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

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
