@csrf

@php
  $selectedIqmUserIds = collect(old('iqm_user_ids', $invoiceReport?->iqmUsers?->pluck('id')->all() ?? []))
    ->map(fn ($id) => (int) $id)
    ->all();
@endphp

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Client *</label>
    <input type="text" name="client" class="form-control @error('client') is-invalid @enderror" value="{{ old('client', $invoiceReport->client) }}" required>
    @error('client')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Invoice No</label>
    <input type="text" name="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror" value="{{ old('invoice_no', $invoiceReport->invoice_no) }}">
    @error('invoice_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">PO / WO No</label>
    <input type="text" name="po_wo_no" class="form-control @error('po_wo_no') is-invalid @enderror" value="{{ old('po_wo_no', $invoiceReport->po_wo_no) }}">
    @error('po_wo_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-8 mb-3">
    <label class="form-label">Job Title *</label>
    <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $invoiceReport->job_title) }}" required>
    @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Invoice Date</label>
    <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', optional($invoiceReport->invoice_date)->format('Y-m-d')) }}">
    @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Quantity</label>
    <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $invoiceReport->quantity ? (float) $invoiceReport->quantity : '') }}" inputmode="decimal">
    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Unit</label>
    <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $invoiceReport->unit) }}">
    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Unit Price</label>
    <input type="text" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $invoiceReport->unit_price ? (int) $invoiceReport->unit_price : '') }}" inputmode="numeric">
    @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label">Total Amount</label>
    <input type="text" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $invoiceReport->total_amount ? (int) $invoiceReport->total_amount : '') }}" inputmode="numeric">
    @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-transparent border-0 pb-0"><h6 class="mb-0">Portal Access</h6></div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Visibility *</label>
        <select id="invoice-report-visibility" name="visibility" class="form-control @error('visibility') is-invalid @enderror" required>
          <option value="private" @selected(old('visibility', $invoiceReport->visibility ?? 'private') === 'private')>Private</option>
          <option value="public" @selected(old('visibility', $invoiceReport->visibility ?? 'private') === 'public')>Public</option>
        </select>
        @error('visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $invoiceReport->sort_order ?? 0) }}">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Status Aktif</label>
        <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
          <option value="1" @selected((string) old('is_active', $invoiceReport->is_active ? '1' : '0') === '1')>Aktif</option>
          <option value="0" @selected((string) old('is_active', $invoiceReport->is_active ? '1' : '0') === '0')>Tidak Aktif</option>
        </select>
        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div id="invoice-report-users-panel">
      <label class="form-label">Pilih IQM User</label>
      <div class="row g-2">
        @foreach($iqmUsers as $portalUser)
          <div class="col-md-6 col-xl-4">
            <label class="border rounded p-3 w-100 h-100 d-flex gap-2 align-items-start">
              <input type="checkbox" name="iqm_user_ids[]" value="{{ $portalUser->id }}" class="form-check-input mt-1 invoice-report-user-checkbox" @checked(in_array((int) $portalUser->id, $selectedIqmUserIds, true))>
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
  <a href="{{ route('paneladmin.invoice-reports.index') }}" class="btn btn-light mb-0">Batal</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Invoice Report</button>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const visibility = document.getElementById('invoice-report-visibility');
    const panel = document.getElementById('invoice-report-users-panel');
    const checkboxes = document.querySelectorAll('.invoice-report-user-checkbox');

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
