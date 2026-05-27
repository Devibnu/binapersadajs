@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Roles & Permission</h6>
          <p class="text-sm mb-0">Kelola kelompok akses admin berbasis checklist permission.</p>
        </div>
        @if(auth()->user()->canAccess('roles.create'))
          <a href="{{ route('paneladmin.roles.create') }}" class="btn bg-gradient-primary mb-0">Tambah Role</a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Permission</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td class="px-4">
                    <h6 class="mb-0 text-sm">{{ $role->name }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $role->description ?: $role->slug }}</p>
                  </td>
                  <td class="text-center text-sm">{{ $role->is_super_admin ? 'Semua' : $role->permissions_count }}</td>
                  <td class="text-center text-sm">{{ $role->users_count }}</td>
                  <td class="text-center">
                    <span class="badge badge-sm {{ $role->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td>
                    @if(auth()->user()->canAccess('roles.update'))
                      <a href="{{ route('paneladmin.roles.edit', $role) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(! $role->is_super_admin && auth()->user()->canAccess('roles.delete'))
                      <form method="POST" action="{{ route('paneladmin.roles.destroy', $role) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
