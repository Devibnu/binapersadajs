@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Video About</h6>
        @if(auth()->user()?->canAccess('about-videos.update'))
          <a href="{{ route('paneladmin.about-videos.edit', $aboutVideo) }}" class="btn bg-gradient-primary mb-0">Edit Video</a>
        @endif
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6 mb-4 mb-lg-0">
            @if($aboutVideo->embedUrl())
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="{{ $aboutVideo->embedUrl() }}" title="{{ $aboutVideo->displayTitle() }}" allowfullscreen loading="lazy"></iframe>
              </div>
            @endif
          </div>
          <div class="col-lg-6">
            <h4>{{ $aboutVideo->displayTitle() }}</h4>
            <p class="text-sm mb-1"><strong>YouTube URL:</strong> <a href="{{ $aboutVideo->youtube_url }}" target="_blank" rel="noopener">{{ $aboutVideo->youtube_url }}</a></p>
            <p class="text-sm mb-1"><strong>YouTube ID:</strong> {{ $aboutVideo->youtube_id ?: '-' }}</p>
            <p class="text-sm mb-1"><strong>Urutan:</strong> {{ $aboutVideo->sort_order }}</p>
            <p class="text-sm mb-3"><strong>Status:</strong>
              <span class="badge badge-sm {{ $aboutVideo->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                {{ $aboutVideo->is_active ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
