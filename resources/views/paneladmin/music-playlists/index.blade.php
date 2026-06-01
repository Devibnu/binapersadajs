@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Music Playlist</h6>
          <p class="text-sm mb-0">Kelola musik yang tampil pada floating player website.</p>
        </div>
        @if(auth()->user()?->canAccess('music-playlists.create'))
          <a href="{{ route('paneladmin.music-playlists.create') }}" class="btn bg-gradient-primary mb-0">Tambah Musik</a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sumber Audio</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($musicPlaylists as $musicPlaylist)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <div class="avatar avatar-sm bg-gradient-primary me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-music text-white"></i>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $musicPlaylist->title }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $musicPlaylist->audio_file ? 'Upload MP3' : 'URL Audio' }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($musicPlaylist->audioSource())
                      <audio controls preload="none" style="max-width: 260px; width: 100%;">
                        <source src="{{ $musicPlaylist->audioSource() }}">
                      </audio>
                    @else
                      <span class="text-xs text-secondary">-</span>
                    @endif
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $musicPlaylist->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $musicPlaylist->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $musicPlaylist->sort_order }}</span></td>
                  <td class="align-middle">
                    @if(auth()->user()?->canAccess('music-playlists.view'))
                      <a href="{{ route('paneladmin.music-playlists.show', $musicPlaylist) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @endif
                    @if(auth()->user()?->canAccess('music-playlists.update'))
                      <a href="{{ route('paneladmin.music-playlists.edit', $musicPlaylist) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()?->canAccess('music-playlists.delete'))
                      <form method="POST" action="{{ route('paneladmin.music-playlists.destroy', $musicPlaylist) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada music playlist.</td>
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
