@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <h6>About Teams</h6>
            <p class="text-sm mb-0">Kelola anggota tim yang tampil pada slider halaman About.</p>
          </div>
          <a href="{{ route('paneladmin.about-teams.create') }}" class="btn bg-gradient-primary mb-0">Tambah Anggota</a>
        </div>
        <form method="GET" action="{{ route('paneladmin.about-teams.index') }}" class="row mt-3">
          <div class="col-md-5">
            <div class="input-group">
              <input type="text" name="q" class="form-control" value="{{ $search }}" placeholder="Cari nama atau jabatan...">
              <button class="btn bg-gradient-secondary mb-0" type="submit">Cari</button>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Anggota Tim</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($aboutTeams as $aboutTeam)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <img src="{{ $aboutTeam->imageUrl() }}" class="avatar avatar-lg me-3" alt="{{ $aboutTeam->name }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $aboutTeam->name }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $aboutTeam->position }}</p>
                      </div>
                    </div>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($aboutTeam->description ?: '-', 70) }}</p></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $aboutTeam->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $aboutTeam->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $aboutTeam->sort_order }}</span></td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.about-teams.edit', $aboutTeam) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.about-teams.destroy', $aboutTeam) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada anggota tim.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $aboutTeams->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
