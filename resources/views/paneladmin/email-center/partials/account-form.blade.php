<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Akun</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $account->email) }}" required>
    </div>
  </div>

  <div class="col-12"><h6 class="text-xs text-uppercase text-secondary font-weight-bolder mt-2">SMTP</h6></div>
  <div class="col-md-6"><div class="form-group"><label>SMTP Host</label><input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $account->smtp_host) }}" required></div></div>
  <div class="col-md-3"><div class="form-group"><label>SMTP Port</label><input type="number" name="smtp_port" class="form-control" value="{{ old('smtp_port', $account->smtp_port ?: 587) }}" required></div></div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Encryption</label>
      <select name="smtp_encryption" class="form-control">
        <option value="">None</option>
        <option value="tls" @selected(old('smtp_encryption', $account->smtp_encryption) === 'tls')>TLS</option>
        <option value="ssl" @selected(old('smtp_encryption', $account->smtp_encryption) === 'ssl')>SSL</option>
      </select>
    </div>
  </div>
  <div class="col-md-6"><div class="form-group"><label>SMTP Username</label><input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username', $account->smtp_username) }}" required></div></div>
  <div class="col-md-6"><div class="form-group"><label>SMTP Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label><input type="password" name="smtp_password" class="form-control" {{ $isEdit ? '' : 'required' }}></div></div>

  <div class="col-12"><h6 class="text-xs text-uppercase text-secondary font-weight-bolder mt-2">IMAP</h6></div>
  <div class="col-md-6"><div class="form-group"><label>IMAP Host</label><input type="text" name="imap_host" class="form-control" value="{{ old('imap_host', $account->imap_host) }}"></div></div>
  <div class="col-md-3"><div class="form-group"><label>IMAP Port</label><input type="number" name="imap_port" class="form-control" value="{{ old('imap_port', $account->imap_port ?: 993) }}"></div></div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Encryption</label>
      <select name="imap_encryption" class="form-control">
        <option value="">None</option>
        <option value="tls" @selected(old('imap_encryption', $account->imap_encryption) === 'tls')>TLS</option>
        <option value="ssl" @selected(old('imap_encryption', $account->imap_encryption ?: 'ssl') === 'ssl')>SSL</option>
      </select>
    </div>
  </div>
  <div class="col-md-6"><div class="form-group"><label>IMAP Username</label><input type="text" name="imap_username" class="form-control" value="{{ old('imap_username', $account->imap_username) }}"></div></div>
  <div class="col-md-6"><div class="form-group"><label>IMAP Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label><input type="password" name="imap_password" class="form-control"></div></div>
  <div class="col-12">
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active-{{ $account->id ?: 'new' }}" @checked(old('is_active', $account->is_active ?? true))>
      <label class="form-check-label" for="active-{{ $account->id ?: 'new' }}">Aktif</label>
    </div>
  </div>
</div>
