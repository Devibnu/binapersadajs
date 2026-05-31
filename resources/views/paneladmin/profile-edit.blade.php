@extends('layouts.user_type.auth')

@section('content')
<style>
    .profile-edit-card {
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }
    .profile-edit-card .card-header {
        padding: 24px 28px 0;
    }
    .profile-edit-card .card-body {
        padding: 24px 28px 28px;
    }
    .profile-edit-card .form-control {
        min-height: 44px;
        font-size: 14px;
    }
    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .profile-info-item {
        border: 1px solid #edf0f5;
        border-radius: 12px;
        padding: 14px;
        background: #f8fafc;
        min-height: 84px;
    }
    .profile-info-label {
        color: #67748e;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .profile-info-value {
        color: #344767;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }
    @media (max-width: 991.98px) {
        .profile-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 575.98px) {
        .profile-edit-card .card-header { padding: 20px 18px 0; }
        .profile-edit-card .card-body { padding: 20px 18px 22px; }
        .profile-info-grid { grid-template-columns: 1fr; }
        .profile-actions { flex-direction: column-reverse; }
        .profile-actions .btn { width: 100%; }
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card profile-edit-card border-0 mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-1 font-weight-bold">Edit Profil</h5>
                <p class="text-sm text-muted mb-0">Perbarui informasi akun administrator Anda.</p>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('paneladmin.profile.update') }}" class="js-confirm-submit">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profile-name" class="form-control-label">Nama</label>
                                <input
                                    id="profile-name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $admin->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    maxlength="100"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profile-email" class="form-control-label">Email</label>
                                <input
                                    id="profile-email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    maxlength="150"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <h6 class="mb-1">Informasi Akun</h6>
                        <p class="text-sm text-muted mb-0">Role dan status akun bersifat readonly dari halaman profil.</p>
                    </div>

                    <div class="profile-info-grid mb-4">
                        <div class="profile-info-item">
                            <div class="profile-info-label">Role</div>
                            <div class="profile-info-value">{{ $admin->role?->name ?? '-' }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Status Akun</div>
                            <div class="profile-info-value">
                                <span class="badge badge-sm {{ $admin->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                    {{ $admin->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Tanggal Dibuat</div>
                            <div class="profile-info-value">{{ optional($admin->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Terakhir Update</div>
                            <div class="profile-info-value">{{ optional($admin->updated_at)->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 profile-actions">
                        <a href="{{ route('paneladmin.profile') }}" class="btn btn-outline-secondary mb-0">Batal</a>
                        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
