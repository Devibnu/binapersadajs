<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
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
            'role' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $user = trim((string) ($filters['user'] ?? ''));
        $role = trim((string) ($filters['role'] ?? ''));

        $activityLogs = ActivityLog::query()
            ->with('user.role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('description', 'like', '%' . $search . '%')
                        ->orWhere('module', 'like', '%' . $search . '%')
                        ->orWhere('action', 'like', '%' . $search . '%')
                        ->orWhere('user_name', 'like', '%' . $search . '%')
                        ->orWhere('ip_address', 'like', '%' . $search . '%')
                        ->orWhere('properties->record_name', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($user !== '', fn ($query) => $query->where('user_name', 'like', '%' . $user . '%'))
            ->when($role !== '', function ($query) use ($role) {
                $query->where(function ($builder) use ($role) {
                    $builder->where('properties->role_name', $role)
                        ->orWhereHas('user.role', fn ($roleQuery) => $roleQuery->where('name', $role));
                });
            })
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $modules = ActivityLog::query()->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::query()->distinct()->orderBy('action')->pluck('action');
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('paneladmin.activity-logs.index', compact('activityLogs', 'filters', 'modules', 'actions', 'roles'));
    }

    public function show(ActivityLog $activityLog): View
    {
        $activityLog->load('user.role');

        return view('paneladmin.activity-logs.show', compact('activityLog'));
    }
}
