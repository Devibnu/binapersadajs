@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card mb-4">
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

                    <div class="form-group">
                        <label for="current-password" class="form-control-label">Password Saat Ini</label>
                        <input
                            id="current-password"
                            type="password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password"
                            required
                        >
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new-password" class="form-control-label">Password Baru</label>
                        <input
                            id="new-password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new-password-confirmation" class="form-control-label">Konfirmasi Password Baru</label>
                        <input
                            id="new-password-confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <p class="text-sm text-secondary mb-4">
                        Gunakan password minimal 8 karakter dan berbeda dari password saat ini.
                    </p>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('paneladmin.profile') }}" class="btn btn-outline-secondary mb-0">Batal</a>
                        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
