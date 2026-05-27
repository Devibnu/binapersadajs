@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama</label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $aboutTeam->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Jabatan</label>
      <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $aboutTeam->position) }}" required>
      @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-8">
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $aboutTeam->description) }}</textarea>
      @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Foto</label>
      <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
      @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($aboutTeam->exists && $aboutTeam->image)
        <img src="{{ $aboutTeam->imageUrl() }}" alt="{{ $aboutTeam->name }}" class="img-fluid border-radius-lg mt-3" style="max-height: 130px;">
      @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>LinkedIn URL</label>
      <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" value="{{ old('linkedin_url', $aboutTeam->linkedin_url) }}">
      @error('linkedin_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Instagram URL</label>
      <input type="url" name="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror" value="{{ old('instagram_url', $aboutTeam->instagram_url) }}">
      @error('instagram_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Twitter / X URL</label>
      <input type="url" name="twitter_url" class="form-control @error('twitter_url') is-invalid @enderror" value="{{ old('twitter_url', $aboutTeam->twitter_url) }}">
      @error('twitter_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" min="0" value="{{ old('sort_order', $aboutTeam->sort_order ?? 0) }}">
      @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" {{ old('is_active', $aboutTeam->is_active) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $aboutTeam->is_active) ? 'selected' : '' }}>Nonaktif</option>
      </select>
      @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <small class="text-secondary">Anggota nonaktif tidak akan tampil di halaman About.</small>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.about-teams.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
