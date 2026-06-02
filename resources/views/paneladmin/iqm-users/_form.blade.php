@csrf

@once
  <style>
    .iqm-admin-password-field {
      position: relative;
    }

    .iqm-admin-password-field .form-control {
      padding-right: 44px;
    }

    .iqm-admin-password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: #6c757d;
      cursor: pointer;
      padding: 4px;
      line-height: 1;
    }

    .iqm-admin-password-toggle:hover {
      color: #1f8f5f;
    }
  </style>
@endonce

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Perusahaan</label>
      <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $user->company_name) }}" required>
      @error('company_name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>PIC</label>
      <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name', $user->pic_name) }}" required>
      @error('pic_name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
      @error('username') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
      @error('email') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>No Telepon</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
      @error('phone') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control" required>
        <option value="active" @selected(old('status', $user->status) === 'active')>Aktif</option>
        <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Nonaktif</option>
      </select>
      @error('status') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
      <div class="iqm-admin-password-field">
        <input type="password" name="password" id="iqm-user-password" class="form-control">
        <button type="button" class="iqm-admin-password-toggle js-iqm-password-toggle" data-target="iqm-user-password" aria-label="Tampilkan password">
          <i class="fas fa-eye"></i>
        </button>
      </div>
      @unless($user->exists)
        <small class="form-text text-muted">
          Jika password dikosongkan, sistem akan menggunakan default password: BinaPersada@2026. User dapat mengubah password setelah login.
        </small>
      @endunless
      @error('password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Konfirmasi Password</label>
      <div class="iqm-admin-password-field">
        <input type="password" name="password_confirmation" id="iqm-user-password-confirmation" class="form-control">
        <button type="button" class="iqm-admin-password-toggle js-iqm-password-toggle" data-target="iqm-user-password-confirmation" aria-label="Tampilkan konfirmasi password">
          <i class="fas fa-eye"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <a href="{{ route('paneladmin.iqm-users.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>

@push('dashboard')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.js-iqm-password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
          const input = document.getElementById(button.dataset.target);
          const icon = button.querySelector('i');

          if (!input || !icon) return;

          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
          }
        });
      });
    });
  </script>
@endpush
