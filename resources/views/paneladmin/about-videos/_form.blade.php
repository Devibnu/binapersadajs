@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Judul Video</label>
      <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $aboutVideo->title) }}">
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>YouTube URL</label>
      <input type="url" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url', $aboutVideo->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=xxxx" required>
      @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Thumbnail</label>
      <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
      <small class="text-secondary">Opsional. Jika kosong, thumbnail otomatis dari YouTube akan digunakan.</small>
      @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($aboutVideo->thumbnailUrl())
        <img src="{{ $aboutVideo->thumbnailUrl() }}" alt="{{ $aboutVideo->displayTitle() }}" class="img-fluid border-radius-lg mt-3" style="max-height: 130px;">
      @endif
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" min="0" value="{{ old('sort_order', $aboutVideo->sort_order ?? 0) }}">
      @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" {{ (string) old('is_active', $aboutVideo->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ (string) old('is_active', $aboutVideo->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
      </select>
      @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
  <a href="{{ route('paneladmin.about-videos.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Video</button>
</div>
