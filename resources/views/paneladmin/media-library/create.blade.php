@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8 col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Upload Media</h6>
        <p class="text-sm mb-0">Tambahkan gambar atau dokumen PDF ke Media Library.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.media-library.store') }}" enctype="multipart/form-data" class="js-confirm-submit">
          @csrf

          <div class="form-group">
            <label>File Media <span class="text-danger">*</span></label>
            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" required>
            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-secondary">Format JPG, JPEG, PNG, WEBP, atau dokumen PDF. Maksimum 5 MB. Gambar akan otomatis dioptimasi dan dikompres.</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255" placeholder="Contoh: Proyek Fabrication Workshop">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Alt Text</label>
                <input type="text" name="alt_text" class="form-control @error('alt_text') is-invalid @enderror" value="{{ old('alt_text') }}" maxlength="255" placeholder="Deskripsi singkat gambar">
                @error('alt_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-secondary">Membantu aksesibilitas dan SEO gambar.</small>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('paneladmin.media-library.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
            <button type="submit" class="btn bg-gradient-primary mb-0">Upload Media</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
