@csrf

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Role</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
      @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $role->slug) }}" placeholder="otomatis-dari-nama-role">
    </div>
  </div>
  <div class="col-md-8">
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Status</label>
      <select name="is_active" class="form-control" required {{ $role->is_super_admin ? 'disabled' : '' }}>
        <option value="1" {{ old('is_active', $role->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ ! old('is_active', $role->is_active ?? true) ? 'selected' : '' }}>Nonaktif</option>
      </select>
      @if($role->is_super_admin)<input type="hidden" name="is_active" value="1">@endif
    </div>
  </div>
</div>

<h6 class="text-uppercase text-xs font-weight-bolder text-secondary mt-4">Checklist Permission</h6>
<div class="row">
  @foreach($permissionsByGroup as $group => $permissions)
    @php
      $groupKey = 'permission-group-' . $loop->index;
      $selectedIds = old('permissions', $selectedPermissions->all());
    @endphp
    <div class="col-md-6 col-xl-4">
      <div class="border border-radius-lg p-3 mb-3 h-100">
        <div class="form-check mb-2">
          <input type="checkbox" class="form-check-input js-group-toggle" id="{{ $groupKey }}" data-group="{{ $groupKey }}">
          <label class="form-check-label font-weight-bold text-sm" for="{{ $groupKey }}">{{ $group }} - Pilih Semua</label>
        </div>
        @foreach($permissions as $permission)
          <div class="form-check ps-4">
            <input type="checkbox" class="form-check-input js-permission-item" data-group="{{ $groupKey }}" name="permissions[]" id="permission-{{ $permission->id }}" value="{{ $permission->id }}" {{ in_array($permission->id, $selectedIds) ? 'checked' : '' }}>
            <label class="form-check-label text-sm" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
          </div>
        @endforeach
      </div>
    </div>
  @endforeach
</div>

<div class="d-flex justify-content-between mt-4">
  <a href="{{ route('paneladmin.roles.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-group-toggle').forEach(function (toggle) {
      var items = document.querySelectorAll('.js-permission-item[data-group="' + toggle.dataset.group + '"]');
      toggle.checked = Array.from(items).length > 0 && Array.from(items).every(function (item) { return item.checked; });
      toggle.addEventListener('change', function () {
        items.forEach(function (item) { item.checked = toggle.checked; });
      });
      items.forEach(function (item) {
        item.addEventListener('change', function () {
          toggle.checked = Array.from(items).every(function (permission) { return permission.checked; });
        });
      });
    });
  });
</script>
@endpush
