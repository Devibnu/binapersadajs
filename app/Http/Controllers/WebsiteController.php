<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
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

        return view('website.home', compact('heroBanners', 'services', 'showServiceFallback'));
    }

    public function about()
    {
        return view('website.about', [
            'pageHero' => $this->pageHero('about'),
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
        ]);
    }

    private function servicesForWebsite(): array
    {
        if (! Schema::hasTable('services')) {
            return [collect(), true];
        }

        $hasServices = Service::exists();
        $services = Service::where('is_active', true)
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
