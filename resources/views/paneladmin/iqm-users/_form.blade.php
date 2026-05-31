@csrf

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
</div>

<div class="d-flex justify-content-between mt-3">
  <a href="{{ route('paneladmin.iqm-users.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
