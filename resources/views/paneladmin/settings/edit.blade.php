@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Pengaturan Website</h6>
        <p class="text-sm mb-0">Kelola identitas, kontak, logo, favicon, dan sosial media website.</p>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success text-white">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
          <div class="alert alert-warning text-white">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger text-white">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('paneladmin.settings.update') }}" enctype="multipart/form-data" class="js-confirm-submit">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $setting->nama_perusahaan) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $setting->telepon) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $setting->whatsapp) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @if($setting->logo)
                  <img src="{{ Storage::url($setting->logo) }}?v={{ optional($setting->updated_at)->timestamp ?? time() }}" alt="Logo" class="mt-3" style="max-height: 60px;">
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Favicon</label>
                <input type="file" name="favicon" class="form-control" accept="image/*,.ico">
                @if($setting->favicon)
                  <img src="{{ Storage::url($setting->favicon) }}?v={{ optional($setting->updated_at)->timestamp ?? time() }}" alt="Favicon" class="mt-3" style="max-height: 40px;">
                @endif
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Deskripsi Perusahaan</label>
                <textarea name="deskripsi_perusahaan" class="form-control" rows="4">{{ old('deskripsi_perusahaan', $setting->deskripsi_perusahaan) }}</textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $setting->alamat) }}</textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Google Maps</label>
                <textarea name="google_maps" class="form-control" rows="3">{{ old('google_maps', $setting->google_maps) }}</textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Label Sertifikat</label>
                <input type="text" name="certificate_label" class="form-control" value="{{ old('certificate_label', $setting->certificate_label ?? 'Global Certificate') }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Nilai Sertifikat</label>
                <input type="text" name="certificate_value" class="form-control" value="{{ old('certificate_value', $setting->certificate_value ?? 'ISO 9001:2017') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Footer Text</label>
                <textarea name="footer_text" class="form-control" rows="3">{{ old('footer_text', $setting->footer_text) }}</textarea>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Facebook</label>
                <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $setting->facebook) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Instagram</label>
                <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $setting->instagram) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>LinkedIn</label>
                <input type="url" name="linkedin" class="form-control" value="{{ old('linkedin', $setting->linkedin) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>YouTube</label>
                <input type="url" name="youtube" class="form-control" value="{{ old('youtube', $setting->youtube) }}">
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Pengaturan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
