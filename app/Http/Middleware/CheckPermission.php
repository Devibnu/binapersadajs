<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if ($request->user()?->canAccess($permission)) {
            return $next($request);
        }

        if ($request->routeIs('paneladmin.dashboard', 'paneladmin.dashboard.alias')) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return redirect()
            ->route('paneladmin.dashboard')
            ->with('error', 'Anda tidak memiliki akses.');
    }
}
