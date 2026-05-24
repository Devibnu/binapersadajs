@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Project Categories</h6>
          <p class="text-sm mb-0">Kelola kategori filter portfolio project website.</p>
        </div>
        <a href="{{ route('paneladmin.project-categories.create') }}" class="btn bg-gradient-primary mb-0">Tambah Kategori</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Projects</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($projectCategories as $projectCategory)
                <tr>
                  <td>
                    <div class="px-2 py-1">
                      <h6 class="mb-0 text-sm">{{ $projectCategory->name }}</h6>
                      <p class="text-xs text-secondary mb-0">{{ $projectCategory->slug }}</p>
                    </div>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($projectCategory->description ?: '-', 65) }}</p></td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $projectCategory->projects_count }}</span></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $projectCategory->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $projectCategory->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $projectCategory->sort_order }}</span></td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.project-categories.edit', $projectCategory) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.project-categories.destroy', $projectCategory) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada kategori project.</td>
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
