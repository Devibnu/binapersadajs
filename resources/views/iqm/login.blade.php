@extends('layouts.iqm')

@section('title', 'Login')

@push('styles')
<style>
  .iqm-password-field {
    position: relative;
  }

  .iqm-password-field .form-control {
    padding-right: 48px;
  }

  .iqm-password-toggle {
    align-items: center;
    background: transparent;
    border: 0;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    height: 42px;
    justify-content: center;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
  }

  .iqm-password-toggle:hover,
  .iqm-password-toggle:focus {
    color: #0f2742;
    outline: 0;
  }

  .iqm-login-logo {
    display: inline-block;
    max-height: 80px;
    max-width: 240px;
    object-fit: contain;
    width: auto;
  }
</style>
@endpush

@section('content')
@php
  $iqmDefaultLogoPath = public_path('web/images/logo.png');
  $iqmHasPrimaryLogo = $websiteSetting?->hasPrimaryLogo() ?? false;
  $iqmLoginLogoUrl = $websiteSetting?->logoUrl() ?? asset('web/images/logo.png');
  $iqmLoginLogoVersion = $iqmHasPrimaryLogo ? $websiteSetting->assetVersion() : (is_file($iqmDefaultLogoPath) ? filemtime($iqmDefaultLogoPath) : time());
  $iqmLoginLogoAlt = $websiteSetting?->nama_perusahaan ?? 'PT Bina Persada JS';
@endphp
<section class="py-5">
  <div class="container py-lg-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5">
        <div class="card iqm-card">
          <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
              <img src="{{ $iqmLoginLogoUrl }}?v={{ $iqmLoginLogoVersion }}" class="iqm-login-logo mb-3" alt="{{ $iqmLoginLogoAlt }}" decoding="async">
              <h4 class="fw-bold mb-1">Login IQM</h4>
              <p class="text-secondary mb-0">Masuk ke portal client Inquiry & Quotation.</p>
            </div>
            <form method="POST" action="{{ route('iqm.authenticate') }}">
              @csrf
              <div class="mb-3"><label class="form-label fw-semibold">Username</label><input type="text" name="username" value="{{ old('username') }}" class="form-control form-control-lg" required>@error('username')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
              <div class="mb-3">
                <label class="form-label fw-semibold" for="iqmPassword">Password</label>
                <div class="iqm-password-field">
                  <input type="password" name="password" id="iqmPassword" class="form-control form-control-lg" required>
                  <button type="button" id="toggleIqmPassword" class="iqm-password-toggle" aria-label="Lihat password" aria-pressed="false">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
              <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))><label class="form-check-label" for="remember">Remember Me</label></div>
              <button class="btn btn-warning btn-lg w-100">Login</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggleIqmPassword');
    const input = document.getElementById('iqmPassword');

    if (!toggle || !input) {
      return;
    }

    toggle.addEventListener('click', function () {
      const icon = toggle.querySelector('i');
      const isHidden = input.type === 'password';

      input.type = isHidden ? 'text' : 'password';
      toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Lihat password');
      toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
      icon.classList.toggle('fa-eye', !isHidden);
      icon.classList.toggle('fa-eye-slash', isHidden);
    });
  });
</script>
@endpush
