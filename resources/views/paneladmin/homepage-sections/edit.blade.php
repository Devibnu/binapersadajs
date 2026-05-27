@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="mb-4">
      <h5>Homepage Sections</h5>
      <p class="text-sm mb-0">Kelola konten section homepage tanpa mengubah hero banner, layanan, project, atau artikel.</p>
    </div>
    <form method="POST" action="{{ route('paneladmin.homepage-sections.update') }}" class="js-confirm-submit">
          @csrf
          @method('PUT')

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>About Company</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Label Bagian</label>
                    <input type="text" name="about_label" class="form-control @error('about_label') is-invalid @enderror" value="{{ old('about_label', $setting->about_label) }}">
                    @error('about_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <label>Judul About</label>
                    <input type="text" name="about_title" class="form-control @error('about_title') is-invalid @enderror" value="{{ old('about_title', $setting->about_title) }}">
                    @error('about_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label>Deskripsi About</label>
                    <textarea name="about_description" class="form-control @error('about_description') is-invalid @enderror" rows="3">{{ old('about_description', $setting->about_description) }}</textarea>
                    @error('about_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                @for($item = 1; $item <= 4; $item++)
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-7">
                        <div class="form-group">
                          <label>Feature {{ $item }}</label>
                          <input type="text" name="about_feature_{{ $item }}_title" class="form-control" value="{{ old('about_feature_' . $item . '_title', $setting->{'about_feature_' . $item . '_title'}) }}">
                        </div>
                      </div>
                      <div class="col-5">
                        <div class="form-group">
                          <label>Icon Class</label>
                          <input type="text" name="about_feature_{{ $item }}_icon" class="form-control" value="{{ old('about_feature_' . $item . '_icon', $setting->{'about_feature_' . $item . '_icon'}) }}" placeholder="fa-tools">
                        </div>
                      </div>
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>Our Values</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Judul Values</label>
                    <input type="text" name="values_title" class="form-control" value="{{ old('values_title', $setting->values_title) }}">
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <label>Deskripsi Values</label>
                    <textarea name="values_description" class="form-control" rows="2">{{ old('values_description', $setting->values_description) }}</textarea>
                  </div>
                </div>
                @for($item = 1; $item <= 3; $item++)
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Judul Value {{ $item }}</label>
                      <input type="text" name="value_{{ $item }}_title" class="form-control" value="{{ old('value_' . $item . '_title', $setting->{'value_' . $item . '_title'}) }}">
                    </div>
                    <div class="form-group">
                      <label>Isi Value {{ $item }}</label>
                      <textarea name="value_{{ $item }}_description" class="form-control" rows="4">{{ old('value_' . $item . '_description', $setting->{'value_' . $item . '_description'}) }}</textarea>
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0">
              <h6>Counter Stats</h6>
              <p class="text-xs mb-0">Nomor dapat mengandung akhiran, misalnya <strong>100%</strong> atau <strong>24/7</strong>.</p>
            </div>
            <div class="card-body">
              <div class="row">
                @for($item = 1; $item <= 4; $item++)
                  <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                      <label>Angka {{ $item }}</label>
                      <input type="text" name="counter_{{ $item }}_number" class="form-control" value="{{ old('counter_' . $item . '_number', $setting->{'counter_' . $item . '_number'}) }}">
                    </div>
                    <div class="form-group">
                      <label>Label {{ $item }}</label>
                      <input type="text" name="counter_{{ $item }}_label" class="form-control" value="{{ old('counter_' . $item . '_label', $setting->{'counter_' . $item . '_label'}) }}">
                    </div>
                    <div class="form-group">
                      <label>Icon Class</label>
                      <input type="text" name="counter_{{ $item }}_icon" class="form-control" value="{{ old('counter_' . $item . '_icon', $setting->{'counter_' . $item . '_icon'}) }}" placeholder="fa-industry">
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>Heading Section Layanan</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Label Section Layanan</label>
                    <input type="text" name="service_section_label" class="form-control @error('service_section_label') is-invalid @enderror" value="{{ old('service_section_label', $setting->service_section_label) }}">
                    @error('service_section_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Judul Section Layanan</label>
                    <input type="text" name="service_section_title" class="form-control @error('service_section_title') is-invalid @enderror" value="{{ old('service_section_title', $setting->service_section_title) }}">
                    @error('service_section_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>Quality &amp; HSE Commitment</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="quality_title" class="form-control" value="{{ old('quality_title', $setting->quality_title) }}">
                  </div>
                  <div class="form-group">
                    <label>Deskripsi Utama</label>
                    <textarea name="quality_description" class="form-control" rows="3">{{ old('quality_description', $setting->quality_description) }}</textarea>
                  </div>
                  <div class="form-group">
                    <label>Deskripsi Tambahan</label>
                    <textarea name="quality_sub_description" class="form-control" rows="3">{{ old('quality_sub_description', $setting->quality_sub_description) }}</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  @for($item = 1; $item <= 4; $item++)
                    <div class="form-group">
                      <label>Item Komitmen {{ $item }}</label>
                      <input type="text" name="quality_item_{{ $item }}" class="form-control" value="{{ old('quality_item_' . $item, $setting->{'quality_item_' . $item}) }}">
                    </div>
                  @endfor
                </div>
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>CTA Strip</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Label Telepon</label>
                    <input type="text" name="cta_phone_label" class="form-control" value="{{ old('cta_phone_label', $setting->cta_phone_label) }}">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="cta_phone" class="form-control" value="{{ old('cta_phone', $setting->cta_phone) }}">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Judul CTA</label>
                    <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $setting->cta_title) }}">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Deskripsi CTA</label>
                    <textarea name="cta_description" class="form-control" rows="2">{{ old('cta_description', $setting->cta_description) }}</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teks Tombol</label>
                    <input type="text" name="cta_button_text" class="form-control" value="{{ old('cta_button_text', $setting->cta_button_text) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Link Tombol</label>
                    <input type="text" name="cta_button_link" class="form-control" value="{{ old('cta_button_link', $setting->cta_button_link) }}" placeholder="/contact">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card border mb-4">
            <div class="card-header pb-0"><h6>Latest Blog Heading</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Label Blog</label>
                    <input type="text" name="blog_label" class="form-control" value="{{ old('blog_label', $setting->blog_label) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Judul Blog</label>
                    <input type="text" name="blog_title" class="form-control" value="{{ old('blog_title', $setting->blog_title) }}">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Homepage Sections</button>
          </div>
    </form>
  </div>
</div>
@endsection
