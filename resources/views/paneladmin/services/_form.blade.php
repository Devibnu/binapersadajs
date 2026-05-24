@csrf

<div class="row">
  <div class="col-12">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Dasar</h6>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Service</label>
      <input type="text" name="title" class="form-control" value="{{ old('title', $service->title) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $service->slug) }}" placeholder="otomatis-dari-nama-service">
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Deskripsi Singkat</label>
      <input type="text" name="short_description" class="form-control" maxlength="255" value="{{ old('short_description', $service->short_description) }}">
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description', $service->description) }}</textarea>
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Ringkasan Halaman Detail</label>
      <textarea name="short_content" class="form-control" rows="3">{{ old('short_content', $service->short_content) }}</textarea>
    </div>
  </div>
  <div class="col-md-5">
    <div class="form-group">
      <label>Icon</label>
      <input type="file" name="icon" class="form-control" accept="image/*">
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($service->icon)
        <img src="{{ $service->iconUrl() }}" alt="Icon {{ $service->title }}" class="mt-3" style="max-height: 56px;">
      @endif
    </div>
  </div>
  <div class="col-md-7">
    <div class="form-group">
      <label>Gambar</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($service->exists)
        <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}" class="img-fluid border-radius-lg mt-3" style="max-height: 160px;">
      @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control" required>
        <option value="1" {{ old('is_active', $service->is_active) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $service->is_active) ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
    </div>
  </div>

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Konten Detail</h6>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Isi Detail Service</label>
      <textarea name="content" class="form-control" rows="8">{{ old('content', $service->content) }}</textarea>
    </div>
  </div>

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Feature List</h6>
  </div>
  @for($number = 1; $number <= 4; $number++)
    <div class="col-md-6">
      <div class="form-group">
        <label>Feature {{ $number }}</label>
        <input type="text" name="feature_{{ $number }}" class="form-control" maxlength="255" value="{{ old('feature_' . $number, $service->{'feature_' . $number}) }}">
      </div>
    </div>
  @endfor

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Gallery</h6>
  </div>
  @for($number = 1; $number <= 3; $number++)
    @php($galleryField = 'gallery_image_' . $number)
    <div class="col-md-4">
      <div class="form-group">
        <label>Gambar Galeri {{ $number }}</label>
        <input type="file" name="{{ $galleryField }}" class="form-control" accept="image/*">
        <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
        @if($service->{$galleryField})
          <img src="{{ $service->mediaUrl($service->{$galleryField}) }}" alt="Gallery {{ $number }}" class="img-fluid border-radius-lg mt-3" style="max-height: 120px;">
        @endif
      </div>
    </div>
  @endfor

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">FAQ</h6>
  </div>
  @for($number = 1; $number <= 3; $number++)
    <div class="col-md-6">
      <div class="form-group">
        <label>Pertanyaan {{ $number }}</label>
        <input type="text" name="faq_{{ $number }}_question" class="form-control" maxlength="255" value="{{ old('faq_' . $number . '_question', $service->{'faq_' . $number . '_question'}) }}">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>Jawaban {{ $number }}</label>
        <textarea name="faq_{{ $number }}_answer" class="form-control" rows="3">{{ old('faq_' . $number . '_answer', $service->{'faq_' . $number . '_answer'}) }}</textarea>
      </div>
    </div>
  @endfor

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">CTA</h6>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Teks CTA</label>
      <input type="text" name="cta_text" class="form-control" maxlength="255" value="{{ old('cta_text', $service->cta_text) }}" placeholder="Interested with this service?">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Teks Tombol CTA</label>
      <input type="text" name="cta_button_text" class="form-control" maxlength="255" value="{{ old('cta_button_text', $service->cta_button_text) }}" placeholder="Get a Quote">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Link Tombol CTA</label>
      <input type="text" name="cta_button_link" class="form-control" maxlength="255" value="{{ old('cta_button_link', $service->cta_button_link) }}" placeholder="/contact">
    </div>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.services.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
