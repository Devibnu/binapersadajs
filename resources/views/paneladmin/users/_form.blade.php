@csrf

<style>
  .admin-password-field {
    position: relative;
  }

  .admin-password-field .form-control {
    padding-right: 46px;
  }

  .admin-password-toggle {
    align-items: center;
    background: transparent;
    border: 0;
    color: #8392ab;
    cursor: pointer;
    display: inline-flex;
    height: 38px;
    justify-content: center;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    z-index: 2;
  }

  .admin-password-toggle:hover,
  .admin-password-toggle:focus {
    color: #2152ff;
    outline: 0;
  }
</style>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
      @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
      @error('email') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
      <div class="admin-password-field">
        <input type="password" name="password" id="admin-user-password" class="form-control" autocomplete="new-password">
        <button type="button" class="admin-password-toggle js-password-toggle" data-target="admin-user-password" aria-label="Lihat password" aria-pressed="false">
          <i class="fas fa-eye" aria-hidden="true"></i>
        </button>
      </div>
      @unless($user->exists)
        <p class="text-xs text-secondary mb-0 mt-1">Jika password dikosongkan, sistem akan menggunakan default password: BinaPersada@2026. User dapat mengubah password setelah login.</p>
      @endunless
      @error('password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Konfirmasi Password</label>
      <div class="admin-password-field">
        <input type="password" name="password_confirmation" id="admin-user-password-confirmation" class="form-control" autocomplete="new-password">
        <button type="button" class="admin-password-toggle js-password-toggle" data-target="admin-user-password-confirmation" aria-label="Lihat password" aria-pressed="false">
          <i class="fas fa-eye" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Role</label>
      <select name="role_id" class="form-control" required>
        <option value="">Pilih Role</option>
        @foreach($roles as $role)
          <option value="{{ $role->id }}" {{ (int) old('role_id', $user->role_id) === $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
      </select>
      @error('role_id') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Status Akun</label>
      <select name="is_active" class="form-control" required>
        <option value="1" {{ old('is_active', $user->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $user->is_active ?? true) ? 'selected' : '' }}>Nonaktif</option>
      </select>
      <p class="text-xs text-secondary mb-0 mt-1">Akun nonaktif tidak dapat mengakses panel admin.</p>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <a href="{{ route('paneladmin.users.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>

@push('dashboard')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-password-toggle').forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        const input = document.getElementById(toggle.dataset.target);
        const icon = toggle.querySelector('i');

        if (!input || !icon) {
          return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Lihat password');
        toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        icon.classList.toggle('fa-eye', !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);
      });
    });
  });
</script>
@endpush
