@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <h6>Users</h6>
            <p class="text-sm mb-0">Kelola akun admin dan penetapan role.</p>
          </div>
          @if(auth()->user()->canAccess('users.create'))
            <a href="{{ route('paneladmin.users.create') }}" class="btn bg-gradient-primary mb-0">Tambah User</a>
          @endif
        </div>
        <form method="GET" class="row mt-3">
          <div class="col-md-5">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau email">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn bg-gradient-dark mb-0">Cari</button>
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dibuat</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td class="px-4">
                    <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                  </td>
                  <td><span class="text-sm">{{ $user->role?->name ?: 'Belum diberi role' }}</span></td>
                  <td class="text-center">
                    <span class="badge badge-sm {{ $user->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="text-center text-xs text-secondary">{{ $user->created_at?->format('d/m/Y') }}</td>
                  <td>
                    @if(auth()->user()->canAccess('users.update'))
                      <a href="{{ route('paneladmin.users.edit', $user) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()->canAccess('users.delete'))
                      <form method="POST" action="{{ route('paneladmin.users.destroy', $user) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada user pada pencarian ini.</td></tr>
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
