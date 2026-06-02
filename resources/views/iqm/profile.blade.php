@extends('layouts.iqm')

@section('title', 'Profile')

@push('styles')
<style>
  .iqm-password-field {
    position: relative;
  }

  .iqm-password-field .form-control {
    padding-right: 46px;
  }

  .iqm-password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    border: 0;
    background: transparent;
    color: #6c757d;
    cursor: pointer;
    padding: 4px;
    line-height: 1;
  }

  .iqm-password-toggle:hover {
    color: #1f8f5f;
  }
</style>
@endpush

@section('content')
<div class="container iqm-container py-4">
  <div class="mb-4">
    <h3 class="fw-bold mb-1">Profile</h3>
    <p class="text-secondary mb-0">Informasi akun portal IQM Anda.</p>
  </div>
  <div class="card iqm-card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Perusahaan</dt><dd class="col-sm-9">{{ $user->company_name }}</dd>
        <dt class="col-sm-3">PIC</dt><dd class="col-sm-9">{{ $user->pic_name }}</dd>
        <dt class="col-sm-3">Username</dt><dd class="col-sm-9">{{ $user->username }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email ?: '-' }}</dd>
        <dt class="col-sm-3">Telepon</dt><dd class="col-sm-9">{{ $user->phone ?: '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge iqm-pill bg-gradient-success">{{ $user->status }}</span></dd>
        <dt class="col-sm-3">Last Login</dt><dd class="col-sm-9">{{ $user->last_login_at?->format('d/m/Y H:i') ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card iqm-card mt-4">
    <div class="card-body">
      <div class="mb-4">
        <h5 class="fw-bold mb-1">Ubah Password</h5>
        <p class="text-secondary mb-0">Perbarui password akun portal Anda secara mandiri.</p>
      </div>

      @if(session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger" role="alert">
          Periksa kembali isian password Anda.
        </div>
      @endif

      <form action="{{ route('iqm.profile.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
            <div class="iqm-password-field">
              <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
              <button type="button" class="iqm-password-toggle js-iqm-profile-password-toggle" data-target="current_password" aria-label="Tampilkan password saat ini">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="password" class="form-label fw-semibold">Password Baru</label>
            <div class="iqm-password-field">
              <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
              <button type="button" class="iqm-password-toggle js-iqm-profile-password-toggle" data-target="password" aria-label="Tampilkan password baru">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
            <div class="iqm-password-field">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
              <button type="button" class="iqm-password-toggle js-iqm-profile-password-toggle" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password baru">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="col-md-6">
            <div class="h-100 rounded-3 border bg-light p-3 d-flex align-items-center">
              <p class="text-secondary small mb-0">Password baru minimal 8 karakter dan harus berbeda dari password saat ini.</p>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="{{ route('iqm.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
          <button type="submit" class="btn btn-success">Simpan Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-iqm-profile-password-toggle').forEach(function (button) {
      button.addEventListener('click', function () {
        const input = document.getElementById(button.dataset.target);
        const icon = button.querySelector('i');

        if (!input || !icon) return;

        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });
    });
  });
</script>
@endpush
