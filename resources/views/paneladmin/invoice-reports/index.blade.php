@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
      <h4 class="font-weight-bold mb-1">Invoice Reports</h4>
      <p class="text-sm text-secondary mb-0">Kelola Invoice Report / Invoice List untuk IQM Portal.</p>
    </div>
    @if(auth()->user()?->canAccess('invoice-reports.create'))
      <a href="{{ route('paneladmin.invoice-reports.create') }}" class="btn bg-gradient-primary mb-0 align-self-lg-center"><i class="fas fa-plus me-1"></i> Tambah Invoice</a>
    @endif
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('paneladmin.invoice-reports.index') }}" class="row g-3 align-items-end">
        <div class="col-lg-7">
          <label class="form-label text-xs font-weight-bold">Search</label>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari client, invoice no, PO/WO, job title">
        </div>
        <div class="col-lg-3">
          <label class="form-label text-xs font-weight-bold">Visibility</label>
          <select name="visibility" class="form-control">
            <option value="">Semua</option>
            <option value="public" @selected(request('visibility') === 'public')>Public</option>
            <option value="private" @selected(request('visibility') === 'private')>Private</option>
          </select>
        </div>
        <div class="col-lg-2">
          <button type="submit" class="btn btn-info mb-0 w-100">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Invoice</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Job</th>
            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Access</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
            <th class="text-secondary opacity-7"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoiceReports as $invoiceReport)
            <tr>
              <td>
                <div class="px-2 py-1">
                  <h6 class="mb-0 text-sm">{{ $invoiceReport->invoice_no ?: '-' }}</h6>
                  <p class="text-xs text-secondary mb-0">{{ $invoiceReport->formattedDate() }}</p>
                </div>
              </td>
              <td><span class="text-xs font-weight-bold">{{ $invoiceReport->client }}</span></td>
              <td>
                <span class="text-xs font-weight-bold">{{ $invoiceReport->job_title }}</span>
                <p class="text-xs text-secondary mb-0">PO/WO: {{ $invoiceReport->po_wo_no ?: '-' }}</p>
              </td>
              <td class="text-end"><span class="text-xs font-weight-bold">{{ $invoiceReport->formattedMoney('total_amount') }}</span></td>
              <td class="text-center">
                <span class="badge badge-sm {{ $invoiceReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $invoiceReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
                @if(! $invoiceReport->isPublic())
                  <div class="text-xs text-secondary mt-1">{{ $invoiceReport->iqmUsers->count() }} users</div>
                @endif
              </td>
              <td class="text-center"><span class="badge badge-sm {{ $invoiceReport->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $invoiceReport->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
              <td class="align-middle">
                @if(auth()->user()?->canAccess('invoice-reports.view'))
                  <a href="{{ route('paneladmin.invoice-reports.show', $invoiceReport) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                @endif
                @if(auth()->user()?->canAccess('invoice-reports.update'))
                  <a href="{{ route('paneladmin.invoice-reports.edit', $invoiceReport) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                @endif
                @if(auth()->user()?->canAccess('invoice-reports.delete'))
                  <form method="POST" action="{{ route('paneladmin.invoice-reports.destroy', $invoiceReport) }}" class="d-inline js-confirm-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada invoice report.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $invoiceReports->links() }}</div>
  </div>
</div>
@endsection
