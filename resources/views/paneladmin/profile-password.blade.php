@extends('layouts.user_type.auth')

@section('content')
@php
    $admin = auth()->user();
@endphp
<style>
    .admin-password-field {
        position: relative;
    }

    .admin-password-field .form-control {
        padding-right: 46px;
    }

    .admin-password-toggle {
        align-items: center;
        background: transparent;
        border: 0;
        color: #8392ab;
        cursor: pointer;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        z-index: 2;
    }

    .admin-password-toggle:hover,
    .admin-password-toggle:focus {
        color: #2152ff;
        outline: 0;
    }

    .admin-password-info {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 0.75rem;
        height: 100%;
        padding: 1rem;
    }

    .admin-password-info p {
        line-height: 1.55;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
                        <i class="fas fa-lock text-white opacity-10"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Ubah Password</h6>
                        <p class="text-sm mb-0">Lindungi akses panel admin Anda.</p>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('paneladmin.profile.password.update') }}" class="js-confirm-submit">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admin-username" class="form-control-label">Username</label>
                                <input
                                    id="admin-username"
                                    type="text"
                                    class="form-control"
                                    value="{{ $admin?->username ?? $admin?->name ?? '-' }}"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admin-email" class="form-control-label">Email</label>
                                <input
                                    id="admin-email"
                                    type="email"
                                    class="form-control"
                                    value="{{ $admin?->email ?? '-' }}"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="current-password" class="form-control-label">Password Saat Ini</label>
                                <div class="admin-password-field">
                                    <input
                                        id="current-password"
                                        type="password"
                                        name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        autocomplete="current-password"
                                        required
                                    >
                                    <button type="button" class="admin-password-toggle js-password-toggle" data-target="current-password" aria-label="Lihat password" aria-pressed="false">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new-password" class="form-control-label">Password Baru</label>
                                <div class="admin-password-field">
                                    <input
                                        id="new-password"
                                        type="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button type="button" class="admin-password-toggle js-password-toggle" data-target="new-password" aria-label="Lihat password" aria-pressed="false">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="new-password-confirmation" class="form-control-label">Konfirmasi Password Baru</label>
                                <div class="admin-password-field">
                                    <input
                                        id="new-password-confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button type="button" class="admin-password-toggle js-password-toggle" data-target="new-password-confirmation" aria-label="Lihat password" aria-pressed="false">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="admin-password-info d-flex align-items-center">
                                <div class="d-flex gap-3 align-items-start">
                                    <span class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md flex-shrink-0">
                                        <i class="fas fa-shield-alt text-white opacity-10"></i>
                                    </span>
                                    <p class="text-sm text-secondary mb-0">
                                        Password minimal 8 karakter dan berbeda dari password saat ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('paneladmin.profile') }}" class="btn btn-outline-secondary mb-0">Batal</a>
                        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('dashboard')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-password-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const input = document.getElementById(toggle.dataset.target);
                const icon = toggle.querySelector('i');

                if (!input || !icon) {
                    return;
                }

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Lihat password');
                toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                icon.classList.toggle('fa-eye', !isHidden);
                icon.classList.toggle('fa-eye-slash', isHidden);
            });
        });
    });
</script>
@endpush
