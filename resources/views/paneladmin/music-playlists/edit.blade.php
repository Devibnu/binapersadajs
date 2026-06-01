@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Edit Music Playlist</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.music-playlists.update', $musicPlaylist) }}" enctype="multipart/form-data" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.music-playlists._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
