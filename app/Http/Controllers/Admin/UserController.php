<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->latest();
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%'));
        }

        $users = $query->paginate(20)->withQueryString();

        return view('paneladmin.users.index', compact('users'));
    }

    public function create()
    {
        return view('paneladmin.users.create', [
            'user' => new User(['is_active' => true]),
            'roles' => $this->availableRoles(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        app(ActivityLogger::class)->log('create', 'Users', 'User ditambahkan: ' . $user->name, $user, [
            'role_id' => $user->role_id,
        ]);

        return redirect()->route('paneladmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('paneladmin.users.edit', [
            'user' => $user,
            'roles' => $this->availableRoles($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validatedData($request, $user);

        if ($this->removesLastSuperAdmin($user, (int) $validated['role_id'], (bool) $validated['is_active'])) {
            return back()->withInput()->with('error', 'User Super Admin terakhir tidak dapat dinonaktifkan atau dipindahkan role.');
        }

        $originalRoleId = $user->role_id;
        if (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        app(ActivityLogger::class)->log('update', 'Users', 'User diperbarui: ' . $user->name, $user, [
            'role_id' => $user->role_id,
            'is_active' => $user->is_active,
        ]);

        if ((int) $originalRoleId !== (int) $user->role_id) {
            app(ActivityLogger::class)->log('assign_role', 'Users', 'Role user diperbarui: ' . $user->name, $user, [
                'from_role_id' => $originalRoleId,
                'to_role_id' => $user->role_id,
            ]);
        }

        return redirect()->route('paneladmin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin() && $this->activeSuperAdminCount() <= 1) {
            return back()->with('error', 'User Super Admin terakhir tidak dapat dihapus.');
        }

        app(ActivityLogger::class)->log('delete', 'Users', 'User dihapus: ' . $user->name, $user, [
            'role_id' => $user->role_id,
        ]);
        $user->delete();

        return redirect()->route('paneladmin.users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function removesLastSuperAdmin(User $user, int $roleId, bool $isActive): bool
    {
        if (! $user->isSuperAdmin() || $this->activeSuperAdminCount() > 1) {
            return false;
        }

        $nextRole = Role::find($roleId);

        return ! $isActive || ! $nextRole?->is_super_admin;
    }

    private function activeSuperAdminCount(): int
    {
        return User::where('is_active', true)
            ->whereHas('role', fn ($query) => $query->where('is_super_admin', true)->where('is_active', true))
            ->count();
    }

    private function availableRoles(?User $user = null)
    {
        return Role::query()
            ->where('is_active', true)
            ->when($user?->role_id, fn ($query) => $query->orWhereKey($user->role_id))
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->get();
    }
}
