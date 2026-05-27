<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorAnalytic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,7,30,custom'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $period = $filters['period'] ?? '30';
        [$from, $to] = $this->dateRange($period, $filters);
        $filtered = VisitorAnalytic::query()->whereBetween('visited_at', [$from, $to]);

        $summary = [
            'total_visitors' => VisitorAnalytic::distinct('session_id')->count('session_id'),
            'unique_visitors' => VisitorAnalytic::distinct('ip_address')->count('ip_address'),
            'page_views' => VisitorAnalytic::count(),
            'today_visitors' => $this->visitorCount(now()->startOfDay(), now()->endOfDay()),
            'seven_day_visitors' => $this->visitorCount(now()->subDays(6)->startOfDay(), now()->endOfDay()),
            'thirty_day_visitors' => $this->visitorCount(now()->subDays(29)->startOfDay(), now()->endOfDay()),
        ];

        $chart7 = $this->dailySeries(now()->subDays(6)->startOfDay(), now()->endOfDay());
        $chart30 = $this->dailySeries(now()->subDays(29)->startOfDay(), now()->endOfDay());

        $topPages = (clone $filtered)
            ->selectRaw('path, page_title, COUNT(*) as views')
            ->groupBy('path', 'page_title')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $deviceBreakdown = $this->breakdown($filtered, 'device_type');
        $browserBreakdown = $this->breakdown($filtered, 'browser');
        $topReferers = (clone $filtered)->get(['referer'])
            ->map(fn (VisitorAnalytic $visit) => $visit->refererLabel())
            ->countBy()
            ->sortDesc()
            ->take(8);

        $recentVisitors = (clone $filtered)->latest('visited_at')->limit(10)->get();

        return view('paneladmin.analytics.index', compact(
            'summary',
            'period',
            'filters',
            'from',
            'to',
            'chart7',
            'chart30',
            'topPages',
            'deviceBreakdown',
            'browserBreakdown',
            'topReferers',
            'recentVisitors'
        ));
    }

    private function visitorCount(Carbon $from, Carbon $to): int
    {
        return VisitorAnalytic::whereBetween('visited_at', [$from, $to])
            ->distinct('session_id')
            ->count('session_id');
    }

    private function dateRange(string $period, array $filters): array
    {
        if ($period === 'custom' && filled($filters['date_from'] ?? null) && filled($filters['date_to'] ?? null)) {
            return [Carbon::parse($filters['date_from'])->startOfDay(), Carbon::parse($filters['date_to'])->endOfDay()];
        }

        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            '7' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    private function dailySeries(Carbon $from, Carbon $to): array
    {
        $records = VisitorAnalytic::query()
            ->whereBetween('visited_at', [$from, $to])
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as views, COUNT(DISTINCT session_id) as visitors')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $visitors = [];
        $views = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $visitors[] = (int) ($records->get($key)?->visitors ?? 0);
            $views[] = (int) ($records->get($key)?->views ?? 0);
        }

        return compact('labels', 'visitors', 'views');
    }

    private function breakdown(Builder $query, string $column): Collection
    {
        $counts = (clone $query)
            ->selectRaw($column . ', COUNT(*) as total')
            ->groupBy($column)
            ->orderByDesc('total')
            ->pluck('total', $column);
        $total = max(1, (int) $counts->sum());

        return $counts->map(fn ($count) => [
            'count' => (int) $count,
            'percentage' => round(((int) $count / $total) * 100, 1),
        ]);
    }
}
