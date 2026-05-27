@csrf

<div class="row">
  <div class="col-12">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Artikel</h6>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Judul Artikel</label>
      <input type="text" id="blog-title" name="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" id="blog-slug" name="slug" class="form-control" value="{{ old('slug', $blog->slug) }}" placeholder="otomatis-dari-judul-artikel">
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Ringkasan</label>
      <textarea id="blog-excerpt" name="excerpt" class="form-control" rows="3" maxlength="500">{{ old('excerpt', $blog->excerpt) }}</textarea>
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group">
      <label>Isi Artikel</label>
      <textarea id="blog-content-editor" name="content" class="form-control" rows="10">{{ old('content', $blog->content) }}</textarea>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Kategori</label>
      <input type="text" name="category" class="form-control" value="{{ old('category', $blog->category) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Tag</label>
      <input type="text" name="tags" class="form-control" value="{{ old('tags', $blog->tags) }}" placeholder="Maintenance, Industri, Safety">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Penulis</label>
      <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $blog->author_name) }}" placeholder="Admin">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Tanggal Publish</label>
      <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', $blog->published_at?->format('Y-m-d\TH:i')) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Status Publish</label>
      <select name="is_published" class="form-control" required>
        <option value="1" {{ old('is_published', $blog->is_published) ? 'selected' : '' }}>Terbit</option>
        <option value="0" {{ ! old('is_published', $blog->is_published) ? 'selected' : '' }}>Draft</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $blog->sort_order ?? 0) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Gambar Utama</label>
      <input type="file" id="blog-featured-image" name="featured_image" class="form-control" accept="image/*" data-has-image="{{ $blog->featured_image ? '1' : '0' }}">
      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($blog->featured_image)
        <img src="{{ $blog->featuredImageUrl() }}" alt="{{ $blog->title }}" class="img-fluid border-radius-lg mt-3" style="max-height: 120px;">
      @endif
    </div>
  </div>

  <div class="col-12 mt-3">
    <hr class="horizontal dark">
    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">SEO</h6>
    <p class="text-sm text-secondary mb-3">Kosongkan field SEO untuk menggunakan judul, ringkasan, dan gambar utama artikel.</p>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Meta Title</label>
      <input type="text" id="blog-meta-title" name="meta_title" class="form-control" maxlength="255" value="{{ old('meta_title', $blog->meta_title) }}" placeholder="{{ $blog->title ?: 'Judul artikel sebagai fallback' }}">
      <small id="seo-meta-title-counter" class="text-secondary">0 / 60 karakter</small>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Meta Keywords</label>
      <input type="text" id="blog-meta-keywords" name="meta_keywords" class="form-control" maxlength="500" value="{{ old('meta_keywords', $blog->meta_keywords) }}" placeholder="maintenance, fabrication, industri">
    </div>
  </div>
  <div class="col-md-8">
    <div class="form-group">
      <label>Meta Description</label>
      <textarea id="blog-meta-description" name="meta_description" class="form-control" rows="3" maxlength="500" placeholder="Ringkasan artikel akan digunakan jika dikosongkan.">{{ old('meta_description', $blog->meta_description) }}</textarea>
      <small id="seo-meta-description-counter" class="text-secondary">0 / 160 karakter</small>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>OG Image</label>
      <input type="file" id="blog-og-image" name="og_image" class="form-control" accept="image/*" data-has-image="{{ $blog->og_image ? '1' : '0' }}">
      <small class="text-secondary">Jika kosong, memakai Gambar Utama. Gambar akan otomatis dioptimasi dan dikompres.</small>
      @if($blog->og_image)
        <img src="{{ $blog->ogImageUrl() }}" alt="OG Image {{ $blog->title }}" class="img-fluid border-radius-lg mt-3" style="max-height: 120px;">
      @endif
    </div>
  </div>

  <div class="col-12 mt-3">
    <div id="seo-analyzer" class="bg-gray-100 border-radius-lg p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
          <h6 class="mb-1">SEO Analyzer</h6>
          <p class="text-sm text-secondary mb-0">Analisis diperbarui otomatis saat artikel ditulis.</p>
        </div>
        <span id="seo-score-badge" class="badge bg-gradient-danger mt-2 mt-sm-0">SEO Score: 0%</span>
      </div>
      <div class="progress mb-4" style="height: 8px;">
        <div id="seo-score-progress" class="progress-bar bg-gradient-danger" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
      </div>
      <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2">Checklist SEO</p>
          <ul id="seo-checklist" class="list-unstyled text-sm mb-0"></ul>
        </div>
        <div class="col-lg-6">
          <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2">Preview Google Search</p>
          <div class="bg-white border-radius-lg p-3">
            <p id="seo-preview-title" class="mb-1" style="color: #1a0dab; font-size: 18px; line-height: 1.35;"></p>
            <p id="seo-preview-url" class="text-success text-sm mb-1"></p>
            <p id="seo-preview-description" class="text-sm text-secondary mb-0"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <a href="{{ route('paneladmin.blogs.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>

@once
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@8/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var contentField = document.querySelector('#blog-content-editor');
        var titleField = document.querySelector('#blog-title');
        var slugField = document.querySelector('#blog-slug');
        var excerptField = document.querySelector('#blog-excerpt');
        var metaTitleField = document.querySelector('#blog-meta-title');
        var metaDescriptionField = document.querySelector('#blog-meta-description');
        var keywordsField = document.querySelector('#blog-meta-keywords');
        var featuredImageField = document.querySelector('#blog-featured-image');
        var ogImageField = document.querySelector('#blog-og-image');
        var scoreBadge = document.querySelector('#seo-score-badge');
        var scoreProgress = document.querySelector('#seo-score-progress');
        var checklist = document.querySelector('#seo-checklist');
        var previewTitle = document.querySelector('#seo-preview-title');
        var previewUrl = document.querySelector('#seo-preview-url');
        var previewDescription = document.querySelector('#seo-preview-description');
        var titleCounter = document.querySelector('#seo-meta-title-counter');
        var descriptionCounter = document.querySelector('#seo-meta-description-counter');

        if (! contentField) {
          return;
        }

        function textValue(field) {
          return field ? field.value.trim() : '';
        }

        function hasImage(field) {
          return field && ((field.files && field.files.length > 0) || field.dataset.hasImage === '1');
        }

        function slugify(value) {
          return value.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        }

        function articleText() {
          var editor = window.tinymce && window.tinymce.get('blog-content-editor');
          var value = editor ? editor.getContent({ format: 'text' }) : contentField.value.replace(/<[^>]*>/g, ' ');

          return value.replace(/\s+/g, ' ').trim();
        }

        function updateAnalyzer() {
          var title = textValue(titleField);
          var effectiveTitle = textValue(metaTitleField) || title;
          var excerpt = textValue(excerptField);
          var effectiveDescription = textValue(metaDescriptionField) || excerpt;
          var slug = textValue(slugField) || slugify(title) || 'slug-artikel';
          var words = articleText() ? articleText().split(/\s+/).length : 0;
          var featuredAvailable = hasImage(featuredImageField);
          var ogAvailable = hasImage(ogImageField) || featuredAvailable;

          function optimalLength(value, label, minimum, maximum) {
            if (! value.length) {
              return { pass: false, status: 'fail', text: label + ' belum tersedia' };
            }

            if (value.length < minimum) {
              return { pass: false, status: 'warning', text: label + ' terlalu pendek (' + value.length + '/' + minimum + ' karakter)' };
            }

            if (value.length > maximum) {
              return { pass: false, status: 'warning', text: label + ' terlalu panjang (' + value.length + '/' + maximum + ' karakter)' };
            }

            return { pass: true, status: 'pass', text: label + ' optimal (' + value.length + ' karakter)' };
          }

          var checks = [
            optimalLength(effectiveTitle, 'Meta title', 50, 60),
            optimalLength(effectiveDescription, 'Meta description', 120, 160),
            { pass: textValue(keywordsField).length > 0, status: textValue(keywordsField).length > 0 ? 'pass' : 'fail', text: textValue(keywordsField).length > 0 ? 'Meta keywords tersedia' : 'Meta keywords belum tersedia' },
            { pass: title.length >= 20, status: title.length >= 20 ? 'pass' : 'fail', text: title.length >= 20 ? 'Judul artikel sudah memadai' : 'Judul artikel kurang dari 20 karakter' },
            { pass: excerpt.length > 0, status: excerpt.length > 0 ? 'pass' : 'fail', text: excerpt.length > 0 ? 'Ringkasan tersedia' : 'Ringkasan belum tersedia' },
            { pass: featuredAvailable, status: featuredAvailable ? 'pass' : 'fail', text: featuredAvailable ? 'Gambar utama tersedia' : 'Gambar utama belum tersedia' },
            { pass: ogAvailable, status: ogAvailable ? 'pass' : 'fail', text: ogAvailable ? 'OG image tersedia atau memakai fallback gambar utama' : 'OG image dan gambar fallback belum tersedia' },
            { pass: words >= 300, status: words >= 300 ? 'pass' : 'fail', text: words >= 300 ? 'Isi artikel memenuhi minimal 300 kata' : 'Isi artikel kurang dari 300 kata (' + words + '/300)' }
          ];
          var passed = checks.filter(function (check) { return check.pass; }).length;
          var score = Math.round((passed / checks.length) * 100);
          var colorClass = score >= 75 ? 'bg-gradient-success' : (score >= 50 ? 'bg-gradient-warning' : 'bg-gradient-danger');

          scoreBadge.className = 'badge ' + colorClass + ' mt-2 mt-sm-0';
          scoreBadge.textContent = 'SEO Score: ' + score + '%';
          scoreProgress.className = 'progress-bar ' + colorClass;
          scoreProgress.style.width = score + '%';
          scoreProgress.setAttribute('aria-valuenow', score);
          checklist.innerHTML = checks.map(function (check) {
            var iconClass = check.status === 'pass'
              ? 'fa-check-circle text-success'
              : (check.status === 'warning' ? 'fa-exclamation-circle text-warning' : 'fa-times-circle text-danger');
            return '<li class="mb-2"><i class="fas ' + iconClass + ' me-2"></i>' + check.text + '</li>';
          }).join('');

          titleCounter.textContent = effectiveTitle.length + ' / 60 karakter' + (textValue(metaTitleField) ? '' : ' (fallback judul)');
          descriptionCounter.textContent = effectiveDescription.length + ' / 160 karakter' + (textValue(metaDescriptionField) ? '' : ' (fallback ringkasan)');
          previewTitle.textContent = effectiveTitle || 'Judul artikel Anda';
          previewUrl.textContent = @json(url('/blog')) + '/' + slug;
          previewDescription.textContent = effectiveDescription || 'Ringkasan artikel akan tampil sebagai cuplikan hasil pencarian.';
        }

        [titleField, slugField, excerptField, metaTitleField, metaDescriptionField, keywordsField, featuredImageField, ogImageField].forEach(function (field) {
          if (field) {
            field.addEventListener('input', updateAnalyzer);
            field.addEventListener('change', updateAnalyzer);
          }
        });

        tinymce.init({
          selector: '#blog-content-editor',
          license_key: 'gpl',
          height: 540,
          menubar: 'edit view insert format tools table',
          plugins: 'lists link image table code fullscreen',
          toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code fullscreen',
          branding: false,
          promotion: false,
          automatic_uploads: true,
          convert_urls: false,
          setup: function (editor) {
            editor.on('init input change keyup undo redo', updateAnalyzer);
          },
          images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
              var formData = new FormData();
              formData.append('file', blobInfo.blob(), blobInfo.filename());

              fetch(@json(route('paneladmin.editor.upload-image')), {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: formData
              }).then(function (response) {
                if (! response.ok) {
                  throw new Error('Upload gambar gagal.');
                }

                return response.json();
              }).then(function (result) {
                if (! result.location) {
                  throw new Error('URL gambar tidak ditemukan.');
                }

                resolve(result.location);
              }).catch(function (error) {
                reject(error.message);
              });
            });
          }
        });

        updateAnalyzer();
      });
    </script>
  @endpush
@endonce
