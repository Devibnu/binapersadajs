@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Projects</h6>
          <p class="text-sm mb-0">Kelola portfolio project yang tampil di website.</p>
        </div>
        <a href="{{ route('paneladmin.projects.create') }}" class="btn bg-gradient-primary mb-0">Tambah Project</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($projects as $project)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <img src="{{ $project->featuredImageUrl() }}" class="avatar avatar-lg me-3" alt="{{ $project->title }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $project->title }}</h6>
                        @if($project->client_name || $project->project_year)
                          <p class="text-xs text-secondary mb-0">
                            {{ collect([$project->client_name, $project->project_year])->filter()->implode(' - ') }}
                          </p>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td><p class="text-xs font-weight-bold mb-0">{{ $project->categoryName() }}</p></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $project->isActive() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $project->isActive() ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ $project->sort_order }}</span>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.projects.show', $project) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    <a href="{{ route('paneladmin.projects.edit', $project) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.projects.destroy', $project) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada data project.</td>
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
