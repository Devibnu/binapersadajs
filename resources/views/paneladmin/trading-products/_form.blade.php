@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Produk</label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tradingProduct->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $tradingProduct->slug) }}" placeholder="otomatis dari nama produk">
      @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Kategori</label>
      <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $tradingProduct->category) }}" required>
      @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" min="0" value="{{ old('sort_order', $tradingProduct->sort_order ?? 0) }}">
      @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" {{ (string) old('is_active', $tradingProduct->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ (string) old('is_active', $tradingProduct->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
      </select>
      @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Foto Produk</label>
      <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
      <small class="text-secondary">Format JPG, PNG, WEBP. Maksimal 2MB.</small>
      @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($tradingProduct->image)
        <img src="{{ $tradingProduct->imageUrl() }}" alt="{{ $tradingProduct->name }}" class="img-fluid border-radius-lg mt-3" style="max-height: 160px;">
      @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Deskripsi Singkat</label>
      <textarea name="short_description" rows="5" class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description', $tradingProduct->short_description) }}</textarea>
      @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" rows="8" class="form-control @error('description') is-invalid @enderror">{{ old('description', $tradingProduct->description) }}</textarea>
      @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Spesifikasi</label>
      <textarea name="specifications" rows="8" class="form-control @error('specifications') is-invalid @enderror" placeholder="Tulis spesifikasi per baris">{{ old('specifications', $tradingProduct->specifications) }}</textarea>
      @error('specifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
  <a href="{{ route('paneladmin.trading-products.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Produk</button>
</div>
