<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:50'],
            'user' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $user = trim((string) ($filters['user'] ?? ''));

        $activityLogs = ActivityLog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('description', 'like', '%' . $search . '%')
                        ->orWhere('module', 'like', '%' . $search . '%')
                        ->orWhere('action', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($user !== '', fn ($query) => $query->where('user_name', 'like', '%' . $user . '%'))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $modules = ActivityLog::query()->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('paneladmin.activity-logs.index', compact('activityLogs', 'filters', 'modules', 'actions'));
    }

    public function show(ActivityLog $activityLog): View
    {
        return view('paneladmin.activity-logs.show', compact('activityLog'));
    }
}
