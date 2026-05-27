@extends('layouts.user_type.auth')

@section('content')
@php
    $roleName = $admin->role?->name ?? 'Belum ditentukan';
    $initial = strtoupper(mb_substr($admin->name, 0, 1));
    $accountActive = (bool) ($admin->is_active ?? true);
    $lastLoginAt = $admin->last_login_at
        ? \Illuminate\Support\Carbon::parse($admin->last_login_at)
        : $lastLoginActivity?->created_at;
    $passwordUpdatedAt = $admin->password_changed_at
        ? \Illuminate\Support\Carbon::parse($admin->password_changed_at)
        : null;
@endphp

<style>
    .admin-profile-hero {
        background: linear-gradient(120deg, #0c1e35 0%, #173956 54%, #1f8f5f 100%);
        overflow: hidden;
    }

    .admin-profile-avatar {
        align-items: center;
        background: linear-gradient(135deg, #1f8f5f, #0c1e35);
        border: 4px solid rgba(255, 255, 255, .34);
        border-radius: 18px;
        color: #fff;
        display: flex;
        flex: 0 0 94px;
        font-size: 38px;
        height: 94px;
        justify-content: center;
        width: 94px;
    }

    .admin-profile-meta {
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: .75rem;
        padding: 12px 16px;
    }

    .admin-profile-detail {
        border-bottom: 1px solid #f0f2f5;
        padding: 14px 0;
    }

    .admin-profile-detail:last-child {
        border-bottom: 0;
    }

    .admin-profile-action {
        min-height: 44px;
    }

    @media (max-width: 575.98px) {
        .admin-profile-avatar {
            flex-basis: 76px;
            font-size: 30px;
            height: 76px;
            width: 76px;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card admin-profile-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                            <div class="admin-profile-avatar mb-3 mb-sm-0 me-sm-4">
                                <span class="font-weight-bolder">{{ $initial }}</span>
                            </div>
                            <div class="text-white">
                                <p class="text-xs text-uppercase font-weight-bolder opacity-8 mb-2">Profile Saya</p>
                                <h3 class="text-white mb-2">{{ $admin->name }}</h3>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="badge bg-white text-dark">{{ $roleName }}</span>
                                    <span class="badge {{ $accountActive ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                        {{ $accountActive ? 'Akun Aktif' : 'Akun Nonaktif' }}
                                    </span>
                                </div>
                                <p class="text-sm text-white opacity-8 mb-0">{{ $admin->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 mt-4 mt-lg-0">
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="admin-profile-meta h-100">
                                    <p class="text-xs text-white opacity-8 text-uppercase font-weight-bolder mb-2">Status Akun</p>
                                    <p class="text-sm text-white font-weight-bold mb-0">{{ $accountActive ? 'Aktif' : 'Nonaktif' }}</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="admin-profile-meta h-100">
                                    <p class="text-xs text-white opacity-8 text-uppercase font-weight-bolder mb-2">Terdaftar Sejak</p>
                                    <p class="text-sm text-white font-weight-bold mb-0">{{ $admin->created_at?->format('d M Y') ?? 'Belum tersedia' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Informasi Akun</h6>
                <p class="text-sm mb-0">Data administrator yang digunakan pada panel CMS.</p>
            </div>
            <div class="card-body pt-3">
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Nama</span>
                    <span class="text-sm font-weight-bold text-dark text-end">{{ $admin->name }}</span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Email</span>
                    <span class="text-sm font-weight-bold text-dark text-end">{{ $admin->email }}</span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Role</span>
                    <span class="badge bg-gradient-primary">{{ $roleName }}</span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Login Terakhir</span>
                    <span class="text-sm font-weight-bold text-dark text-end">
                        {{ $lastLoginAt?->format('d/m/Y H:i') ?? 'Belum tersedia' }}
                    </span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Dibuat Pada</span>
                    <span class="text-sm font-weight-bold text-dark text-end">{{ $admin->created_at?->format('d/m/Y H:i') ?? 'Belum tersedia' }}</span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Status Akun</span>
                    <span class="badge {{ $accountActive ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                        {{ $accountActive ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card mb-4 mb-lg-0">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Aktivitas Terbaru</h6>
                    <p class="text-sm mb-0">Lima tindakan terakhir dari akun ini.</p>
                </div>
                <span class="badge bg-gradient-info">{{ $activityCount }} aktivitas</span>
            </div>
            <div class="card-body pt-3">
                @forelse($activityLogs as $activityLog)
                    <div class="admin-profile-detail d-flex align-items-start">
                        <span class="badge {{ $activityLog->actionBadgeClass() }} me-3 mt-1">
                            {{ \Illuminate\Support\Str::headline($activityLog->action) }}
                        </span>
                        <div class="flex-grow-1">
                            <p class="text-sm font-weight-bold mb-1">{{ $activityLog->module }}</p>
                            @if($activityLog->description)
                                <p class="text-xs text-secondary mb-1">{{ $activityLog->description }}</p>
                            @endif
                            <p class="text-xs text-secondary mb-0">{{ $activityLog->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-history text-secondary text-lg mb-3"></i>
                        <p class="text-sm text-secondary mb-0">Belum ada aktivitas terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Keamanan Akun</h6>
                <p class="text-sm mb-0">Informasi perlindungan akses administrator.</p>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
                        <i class="fas fa-shield-alt text-white opacity-10"></i>
                    </div>
                    <div>
                        <p class="text-sm font-weight-bold mb-0">Password Terakhir Diubah</p>
                        <p class="text-xs text-secondary mb-0">
                            {{ $passwordUpdatedAt?->format('d/m/Y H:i') ?? 'Belum tersedia' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('paneladmin.profile.password') }}" class="btn bg-gradient-primary w-100 mb-2">
                    <i class="fas fa-lock me-2"></i>Ubah Password
                </a>
                <p class="text-xs text-secondary mb-0">Gunakan password kuat dan jangan membagikannya kepada pihak lain.</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Ringkasan Aktivitas</h6>
                <p class="text-sm mb-0">Jejak penggunaan akun pada CMS.</p>
            </div>
            <div class="card-body pt-3">
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Jumlah Aktivitas</span>
                    <span class="text-sm font-weight-bold text-dark">{{ $activityCount }} aktivitas</span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">Aktivitas Terakhir</span>
                    <span class="text-sm font-weight-bold text-dark text-end">
                        {{ $activityLogs->first()?->created_at?->format('d/m/Y H:i') ?? 'Belum tersedia' }}
                    </span>
                </div>
                <div class="admin-profile-detail d-flex justify-content-between align-items-center">
                    <span class="text-sm text-secondary">IP Login Terakhir</span>
                    <span class="text-sm font-weight-bold text-dark text-end">{{ $lastLoginActivity?->ip_address ?? 'Belum tersedia' }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header pb-0">
                <h6 class="mb-1">Aksi Cepat</h6>
                <p class="text-sm mb-0">Navigasi singkat untuk pengelolaan akun.</p>
            </div>
            <div class="card-body pt-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <a href="{{ route('paneladmin.profile.edit') }}" class="btn btn-outline-primary w-100 admin-profile-action mb-0">
                            <i class="fas fa-user-edit me-1"></i>Edit Profil
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('paneladmin.profile.password') }}" class="btn btn-outline-primary w-100 admin-profile-action mb-0">
                            <i class="fas fa-key me-1"></i>Ubah Password
                        </a>
                    </div>
                    @if($admin->canAccess('activity-logs.view'))
                        <div class="col-sm-6">
                            <a href="{{ route('paneladmin.activity-logs.index') }}" class="btn btn-outline-primary w-100 admin-profile-action mb-0">
                                <i class="fas fa-history me-1"></i>Activity Logs
                            </a>
                        </div>
                    @endif
                    <div class="col-sm-6">
                        <a href="{{ route('paneladmin.dashboard') }}" class="btn bg-gradient-primary w-100 admin-profile-action mb-0">
                            <i class="fas fa-th-large me-1"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
