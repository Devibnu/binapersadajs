@csrf

@php
  $selectedIqmUserIds = collect(old('iqm_user_ids', $projectReport?->iqmUsers?->pluck('id')->all() ?? []))
    ->map(fn ($id) => (int) $id)
    ->all();
@endphp

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Project No</label>
    <input type="text" name="project_no" class="form-control @error('project_no') is-invalid @enderror" value="{{ old('project_no', $projectReport->project_no) }}">
    @error('project_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-8 mb-3">
    <label class="form-label">Job Title *</label>
    <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $projectReport->job_title) }}" required>
    @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Quotation Price</label>
    <input type="text" name="quotation_price" class="form-control @error('quotation_price') is-invalid @enderror" value="{{ old('quotation_price', $projectReport->quotation_price ? (int) $projectReport->quotation_price : '') }}" inputmode="numeric">
    @error('quotation_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Contract Number</label>
    <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror" value="{{ old('contract_number', $projectReport->contract_number) }}">
    @error('contract_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Contract Price</label>
    <input type="text" name="contract_price" class="form-control @error('contract_price') is-invalid @enderror" value="{{ old('contract_price', $projectReport->contract_price ? (int) $projectReport->contract_price : '') }}" inputmode="numeric">
    @error('contract_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Invoice Amount</label>
    <input type="text" name="invoice_amount" class="form-control @error('invoice_amount') is-invalid @enderror" value="{{ old('invoice_amount', $projectReport->invoice_amount ? (int) $projectReport->invoice_amount : '') }}" inputmode="numeric">
    @error('invoice_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Corporation</label>
    <input type="text" name="corporation" class="form-control @error('corporation') is-invalid @enderror" value="{{ old('corporation', $projectReport->corporation) }}">
    @error('corporation')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Department</label>
    <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $projectReport->department) }}">
    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">User / PIC</label>
    <input type="text" name="user_pic" class="form-control @error('user_pic') is-invalid @enderror" value="{{ old('user_pic', $projectReport->user_pic) }}">
    @error('user_pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">E-WO Status</label>
    <input type="text" name="e_wo_status" class="form-control @error('e_wo_status') is-invalid @enderror" value="{{ old('e_wo_status', $projectReport->e_wo_status) }}">
    @error('e_wo_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Report Status</label>
    <input type="text" name="report_status" class="form-control @error('report_status') is-invalid @enderror" value="{{ old('report_status', $projectReport->report_status) }}">
    @error('report_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-12 mb-3">
    <label class="form-label">Remark</label>
    <textarea name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror">{{ old('remark', $projectReport->remark) }}</textarea>
    @error('remark')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-transparent border-0 pb-0"><h6 class="mb-0">Portal Access</h6></div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Visibility *</label>
        <select id="project-report-visibility" name="visibility" class="form-control @error('visibility') is-invalid @enderror" required>
          <option value="private" @selected(old('visibility', $projectReport->visibility ?? 'private') === 'private')>Private</option>
          <option value="public" @selected(old('visibility', $projectReport->visibility ?? 'private') === 'public')>Public</option>
        </select>
        @error('visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $projectReport->sort_order ?? 0) }}">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Status Aktif</label>
        <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
          <option value="1" @selected((string) old('is_active', $projectReport->is_active ? '1' : '0') === '1')>Aktif</option>
          <option value="0" @selected((string) old('is_active', $projectReport->is_active ? '1' : '0') === '0')>Tidak Aktif</option>
        </select>
        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div id="project-report-users-panel">
      <label class="form-label">Pilih IQM User</label>
      <div class="row g-2">
        @foreach($iqmUsers as $portalUser)
          <div class="col-md-6 col-xl-4">
            <label class="border rounded p-3 w-100 h-100 d-flex gap-2 align-items-start">
              <input type="checkbox" name="iqm_user_ids[]" value="{{ $portalUser->id }}" class="form-check-input mt-1 project-report-user-checkbox" @checked(in_array((int) $portalUser->id, $selectedIqmUserIds, true))>
              <span>
                <span class="d-block fw-semibold">{{ $portalUser->company_name }}</span>
                <span class="d-block text-muted small">{{ $portalUser->pic_name }} - {{ $portalUser->email ?: $portalUser->username }}</span>
              </span>
            </label>
          </div>
        @endforeach
      </div>
      <small class="text-muted">Private wajib memilih minimal satu user. Public dapat dilihat semua IQM user.</small>
      @error('iqm_user_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      @error('iqm_user_ids.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<div class="d-flex justify-content-end gap-2">
  <a href="{{ route('paneladmin.project-reports.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Project Report</button>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const visibility = document.getElementById('project-report-visibility');
    const panel = document.getElementById('project-report-users-panel');
    const checkboxes = document.querySelectorAll('.project-report-user-checkbox');

    function syncPortalAccess() {
      const isPublic = visibility && visibility.value === 'public';
      panel.style.display = isPublic ? 'none' : '';
      checkboxes.forEach((checkbox) => {
        if (isPublic) {
          checkbox.checked = false;
        }
      });
    }

    visibility?.addEventListener('change', syncPortalAccess);
    syncPortalAccess();
  });
</script>
@endpush
