@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="mb-4">
      <h5>SEO Settings</h5>
      <p class="text-sm mb-0">Kelola metadata global, indexing Google, social preview, analytics, dan identitas schema website.</p>
    </div>

    <form method="POST" action="{{ route('paneladmin.seo-settings.update') }}" enctype="multipart/form-data" class="js-confirm-submit">
      @csrf
      @method('PUT')

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Global SEO</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Meta Title Default</label>
                <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $setting->meta_title) }}">
                @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Meta Description Default</label>
                <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3">{{ old('meta_description', $setting->meta_description) }}</textarea>
                @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Meta Keywords</label>
                <textarea name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" rows="2">{{ old('meta_keywords', $setting->meta_keywords) }}</textarea>
                @error('meta_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Canonical Domain / Base URL</label>
                <input type="url" name="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror" value="{{ old('canonical_url', $setting->canonical_url) }}" placeholder="https://binapersadajs.co.id">
                <small class="text-secondary">Path halaman akan ditambahkan otomatis pada domain ini.</small>
                @error('canonical_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Open Graph &amp; Twitter Card</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>OG Image Default</label>
                <input type="file" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                <small class="text-secondary">Gambar social preview akan otomatis dioptimasi dan dikompres.</small>
                @if($setting->og_image)
                  <img src="{{ $setting->ogImageUrl($websiteSetting) }}" alt="OG Preview" class="img-fluid border-radius-lg mt-3" style="max-height: 130px;">
                @endif
                @error('og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Twitter Card Type</label>
                <select name="twitter_card_type" class="form-control @error('twitter_card_type') is-invalid @enderror">
                  <option value="summary_large_image" {{ old('twitter_card_type', $setting->twitter_card_type) === 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                  <option value="summary" {{ old('twitter_card_type', $setting->twitter_card_type) === 'summary' ? 'selected' : '' }}>Summary</option>
                </select>
                @error('twitter_card_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Twitter Site</label>
                <input type="text" name="twitter_site" class="form-control @error('twitter_site') is-invalid @enderror" value="{{ old('twitter_site', $setting->twitter_site) }}" placeholder="@akun_perusahaan">
                @error('twitter_site')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Robots &amp; Indexing</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Google Indexing</label>
                <select name="robots_index" class="form-control" required>
                  <option value="1" {{ old('robots_index', $setting->robots_index) ? 'selected' : '' }}>Index - Izinkan muncul di Google</option>
                  <option value="0" {{ ! old('robots_index', $setting->robots_index) ? 'selected' : '' }}>No Index - Blokir indexing</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Link Crawling</label>
                <select name="robots_follow" class="form-control" required>
                  <option value="1" {{ old('robots_follow', $setting->robots_follow) ? 'selected' : '' }}>Follow</option>
                  <option value="0" {{ ! old('robots_follow', $setting->robots_follow) ? 'selected' : '' }}>No Follow</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Google Site Verification</label>
                <input type="text" name="google_site_verification" class="form-control" value="{{ old('google_site_verification', $setting->google_site_verification) }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Analytics</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Google Analytics GA4 ID</label>
                <input type="text" name="google_analytics_id" class="form-control @error('google_analytics_id') is-invalid @enderror" value="{{ old('google_analytics_id', $setting->google_analytics_id) }}" placeholder="G-XXXXXXXXXX">
                @error('google_analytics_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Google Tag Manager ID</label>
                <input type="text" name="google_tag_manager" class="form-control @error('google_tag_manager') is-invalid @enderror" value="{{ old('google_tag_manager', $setting->google_tag_manager) }}" placeholder="GTM-XXXXXXX">
                @error('google_tag_manager')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border mb-4">
        <div class="card-header pb-0"><h6>Schema Organization &amp; Local Business</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Perusahaan Schema</label>
                <input type="text" name="schema_company_name" class="form-control" value="{{ old('schema_company_name', $setting->schema_company_name) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Logo Schema</label>
                <input type="file" name="schema_logo" class="form-control @error('schema_logo') is-invalid @enderror" accept="image/*">
                <small class="text-secondary">Jika kosong, memakai logo Pengaturan Website.</small>
                @if($setting->schema_logo)
                  <img src="{{ $setting->schemaLogoUrl($websiteSetting) }}" alt="Logo Schema" class="img-fluid border-radius-lg mt-3" style="max-height: 80px;">
                @endif
                @error('schema_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="schema_phone" class="form-control" value="{{ old('schema_phone', $setting->schema_phone) }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="schema_email" class="form-control @error('schema_email') is-invalid @enderror" value="{{ old('schema_email', $setting->schema_email) }}">
                @error('schema_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Kota</label>
                <input type="text" name="schema_city" class="form-control" value="{{ old('schema_city', $setting->schema_city) }}">
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Alamat</label>
                <textarea name="schema_address" class="form-control" rows="2">{{ old('schema_address', $setting->schema_address) }}</textarea>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Kode Pos</label>
                <input type="text" name="schema_postal_code" class="form-control" value="{{ old('schema_postal_code', $setting->schema_postal_code) }}">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Negara</label>
                <input type="text" name="schema_country" class="form-control" value="{{ old('schema_country', $setting->schema_country) }}" placeholder="ID">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan SEO Settings</button>
      </div>
    </form>
  </div>
</div>
@endsection
