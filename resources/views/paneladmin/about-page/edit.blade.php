@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="mb-4">
      <h5>About Page</h5>
      <p class="text-sm mb-2">Kelola isi halaman About tanpa mengubah struktur tampilan Constra.</p>
      <p class="text-sm text-secondary mb-0">
        Banner hero halaman About dikelola melalui menu Page Heroes.
        <a href="{{ route('paneladmin.page-heroes.index') }}" class="font-weight-bold">Kelola Page Hero</a>
      </p>
    </div>
    <form method="POST" action="{{ route('paneladmin.about-page.update') }}" enctype="multipart/form-data" class="js-confirm-submit">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-header pb-0"><h6>Who We Are</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Label Bagian</label>
                <input type="text" name="section_label" class="form-control" value="{{ old('section_label', $setting->section_label) }}">
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Judul Tambahan</label>
                <input type="text" name="section_title" class="form-control" value="{{ old('section_title', $setting->section_title) }}">
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Deskripsi Utama</label>
                <textarea name="section_description" class="form-control" rows="3">{{ old('section_description', $setting->section_description) }}</textarea>
              </div>
              <div class="form-group">
                <label>Kutipan</label>
                <textarea name="quote_text" class="form-control" rows="3">{{ old('quote_text', $setting->quote_text) }}</textarea>
              </div>
              <div class="form-group">
                <label>Deskripsi Bawah</label>
                <textarea name="section_description_bottom" class="form-control" rows="3">{{ old('section_description_bottom', $setting->section_description_bottom) }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header pb-0"><h6>Right Slider</h6></div>
        <div class="card-body">
          <div class="row">
            @for($item = 1; $item <= 3; $item++)
              <div class="col-lg-4 col-md-6">
                <div class="form-group">
                  <label>Judul Slide {{ $item }}</label>
                  <input type="text" name="slider_{{ $item }}_title" class="form-control" value="{{ old('slider_' . $item . '_title', $setting->{'slider_' . $item . '_title'}) }}">
                </div>
                <div class="form-group">
                  <label>Gambar Slide {{ $item }}</label>
                  <input type="file" name="slider_{{ $item }}_image" class="form-control" accept="image/*">
                  <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
                  <img src="{{ $setting->imageUrl('slider_' . $item . '_image') }}" alt="Slide {{ $item }}" class="img-fluid border-radius-lg mt-3" style="max-height: 115px;">
                </div>
              </div>
            @endfor
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Counter</h6>
          <p class="text-xs mb-0">Gunakan angka saja agar animasi counter tetap berjalan.</p>
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
                  <input type="text" name="counter_{{ $item }}_icon" class="form-control" value="{{ old('counter_' . $item . '_icon', $setting->{'counter_' . $item . '_icon'}) }}" placeholder="fa-hard-hat">
                </div>
              </div>
            @endfor
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header pb-0"><h6>Team Heading</h6></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Label Tim</label>
                <input type="text" name="team_label" class="form-control" value="{{ old('team_label', $setting->team_label) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Judul Tim</label>
                <input type="text" name="team_title" class="form-control" value="{{ old('team_title', $setting->team_title) }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn bg-gradient-primary mb-0">Simpan About Page</button>
      </div>
    </form>
  </div>
</div>
@endsection
