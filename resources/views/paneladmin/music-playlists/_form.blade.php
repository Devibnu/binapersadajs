@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Judul Musik</label>
      <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $musicPlaylist->title) }}" required>
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" min="0" value="{{ old('sort_order', $musicPlaylist->sort_order ?? 0) }}">
      @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" {{ (string) old('is_active', $musicPlaylist->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ (string) old('is_active', $musicPlaylist->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
      </select>
      @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Upload MP3</label>
      <input type="file" name="audio_file" class="form-control @error('audio_file') is-invalid @enderror" accept="audio/mpeg,.mp3">
      <small class="text-secondary">Opsional jika URL Audio diisi. Maksimal 10MB.</small>
      @error('audio_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($musicPlaylist->audio_file)
        <div class="mt-3">
          <audio controls preload="none" style="width: 100%;">
            <source src="{{ $musicPlaylist->audioSource() }}">
          </audio>
        </div>
      @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>URL Audio</label>
      <input type="url" name="audio_url" class="form-control @error('audio_url') is-invalid @enderror" value="{{ old('audio_url', $musicPlaylist->audio_url) }}" placeholder="https://example.com/audio.mp3">
      <small class="text-secondary">Opsional jika MP3 diupload. Jika keduanya diisi, file upload diprioritaskan.</small>
      @error('audio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
  <a href="{{ route('paneladmin.music-playlists.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Musik</button>
</div>
