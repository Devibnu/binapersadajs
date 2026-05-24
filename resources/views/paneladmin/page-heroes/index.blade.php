@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Page Hero</h6>
          <p class="text-sm mb-0">Kelola banner halaman internal website.</p>
        </div>
        <a href="{{ route('paneladmin.page-heroes.create') }}" class="btn bg-gradient-primary mb-0">Tambah Page Hero</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Halaman</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Judul / Breadcrumb</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Posisi</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($pageHeroes as $pageHero)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <img src="{{ $pageHero->backgroundUrl() }}" class="avatar avatar-lg me-3" alt="{{ $pageHero->title }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $pageHero->page_key }}</h6>
                        <p class="text-xs text-secondary mb-0">Overlay: {{ $pageHero->overlay_opacity ?? 1 }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $pageHero->title }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $pageHero->breadcrumb_text ?: '-' }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold text-capitalize">{{ $pageHero->text_position ?: 'center' }}</span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $pageHero->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $pageHero->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.page-heroes.edit', $pageHero) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.page-heroes.destroy', $pageHero) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada data page hero.</td>
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
