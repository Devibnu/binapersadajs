@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Edit Profil</h6>
                <p class="text-sm mb-0">Perbarui informasi dasar akun administrator Anda.</p>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('paneladmin.profile.update') }}" class="js-confirm-submit">
                    @csrf
                    @method('PUT')

                    <div class="row">
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

                    <div class="alert alert-light text-sm mb-4" role="alert">
                        Perubahan ini hanya memengaruhi nama dan email akun Anda. Role serta status akun tidak dapat diubah dari halaman profil.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('paneladmin.profile') }}" class="btn btn-outline-secondary mb-0">Batal</a>
                        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
