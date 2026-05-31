@extends('layouts.iqm')

@section('title', 'Login')

@section('content')
<section class="py-5">
  <div class="container py-lg-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5">
        <div class="card iqm-card">
          <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
              <img src="{{ $websiteSetting?->logoUrl() ?? asset('web/images/logo.png') }}" style="max-height:70px;object-fit:contain" class="mb-3" alt="PT Bina Persada JS">
              <h4 class="fw-bold mb-1">Login IQM</h4>
              <p class="text-secondary mb-0">Masuk ke portal client Inquiry & Quotation.</p>
            </div>
            <form method="POST" action="{{ route('iqm.authenticate') }}">
              @csrf
              <div class="mb-3"><label class="form-label fw-semibold">Username</label><input type="text" name="username" value="{{ old('username') }}" class="form-control form-control-lg" required>@error('username')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
              <div class="mb-3"><label class="form-label fw-semibold">Password</label><input type="password" name="password" class="form-control form-control-lg" required></div>
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
