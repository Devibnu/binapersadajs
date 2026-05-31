@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <h6>Detail User Portal IQM</h6>
            <p class="text-sm mb-0">Informasi akun client portal Inquiry & Quotation Management.</p>
          </div>
          <a href="{{ route('paneladmin.iqm-users.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
        </div>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-3 text-sm">Perusahaan</dt>
          <dd class="col-sm-9 text-sm">{{ $user->company_name }}</dd>

          <dt class="col-sm-3 text-sm">PIC</dt>
          <dd class="col-sm-9 text-sm">{{ $user->pic_name }}</dd>

          <dt class="col-sm-3 text-sm">Username</dt>
          <dd class="col-sm-9 text-sm">{{ $user->username }}</dd>

          <dt class="col-sm-3 text-sm">Email</dt>
          <dd class="col-sm-9 text-sm">{{ $user->email ?: '-' }}</dd>

          <dt class="col-sm-3 text-sm">Telepon</dt>
          <dd class="col-sm-9 text-sm">{{ $user->phone ?: '-' }}</dd>

          <dt class="col-sm-3 text-sm">Status</dt>
          <dd class="col-sm-9 text-sm">
            <span class="badge badge-sm {{ $user->isActive() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
              {{ $user->isActive() ? 'Aktif' : 'Nonaktif' }}
            </span>
          </dd>

          <dt class="col-sm-3 text-sm">Last Login</dt>
          <dd class="col-sm-9 text-sm">{{ $user->last_login_at?->format('d/m/Y H:i') ?: '-' }}</dd>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
