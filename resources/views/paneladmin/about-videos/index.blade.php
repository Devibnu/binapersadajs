@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>About Videos</h6>
          <p class="text-sm mb-0">Kelola video YouTube yang tampil di halaman About.</p>
        </div>
        @if(auth()->user()?->canAccess('about-videos.create'))
          <a href="{{ route('paneladmin.about-videos.create') }}" class="btn bg-gradient-primary mb-0">Tambah Video</a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Video</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">YouTube URL</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($aboutVideos as $aboutVideo)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <img src="{{ $aboutVideo->thumbnailUrl() }}" class="avatar avatar-lg me-3" alt="{{ $aboutVideo->displayTitle() }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $aboutVideo->displayTitle() }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $aboutVideo->youtube_id ?: '-' }}</p>
                      </div>
                    </div>
                  </td>
                  <td><a href="{{ $aboutVideo->youtube_url }}" target="_blank" rel="noopener" class="text-xs font-weight-bold">{{ \Illuminate\Support\Str::limit($aboutVideo->youtube_url, 45) }}</a></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $aboutVideo->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $aboutVideo->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $aboutVideo->sort_order }}</span></td>
                  <td class="align-middle">
                    @if(auth()->user()?->canAccess('about-videos.view'))
                      <a href="{{ route('paneladmin.about-videos.show', $aboutVideo) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @endif
                    @if(auth()->user()?->canAccess('about-videos.update'))
                      <a href="{{ route('paneladmin.about-videos.edit', $aboutVideo) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()?->canAccess('about-videos.delete'))
                      <form method="POST" action="{{ route('paneladmin.about-videos.destroy', $aboutVideo) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada video About.</td>
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
