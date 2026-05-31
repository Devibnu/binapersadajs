@extends('layouts.iqm')

@section('title', 'Profile')

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
</div>
@endsection
