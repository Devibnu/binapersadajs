@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Halaman</label>
      <select name="page_key" class="form-control" required>
        <option value="" disabled {{ old('page_key', $pageHero->page_key) ? '' : 'selected' }}>Pilih halaman</option>
        <option value="services" {{ old('page_key', $pageHero->page_key) === 'services' ? 'selected' : '' }}>Services</option>
        <option value="projects" {{ old('page_key', $pageHero->page_key) === 'projects' ? 'selected' : '' }}>Projects</option>
        <option value="about" {{ old('page_key', $pageHero->page_key) === 'about' ? 'selected' : '' }}>About</option>
        <option value="contact" {{ old('page_key', $pageHero->page_key) === 'contact' ? 'selected' : '' }}>Contact</option>
        <option value="blog" {{ old('page_key', $pageHero->page_key) === 'blog' ? 'selected' : '' }}>Blog</option>
        <option value="trading" {{ old('page_key', $pageHero->page_key) === 'trading' ? 'selected' : '' }}>Trading</option>
      </select>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Judul Hero</label>
      <input type="text" name="title" class="form-control" value="{{ old('title', $pageHero->title) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Teks Breadcrumb</label>
      <input type="text" name="breadcrumb_text" class="form-control" maxlength="255" value="{{ old('breadcrumb_text', $pageHero->breadcrumb_text) }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Posisi Teks</label>
      <select name="text_position" class="form-control">
        <option value="center" {{ old('text_position', $pageHero->text_position ?? 'center') === 'center' ? 'selected' : '' }}>Tengah</option>
        <option value="left" {{ old('text_position', $pageHero->text_position) === 'left' ? 'selected' : '' }}>Kiri</option>
        <option value="right" {{ old('text_position', $pageHero->text_position) === 'right' ? 'selected' : '' }}>Kanan</option>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Overlay Opacity</label>
      <input type="number" name="overlay_opacity" class="form-control" min="0" max="1" step="0.05" value="{{ old('overlay_opacity', $pageHero->overlay_opacity ?? 1) }}">
    </div>
  </div>
  <div class="col-md-8">
    <div class="form-group">
      <label>Background Image</label>
      <input type="file" name="background_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres. Rekomendasi ukuran 1920x700px, maksimal upload 20MB.</small>
      @if($pageHero->exists)
        <img src="{{ $pageHero->backgroundUrl() }}" alt="{{ $pageHero->title }}" class="img-fluid border-radius-lg mt-3" style="max-height: 180px;">
      @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control" required>
        <option value="1" {{ old('is_active', $pageHero->is_active) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $pageHero->is_active) ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.page-heroes.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
