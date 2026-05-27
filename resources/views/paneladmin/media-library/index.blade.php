@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header pb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <h6>Media Library</h6>
            <p class="text-sm mb-0">Pusat gambar dan dokumen yang diupload secara mandiri melalui panel admin.</p>
          </div>
          <a href="{{ route('paneladmin.media-library.create') }}" class="btn bg-gradient-primary mb-0">
            <i class="fas fa-upload me-1"></i> Upload Media
          </a>
        </div>
        <form method="GET" action="{{ route('paneladmin.media-library.index') }}" class="row mt-3 g-2">
          <div class="col-lg-5 col-md-7">
            <input type="text" name="q" class="form-control" value="{{ $search }}" placeholder="Cari nama, judul, atau alt text...">
          </div>
          <div class="col-lg-3 col-md-5">
            <select name="type" class="form-control">
              <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Media</option>
              <option value="images" {{ $type === 'images' ? 'selected' : '' }}>Gambar</option>
              <option value="documents" {{ $type === 'documents' ? 'selected' : '' }}>Dokumen</option>
            </select>
          </div>
          <div class="col-lg-4 d-flex gap-2">
            <button type="submit" class="btn bg-gradient-secondary mb-0">Filter</button>
            <a href="{{ route('paneladmin.media-library.index') }}" class="btn btn-outline-secondary mb-0">Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  @forelse($mediaFiles as $mediaFile)
    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
      <div class="card h-100 shadow-sm">
        @if($mediaFile->isImage())
          <img src="{{ $mediaFile->publicUrl() }}" alt="{{ $mediaFile->alt_text ?: $mediaFile->original_name }}" class="card-img-top border-radius-lg" style="height: 190px; object-fit: cover;">
        @else
          <div class="d-flex justify-content-center align-items-center bg-gray-100 border-radius-lg" style="height: 190px;">
            <i class="fas fa-file-pdf fa-3x text-danger"></i>
          </div>
        @endif
        <div class="card-body px-3 pb-3 pt-3">
          <h6 class="text-sm mb-1 text-truncate" title="{{ $mediaFile->title ?: $mediaFile->original_name }}">
            {{ $mediaFile->title ?: $mediaFile->original_name }}
          </h6>
          <p class="text-xs text-secondary text-truncate mb-2">{{ $mediaFile->original_name }}</p>
          <p class="text-xs text-secondary mb-1">
            <i class="fas fa-hdd me-1"></i>{{ $mediaFile->formattedSize() }}
            @if($mediaFile->isImage())
              <span class="mx-1">|</span>{{ $mediaFile->dimensionsLabel() }}
            @endif
          </p>
          <p class="text-xs text-secondary mb-3">
            <i class="far fa-calendar me-1"></i>{{ $mediaFile->created_at->format('d M Y') }}
          </p>
          <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('paneladmin.media-library.show', $mediaFile) }}" class="btn btn-outline-primary btn-sm mb-0">Detail</a>
            <button type="button" class="btn btn-outline-secondary btn-sm mb-0 js-copy-media-url" data-url="{{ $mediaFile->publicUrl() }}">Copy URL</button>
            <a href="{{ route('paneladmin.media-library.download', $mediaFile) }}" class="btn btn-outline-secondary btn-sm mb-0">Download</a>
            <form method="POST" action="{{ route('paneladmin.media-library.destroy', $mediaFile) }}" class="d-inline js-confirm-delete">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-link text-danger text-xs p-0 mb-0">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-body text-center py-5 text-secondary">
          <i class="fas fa-photo-video fa-2x mb-3"></i>
          <p class="text-sm mb-0">
            Belum ada media yang tersedia.
          </p>
        </div>
      </div>
    </div>
  @endforelse
  @if($mediaFiles->hasPages())
    <div class="col-12 mb-4">{{ $mediaFiles->links() }}</div>
  @endif
</div>
@endsection

@include('paneladmin.media-library._copy-script')
