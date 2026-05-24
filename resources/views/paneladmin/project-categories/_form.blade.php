@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Kategori</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $projectCategory->name) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $projectCategory->slug) }}" placeholder="otomatis-dari-nama-kategori">
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description', $projectCategory->description) }}</textarea>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $projectCategory->sort_order ?? 0) }}">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control" required>
        <option value="1" {{ old('is_active', $projectCategory->is_active) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $projectCategory->is_active) ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.project-categories.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
