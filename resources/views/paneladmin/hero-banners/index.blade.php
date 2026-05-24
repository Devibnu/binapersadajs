@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Hero Banner</h6>
          <p class="text-sm mb-0">Kelola slider utama homepage.</p>
        </div>
        <a href="{{ route('paneladmin.hero-banners.create') }}" class="btn bg-gradient-primary mb-0">Tambah Banner</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Banner</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tombol</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($heroBanners as $heroBanner)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
		                        <img src="{{ $heroBanner->backgroundUrl() }}" class="avatar avatar-lg me-3" alt="{{ $heroBanner->display_title }}">
		                      </div>
		                      <div class="d-flex flex-column justify-content-center">
		                        <h6 class="mb-0 text-sm">{{ $heroBanner->display_title }}</h6>
		                        <p class="text-xs text-secondary mb-0">{{ $heroBanner->display_small_text }}</p>
		                      </div>
                    </div>
                  </td>
                  <td>
		                    <p class="text-xs font-weight-bold mb-0">{{ $heroBanner->display_button_text ?: '-' }}</p>
		                    <p class="text-xs text-secondary mb-0">{{ $heroBanner->display_button_link ?: '-' }}</p>
                  </td>
                  <td class="align-middle text-center text-sm">
		                    <span class="badge badge-sm {{ $heroBanner->display_is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
		                      {{ $heroBanner->display_is_active ? 'Aktif' : 'Nonaktif' }}
		                    </span>
                  </td>
                  <td class="align-middle text-center">
		                    <span class="text-secondary text-xs font-weight-bold">{{ $heroBanner->display_sort_order }}</span>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.hero-banners.edit', $heroBanner) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.hero-banners.destroy', $heroBanner) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada hero banner.</td>
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
