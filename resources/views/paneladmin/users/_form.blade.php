@csrf

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
      <input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}>
      @error('password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Konfirmasi Password</label>
      <input type="password" name="password_confirmation" class="form-control" {{ $user->exists ? '' : 'required' }}>
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
