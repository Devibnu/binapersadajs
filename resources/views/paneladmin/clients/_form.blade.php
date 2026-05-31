@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Client / Perusahaan</label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $client->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Website URL</label>
      <input type="url" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url', $client->website_url) }}" placeholder="https://example.com">
      @error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Logo Client</label>
      <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*,.svg">
      <small class="text-secondary">Opsional. Format jpg, jpeg, png, webp, svg. Maksimal 2MB.</small>
      @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($client->logo)
        <div class="mt-3">
          <img src="{{ $client->logoUrl() }}" alt="Logo {{ $client->name }}" class="border-radius-md border p-2 bg-white" style="max-height: 80px; max-width: 180px;">
        </div>
      @endif
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" min="0" value="{{ old('sort_order', $client->sort_order ?? 0) }}">
      @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" {{ (string) old('is_active', $client->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ (string) old('is_active', $client->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
      </select>
      @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
  <a href="{{ route('paneladmin.clients.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Client</button>
</div>
