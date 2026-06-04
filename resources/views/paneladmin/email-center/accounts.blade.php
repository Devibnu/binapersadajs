@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Tambah Email Account</h6>
        <p class="text-sm mb-0">Konfigurasi SMTP dan IMAP email perusahaan.</p>
      </div>
      <div class="card-body">
        @if(session('success'))<div class="alert alert-success text-white">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger text-white">{{ session('error') }}</div>@endif
        <form method="POST" action="{{ route('paneladmin.email-center.accounts.store') }}" class="js-confirm-submit">
          @csrf
          @include('paneladmin.email-center.partials.account-form', ['account' => $account, 'isEdit' => false])
          <button type="submit" class="btn bg-gradient-primary w-100 mb-0">Simpan Account</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Email Accounts</h6>
          <p class="text-sm mb-0">Daftar akun email aktif dan nonaktif.</p>
        </div>
        <a href="{{ route('paneladmin.email-center.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Email Center</a>
      </div>
      <div class="card-body">
        @forelse($accounts as $item)
          <div class="border rounded-3 p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
              <div>
                <p class="text-sm font-weight-bold mb-1">{{ $item->name }} <span class="badge {{ $item->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></p>
                <p class="text-xs text-secondary mb-0">{{ $item->email }} | SMTP: {{ $item->smtp_host }} | IMAP: {{ $item->imap_host ?: '-' }}</p>
              </div>
              <form method="POST" action="{{ route('paneladmin.email-center.accounts.destroy', $item) }}" class="js-confirm-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link text-danger text-xs p-0 mb-0">Hapus</button>
              </form>
            </div>
            <details>
              <summary class="text-xs text-info font-weight-bold cursor-pointer">Edit account</summary>
              <form method="POST" action="{{ route('paneladmin.email-center.accounts.update', $item) }}" class="mt-3 js-confirm-submit">
                @csrf
                @method('PUT')
                @include('paneladmin.email-center.partials.account-form', ['account' => $item, 'isEdit' => true])
                <button type="submit" class="btn bg-gradient-info mb-0">Update Account</button>
              </form>
            </details>
          </div>
        @empty
          <p class="text-sm text-secondary mb-0">Belum ada email account.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
