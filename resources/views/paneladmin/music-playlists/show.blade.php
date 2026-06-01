@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Music Playlist</h6>
        @if(auth()->user()?->canAccess('music-playlists.update'))
          <a href="{{ route('paneladmin.music-playlists.edit', $musicPlaylist) }}" class="btn bg-gradient-primary mb-0">Edit Musik</a>
        @endif
      </div>
      <div class="card-body">
        <h4>{{ $musicPlaylist->title }}</h4>
        <p class="text-sm mb-1"><strong>Sumber:</strong> {{ $musicPlaylist->audio_file ? 'Upload MP3' : 'URL Audio' }}</p>
        <p class="text-sm mb-1"><strong>URL Audio:</strong> {{ $musicPlaylist->audio_url ?: '-' }}</p>
        <p class="text-sm mb-1"><strong>File Audio:</strong> {{ $musicPlaylist->audio_file ?: '-' }}</p>
        <p class="text-sm mb-1"><strong>Urutan:</strong> {{ $musicPlaylist->sort_order }}</p>
        <p class="text-sm mb-3"><strong>Status:</strong>
          <span class="badge badge-sm {{ $musicPlaylist->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
            {{ $musicPlaylist->is_active ? 'Aktif' : 'Tidak Aktif' }}
          </span>
        </p>
        @if($musicPlaylist->audioSource())
          <audio controls preload="none" style="width: 100%; max-width: 520px;">
            <source src="{{ $musicPlaylist->audioSource() }}">
          </audio>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
