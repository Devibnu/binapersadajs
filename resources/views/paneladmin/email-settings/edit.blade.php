@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Pengaturan Email SMTP</h6>
        <p class="text-sm mb-0">Atur pengiriman email website tanpa mengubah file konfigurasi server secara manual.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.email-settings.update') }}" class="js-confirm-submit">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Mail Mailer</label>
                <select name="mailer" class="form-control @error('mailer') is-invalid @enderror" required>
                  <option value="smtp" {{ old('mailer', $setting->mailer ?: 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                </select>
                @error('mailer')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label>SMTP Host</label>
                <input type="text" name="host" value="{{ old('host', $setting->host) }}" class="form-control @error('host') is-invalid @enderror" placeholder="smtp.gmail.com" required>
                @error('host')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>SMTP Port</label>
                <input type="number" name="port" value="{{ old('port', $setting->port ?: 587) }}" class="form-control @error('port') is-invalid @enderror" min="1" max="65535" required>
                @error('port')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>SMTP Username</label>
                <input type="text" name="username" value="{{ old('username', $setting->username) }}" class="form-control @error('username') is-invalid @enderror" autocomplete="username" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>SMTP Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="{{ $setting->exists ? 'Tersimpan - kosongkan jika tidak diubah' : 'Masukkan password SMTP' }}">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="form-text text-muted">Password disimpan terenkripsi dan tidak pernah ditampilkan kembali.</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Encryption</label>
                <select name="encryption" class="form-control @error('encryption') is-invalid @enderror">
                  <option value="tls" {{ old('encryption', $setting->encryption ?: 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                  <option value="ssl" {{ old('encryption', $setting->encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                </select>
                @error('encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>From Email</label>
                <input type="email" name="from_address" value="{{ old('from_address', $setting->from_address) }}" class="form-control @error('from_address') is-invalid @enderror" required>
                @error('from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>From Name</label>
                <input type="text" name="from_name" value="{{ old('from_name', $setting->from_name ?: 'PT. Bina Persada Jaya Sejahtera') }}" class="form-control @error('from_name') is-invalid @enderror" required>
                @error('from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-check form-switch mb-4">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" {{ old('is_active', $setting->exists ? $setting->is_active : true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Aktifkan konfigurasi SMTP database untuk pengiriman email website</label>
              </div>
            </div>
          </div>

          <div class="alert alert-light text-dark text-sm border" role="alert">
            <strong>Contoh Gmail / Google Workspace:</strong> Host <code>smtp.gmail.com</code>, Port <code>587</code>, Encryption <code>TLS</code>. Gunakan App Password jika akun memakai verifikasi dua langkah.
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Pengaturan SMTP</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Test Koneksi Email</h6>
        <p class="text-sm mb-0">Simpan dan aktifkan pengaturan SMTP sebelum mengirim email test.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.email-settings.test') }}">
          @csrf
          <div class="form-group">
            <label>Email Tujuan Test</label>
            <input type="email" name="test_email" value="{{ old('test_email', $websiteSetting?->email ?? '') }}" class="form-control @error('test_email') is-invalid @enderror" placeholder="admin@perusahaan.com" required>
            @error('test_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn bg-gradient-success mb-0 w-100">Kirim Test Email</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">Provider yang Didukung</h6>
        <p class="text-sm mb-2"><strong>Gmail SMTP</strong><br>Gunakan email Gmail dan App Password.</p>
        <p class="text-sm mb-2"><strong>Google Workspace</strong><br>Gunakan akun email domain perusahaan.</p>
        <p class="text-sm mb-0"><strong>Email Hosting</strong><br>Masukkan host dan port sesuai provider hosting.</p>
      </div>
    </div>
  </div>
</div>
@endsection
