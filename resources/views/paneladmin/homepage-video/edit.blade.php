@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="mb-4">
      <h5>Homepage Video</h5>
      <p class="text-sm mb-0">Kelola video company profile yang tampil pada homepage setelah hero banner.</p>
    </div>

    <form method="POST" action="{{ route('paneladmin.homepage-video.update') }}" enctype="multipart/form-data" class="js-confirm-submit">
      @csrf
      @method('PUT')

      <div class="card border mb-4">
        <div class="card-header pb-0">
          <h6>Konten Video Company Profile</h6>
          <p class="text-xs mb-0">Gunakan link YouTube agar website tetap ringan dan cepat.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Label Section</label>
                <input type="text" name="section_label" class="form-control @error('section_label') is-invalid @enderror" value="{{ old('section_label', $setting->section_label) }}">
                @error('section_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $setting->title) }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $setting->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Link YouTube</label>
                <input type="url" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url', $setting->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=..." required>
                <small class="text-secondary">Mendukung link YouTube watch, youtu.be, shorts, dan embed.</small>
                @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Status</label>
                <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                  <option value="1" {{ (string) old('is_active', (int) $setting->is_active) === '1' ? 'selected' : '' }}>Aktif</option>
                  <option value="0" {{ (string) old('is_active', (int) $setting->is_active) === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <small class="text-secondary">Section nonaktif tidak akan tampil di homepage.</small>
                @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Thumbnail &amp; Tombol</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-5">
              <div class="form-group">
                <label>Upload Thumbnail</label>
                <input id="homepage-video-thumbnail" type="file" name="thumbnail_image" class="form-control @error('thumbnail_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/*">
                <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
                @error('thumbnail_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <img id="homepage-video-preview" src="{{ $setting->thumbnailUrl() }}" alt="Preview Thumbnail Video" class="img-fluid border-radius-lg shadow-sm mt-2" style="max-height: 230px; width: 100%; object-fit: cover;">
            </div>
            <div class="col-lg-7 mt-4 mt-lg-0">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Text Tombol</label>
                    <input type="text" name="button_text" class="form-control @error('button_text') is-invalid @enderror" value="{{ old('button_text', $setting->button_text) }}">
                    @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="form-group">
                    <label>Link Tombol</label>
                    <input type="url" name="button_link" class="form-control @error('button_link') is-invalid @enderror" value="{{ old('button_link', $setting->button_link) }}" placeholder="https://www.youtube.com/">
                    @error('button_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                @for($item = 1; $item <= 4; $item++)
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Feature {{ $item }}</label>
                      <input type="text" name="feature_{{ $item }}" class="form-control @error('feature_' . $item) is-invalid @enderror" value="{{ old('feature_' . $item, $setting->{'feature_' . $item}) }}">
                      @error('feature_' . $item)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Homepage Video</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('homepage-video-thumbnail');
    var preview = document.getElementById('homepage-video-preview');

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];

      if (file) {
        preview.src = URL.createObjectURL(file);
      }
    });
  });
</script>
@endpush
