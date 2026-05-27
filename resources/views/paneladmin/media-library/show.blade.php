@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-7 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6>Preview Media</h6>
      </div>
      <div class="card-body">
        @if($mediaFile->isImage())
          <img src="{{ $mediaFile->publicUrl() }}" alt="{{ $mediaFile->alt_text ?: $mediaFile->original_name }}" class="img-fluid border-radius-lg w-100" style="max-height: 560px; object-fit: contain;">
        @else
          <div class="bg-gray-100 border-radius-lg d-flex justify-content-center align-items-center" style="height: 360px;">
            <i class="fas fa-file-pdf fa-4x text-danger"></i>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-5 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6>Detail Media</h6>
      </div>
      <div class="card-body">
        <dl class="row text-sm mb-4">
          <dt class="col-sm-4 text-secondary">Judul</dt>
          <dd class="col-sm-8">{{ $mediaFile->title ?: '-' }}</dd>
          <dt class="col-sm-4 text-secondary">Nama File</dt>
          <dd class="col-sm-8 text-break">{{ $mediaFile->original_name }}</dd>
          <dt class="col-sm-4 text-secondary">File Tersimpan</dt>
          <dd class="col-sm-8 text-break">{{ $mediaFile->file_name }}</dd>
          <dt class="col-sm-4 text-secondary">Path</dt>
          <dd class="col-sm-8 text-break">{{ $mediaFile->path }}</dd>
          <dt class="col-sm-4 text-secondary">URL</dt>
          <dd class="col-sm-8 text-break">{{ $mediaFile->publicUrl() }}</dd>
          <dt class="col-sm-4 text-secondary">MIME Type</dt>
          <dd class="col-sm-8">{{ $mediaFile->mime_type }}</dd>
          <dt class="col-sm-4 text-secondary">Ukuran</dt>
          <dd class="col-sm-8">{{ $mediaFile->formattedSize() }}</dd>
          <dt class="col-sm-4 text-secondary">Dimensi</dt>
          <dd class="col-sm-8">{{ $mediaFile->dimensionsLabel() }}</dd>
          <dt class="col-sm-4 text-secondary">Alt Text</dt>
          <dd class="col-sm-8">{{ $mediaFile->alt_text ?: '-' }}</dd>
          <dt class="col-sm-4 text-secondary">Uploader</dt>
          <dd class="col-sm-8">{{ $mediaFile->uploader?->name ?: '-' }}</dd>
          <dt class="col-sm-4 text-secondary">Tanggal</dt>
          <dd class="col-sm-8">{{ $mediaFile->created_at->format('d M Y H:i') }}</dd>
        </dl>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('paneladmin.media-library.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
          <a href="{{ route('paneladmin.media-library.download', $mediaFile) }}" class="btn bg-gradient-primary mb-0">
            <i class="fas fa-download me-1"></i> Download
          </a>
          <button type="button" class="btn btn-outline-primary mb-0 js-copy-media-url" data-url="{{ $mediaFile->publicUrl() }}">Copy URL</button>
          <form method="POST" action="{{ route('paneladmin.media-library.destroy', $mediaFile) }}" class="d-inline js-confirm-delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger mb-0">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@include('paneladmin.media-library._copy-script')
