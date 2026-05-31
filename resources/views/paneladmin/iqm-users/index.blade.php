@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <h6>IQM User Portal</h6>
            <p class="text-sm mb-0">Kelola akun client untuk portal Inquiry & Quotation Management.</p>
          </div>
          @if(auth()->user()->canAccess('iqm-user.create'))
            <a href="{{ route('paneladmin.iqm-users.create') }}" class="btn bg-gradient-primary mb-0">+ Tambah User IQM</a>
          @endif
        </div>
        <form method="GET" class="row mt-3">
          <div class="col-md-5"><input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari perusahaan, PIC, username, email"></div>
          <div class="col-auto"><button type="submit" class="btn bg-gradient-dark mb-0">Cari</button></div>
        </form>
      </div>
      <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead><tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Perusahaan</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Username</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kontak</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Login</th>
              <th></th>
            </tr></thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td class="ps-4"><h6 class="mb-0 text-sm">{{ $user->company_name }}</h6><p class="text-xs text-secondary mb-0">{{ $user->pic_name }}</p></td>
                  <td class="text-sm">{{ $user->username }}</td>
                  <td><p class="text-xs mb-0">{{ $user->email ?: '-' }}</p><p class="text-xs text-secondary mb-0">{{ $user->phone ?: '-' }}</p></td>
                  <td class="text-center"><span class="badge badge-sm {{ $user->isActive() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $user->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                  <td class="text-center text-xs text-secondary">{{ $user->last_login_at?->format('d/m/Y H:i') ?: '-' }}</td>
                  <td class="text-end pe-4">
                    @if(auth()->user()->canAccess('iqm-user.view'))
                      <a href="{{ route('paneladmin.iqm-users.show', $user) }}" class="text-secondary font-weight-bold text-xs me-3">Detail</a>
                    @endif
                    @if(auth()->user()->canAccess('iqm-user.edit'))
                      <a href="{{ route('paneladmin.iqm-users.edit', $user) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()->canAccess('iqm-user.delete'))
                      <form method="POST" action="{{ route('paneladmin.iqm-users.destroy', $user) }}" class="d-inline js-confirm-delete">@csrf @method('DELETE')<button class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button></form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada user portal.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $users->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
