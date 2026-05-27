<?php

namespace App\Http\Middleware;

use App\Models\VisitorAnalytic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrackVisitorAnalytics
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response->getStatusCode())) {
            $userAgent = (string) $request->userAgent();

            VisitorAnalytic::create([
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'ip_address' => $request->ip(),
                'url' => $request->url(),
                'path' => '/' . ltrim($request->path(), '/'),
                'page_title' => $this->pageTitle($request),
                'referer' => $request->headers->get('referer'),
                'user_agent' => $userAgent ?: null,
                'browser' => $this->browser($userAgent),
                'platform' => $this->platform($userAgent),
                'device_type' => $this->deviceType($userAgent),
                'visited_at' => now(),
            ]);
        }

        return $response;
    }

    private function shouldTrack(Request $request, int $statusCode): bool
    {
        $agent = strtolower((string) $request->userAgent());

        return $request->isMethod('GET')
            && $statusCode >= 200
            && $statusCode < 400
            && Schema::hasTable('visitor_analytics')
            && ! preg_match('/bot|crawler|spider|slurp|bingpreview/i', $agent);
    }

    private function pageTitle(Request $request): string
    {
        return match ($request->route()?->getName()) {
            'website.home' => 'Beranda',
            'website.about' => 'Tentang Kami',
            'services.index' => 'Layanan',
            'services.show' => 'Detail Layanan',
            'website.projects' => 'Projects',
            'website.blog.index' => 'Blog',
            'website.blog.show' => 'Detail Blog',
            'website.contact' => 'Kontak',
            default => 'Website',
        };
    }

    private function browser(string $userAgent): string
    {
        return match (true) {
            preg_match('/Edg\//i', $userAgent) === 1 => 'Edge',
            preg_match('/Firefox\//i', $userAgent) === 1 => 'Firefox',
            preg_match('/Chrome\//i', $userAgent) === 1 => 'Chrome',
            preg_match('/Safari\//i', $userAgent) === 1 => 'Safari',
            default => 'Other',
        };
    }

    private function platform(string $userAgent): string
    {
        return match (true) {
            preg_match('/Android/i', $userAgent) === 1 => 'Android',
            preg_match('/iPhone|iPad|iPod/i', $userAgent) === 1 => 'iOS',
            preg_match('/Windows/i', $userAgent) === 1 => 'Windows',
            preg_match('/Macintosh|Mac OS/i', $userAgent) === 1 => 'macOS',
            preg_match('/Linux/i', $userAgent) === 1 => 'Linux',
            default => 'Unknown',
        };
    }

    private function deviceType(string $userAgent): string
    {
        return match (true) {
            preg_match('/iPad|Tablet/i', $userAgent) === 1 => 'tablet',
            preg_match('/Android(?!.*Tablet)|iPhone|Mobile|iPod/i', $userAgent) === 1 => 'mobile',
            default => 'desktop',
        };
    }
}
