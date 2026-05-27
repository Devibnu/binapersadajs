<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderByDesc('is_super_admin')->orderBy('name')->get();

        return view('paneladmin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('paneladmin.roles.create', [
            'role' => new Role(['is_active' => true]),
            'permissionsByGroup' => Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name'),
            'selectedPermissions' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name']);
        $validated['is_super_admin'] = false;

        $role = Role::create($validated);
        $role->permissions()->sync($request->input('permissions', []));

        app(ActivityLogger::class)->log('create', 'Roles', 'Role ditambahkan: ' . $role->name, $role, [
            'permissions' => $role->permissions()->pluck('slug')->values()->all(),
        ]);

        return redirect()->route('paneladmin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        return view('paneladmin.roles.edit', [
            'role' => $role,
            'permissionsByGroup' => Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name'),
            'selectedPermissions' => $role->permissions()->pluck('permissions.id'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $this->validatedData($request, $role);
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name'], $role);

        if ($role->is_super_admin) {
            $validated['is_active'] = true;
            $validated['is_super_admin'] = true;
        }

        $role->update($validated);
        $role->permissions()->sync($request->input('permissions', []));

        app(ActivityLogger::class)->log('update', 'Roles', 'Role diperbarui: ' . $role->name, $role, [
            'permissions' => $role->permissions()->pluck('slug')->values()->all(),
        ]);

        return redirect()->route('paneladmin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_super_admin) {
            return back()->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Role masih digunakan oleh user dan tidak dapat dihapus.');
        }

        app(ActivityLogger::class)->log('delete', 'Roles', 'Role dihapus: ' . $role->name, $role);
        $role->delete();

        return redirect()->route('paneladmin.roles.index')->with('success', 'Role berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('roles', 'slug')->ignore($role)],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }

    private function uniqueSlug(?string $slug, string $name, ?Role $role = null): string
    {
        $base = Str::slug($slug ?: $name) ?: 'role';
        $candidate = $base;
        $suffix = 1;

        while (Role::where('slug', $candidate)->when($role, fn ($query) => $query->whereKeyNot($role->id))->exists()) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }
}
