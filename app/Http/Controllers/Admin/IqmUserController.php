<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IqmUser;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class IqmUserController extends Controller
{
    private const DEFAULT_PASSWORD = 'BinaPersada@2026';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:iqm-user.view')->only(['index', 'show']);
        $this->middleware('permission:iqm-user.create')->only(['create', 'store']);
        $this->middleware('permission:iqm-user.edit')->only(['edit', 'update']);
        $this->middleware('permission:iqm-user.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = IqmUser::query()->latest();

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(fn ($builder) => $builder
                ->where('company_name', 'like', '%' . $search . '%')
                ->orWhere('pic_name', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%'));
        }

        return view('paneladmin.iqm-users.index', [
            'users' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('paneladmin.iqm-users.create', ['user' => new IqmUser(['status' => 'active'])]);
    }

    public function store(Request $request)
    {
        $this->normalizePhoneInput($request);
        $validated = $this->validatedData($request);
        $validated['password'] = filled($validated['password'] ?? null)
            ? $validated['password']
            : self::DEFAULT_PASSWORD;
        $validated['password'] = Hash::make($validated['password']);

        $user = IqmUser::create($validated);
        app(ActivityLogger::class)->log('create', 'IQM User Portal', 'User portal ditambahkan: ' . $user->username, $user);

        return redirect()->route('paneladmin.iqm-users.index')->with('success', 'User portal berhasil ditambahkan.');
    }

    public function edit(IqmUser $iqmUser)
    {
        return view('paneladmin.iqm-users.edit', ['user' => $iqmUser]);
    }

    public function show(IqmUser $iqmUser)
    {
        return view('paneladmin.iqm-users.show', ['user' => $iqmUser]);
    }

    public function update(Request $request, IqmUser $iqmUser)
    {
        $this->normalizePhoneInput($request);
        $validated = $this->validatedData($request, $iqmUser);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->input('password'));
        } else {
            unset($validated['password']);
        }

        if (! $request->has('phone')) {
            unset($validated['phone']);
        }

        $iqmUser->update($validated);
        app(ActivityLogger::class)->log('update', 'IQM User Portal', 'User portal diperbarui: ' . $iqmUser->username, $iqmUser);

        return redirect()->route('paneladmin.iqm-users.index')->with('success', 'User portal berhasil diperbarui.');
    }

    public function resetPassword(Request $request, IqmUser $iqmUser)
    {
        abort(404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $iqmUser->update(['password' => Hash::make($validated['password'])]);
        app(ActivityLogger::class)->log('reset_password', 'IQM User Portal', 'Password portal direset: ' . $iqmUser->username, $iqmUser);

        return back()->with('success', 'Password user portal berhasil direset.');
    }

    public function toggleStatus(IqmUser $iqmUser)
    {
        abort(404);

        $iqmUser->update(['status' => $iqmUser->isActive() ? 'inactive' : 'active']);

        return back()->with('success', 'Status user portal berhasil diperbarui.');
    }

    public function destroy(IqmUser $iqmUser)
    {
        app(ActivityLogger::class)->log('delete', 'IQM User Portal', 'User portal dihapus: ' . $iqmUser->username, $iqmUser);
        $iqmUser->delete();

        return redirect()->route('paneladmin.iqm-users.index')->with('success', 'User portal berhasil dihapus.');
    }

    private function validatedData(Request $request, ?IqmUser $user = null): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'pic_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:80', Rule::unique('iqm_users', 'username')->ignore($user)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('iqm_users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }

    private function normalizePhoneInput(Request $request): void
    {
        if (! $request->has('phone') && $request->has('no_telepon')) {
            $request->merge(['phone' => $request->input('no_telepon')]);
        }
    }
}
