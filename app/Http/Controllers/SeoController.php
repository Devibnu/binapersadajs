<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\SeoSetting;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $setting = SeoSetting::current();
        $baseUrl = rtrim($setting->canonical_url ?: url('/'), '/');
        $urls = collect([
            ['location' => $baseUrl . '/', 'priority' => '1.0'],
            ['location' => $baseUrl . '/about', 'priority' => '0.8'],
            ['location' => $baseUrl . '/services', 'priority' => '0.9'],
            ['location' => $baseUrl . '/projects', 'priority' => '0.8'],
            ['location' => $baseUrl . '/blog', 'priority' => '0.8'],
            ['location' => $baseUrl . '/contact', 'priority' => '0.7'],
        ]);

        if (Schema::hasTable('services')) {
            $urls = $urls->concat(
                Service::where('status', 'active')->latest('updated_at')->get()
                    ->map(fn (Service $service) => [
                        'location' => $baseUrl . '/services/' . $service->slug,
                        'lastmod' => $service->updated_at?->toAtomString(),
                        'priority' => '0.8',
                    ])
            );
        }

        if (Schema::hasTable('blogs')) {
            $urls = $urls->concat(
                Blog::published()->latest('updated_at')->get()
                    ->map(fn (Blog $blog) => [
                        'location' => $baseUrl . '/blog/' . $blog->slug,
                        'lastmod' => $blog->updated_at?->toAtomString(),
                        'priority' => '0.7',
                    ])
            );
        }

        return response()
            ->view('website.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $setting = SeoSetting::current();
        $baseUrl = rtrim($setting->canonical_url ?: url('/'), '/');
        $lines = [
            'User-agent: *',
            $setting->robots_index ? 'Allow: /' : 'Disallow: /',
            '',
            'Sitemap: ' . $baseUrl . '/sitemap.xml',
        ];

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
