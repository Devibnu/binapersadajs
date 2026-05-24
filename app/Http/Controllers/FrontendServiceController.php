<?php

namespace App\Http\Controllers;

use App\Models\PageHero;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class FrontendServiceController extends Controller
{
    public function index()
    {
        [$services, $showServiceFallback] = $this->servicesForWebsite();

        return view('website.services', [
            'services' => $services,
            'showServiceFallback' => $showServiceFallback,
            'pageHero' => $this->pageHero(),
        ]);
    }

    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedServices = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('website.service-single', [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'pageHero' => $this->pageHero(),
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

    private function pageHero(): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', 'services')
            ->where('is_active', true)
            ->first();
    }
}
