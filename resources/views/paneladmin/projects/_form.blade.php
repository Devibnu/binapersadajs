@csrf

<div class="row">
  <div class="col-12">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Dasar</h6>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Nama Project</label>
      <input type="text" name="title" class="form-control" value="{{ old('title', $project->title) }}" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $project->slug) }}" placeholder="otomatis-dari-nama-project">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Kategori</label>
      <select name="project_category_id" class="form-control">
        <option value="">Tanpa Kategori</option>
        @foreach($projectCategories as $projectCategory)
          <option value="{{ $projectCategory->id }}" {{ (string) old('project_category_id', $project->project_category_id) === (string) $projectCategory->id ? 'selected' : '' }}>
            {{ $projectCategory->name }}
          </option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Client</label>
      <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $project->client_name) }}">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Lokasi Project</label>
      <input type="text" name="project_location" class="form-control" value="{{ old('project_location', $project->project_location) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Tahun Project</label>
      <input type="text" name="project_year" class="form-control" maxlength="20" value="{{ old('project_year', $project->project_year) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $project->sort_order ?? 0) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control" required>
        <option value="active" {{ old('status', $project->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ old('status', $project->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
  </div>
  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Gambar</h6>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Featured Image</label>
      <input type="file" name="featured_image" class="form-control" accept="image/*">
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($project->featured_image)
        <img src="{{ $project->mediaUrl($project->featured_image) }}" alt="Featured Image" class="img-fluid border-radius-lg mt-3" style="max-height: 140px;">
      @endif
    </div>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.projects.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
