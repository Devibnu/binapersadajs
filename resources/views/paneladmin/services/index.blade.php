@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Services</h6>
          <p class="text-sm mb-0">Kelola layanan yang tampil di website.</p>
        </div>
        <a href="{{ route('paneladmin.services.create') }}" class="btn bg-gradient-primary mb-0">Tambah Service</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Service</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Slug</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($services as $service)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <img src="{{ $service->imageUrl() }}" class="avatar avatar-lg me-3" alt="{{ $service->title }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $service->title }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $service->short_description ?: '-' }}</p>
                      </div>
                    </div>
                  </td>
                  <td><p class="text-xs font-weight-bold mb-0">{{ $service->slug }}</p></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $service->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ $service->sort_order }}</span>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.services.edit', $service) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.services.destroy', $service) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada data service.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
