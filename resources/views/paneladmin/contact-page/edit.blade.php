@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Halaman Kontak</h6>
        <p class="text-sm mb-0">Kelola teks halaman kontak. Alamat, email, telepon, dan Google Maps tetap dikelola dari Pengaturan Website.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.contact-page.update') }}" class="js-confirm-submit">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Label Bagian</label>
                <input type="text" name="section_label" class="form-control @error('section_label') is-invalid @enderror" value="{{ old('section_label', $setting->section_label) }}">
                @error('section_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Judul Utama</label>
                <input type="text" name="heading" class="form-control @error('heading') is-invalid @enderror" value="{{ old('heading', $setting->heading) }}">
                @error('heading')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Judul Alamat</label>
                <input type="text" name="address_title" class="form-control @error('address_title') is-invalid @enderror" value="{{ old('address_title', $setting->address_title) }}">
                @error('address_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Judul Email</label>
                <input type="text" name="email_title" class="form-control @error('email_title') is-invalid @enderror" value="{{ old('email_title', $setting->email_title) }}">
                @error('email_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Judul Telepon</label>
                <input type="text" name="phone_title" class="form-control @error('phone_title') is-invalid @enderror" value="{{ old('phone_title', $setting->phone_title) }}">
                @error('phone_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Judul Formulir</label>
                <input type="text" name="form_heading" class="form-control @error('form_heading') is-invalid @enderror" value="{{ old('form_heading', $setting->form_heading) }}">
                @error('form_heading')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Teks Tombol Kirim</label>
                <input type="text" name="submit_button_text" class="form-control @error('submit_button_text') is-invalid @enderror" value="{{ old('submit_button_text', $setting->submit_button_text) }}">
                @error('submit_button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Pesan Sukses Form Kontak</label>
                <textarea name="success_message" class="form-control @error('success_message') is-invalid @enderror" rows="3">{{ old('success_message', $setting->success_message) }}</textarea>
                @error('success_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Halaman Kontak</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
