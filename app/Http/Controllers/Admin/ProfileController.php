<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $admin = auth()->user()->loadMissing('role');

        $activityLogs = ActivityLog::query()
            ->where('user_id', $admin->id)
            ->latest('id')
            ->limit(5)
            ->get();

        $activityCount = ActivityLog::query()
            ->where('user_id', $admin->id)
            ->count();

        $lastLoginActivity = ActivityLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'login')
            ->latest('id')
            ->first();

        return view('paneladmin.profile', compact(
            'admin',
            'activityLogs',
            'activityCount',
            'lastLoginActivity'
        ));
    }

    public function edit(): View
    {
        return view('paneladmin.profile-edit', [
            'admin' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($admin->id)],
        ]);

        $admin->update($validated);

        app(ActivityLogger::class)->log(
            'profile.updated',
            'Profile',
            'Profil administrator diperbarui.',
            $admin,
            ['name' => $admin->name, 'email' => $admin->email]
        );

        return redirect()
            ->route('paneladmin.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(): View
    {
        return view('paneladmin.profile-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = $request->user();

        if (! Hash::check($validated['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        if (Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password baru tidak boleh sama dengan password saat ini.',
            ]);
        }

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        app(ActivityLogger::class)->log(
            'password.updated',
            'Profile',
            'Password administrator diperbarui.',
            $admin
        );

        return redirect()
            ->route('paneladmin.profile')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
