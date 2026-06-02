@extends('layouts.iqm')

@section('title', 'Invoice Reports')

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Invoice Reports</h3>
      <p class="text-secondary mb-0">{{ $user->company_name }} - {{ $user->pic_name }}</p>
    </div>
  </div>

  <div class="card iqm-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle iqm-table">
          <thead>
            <tr>
              <th>Invoice No</th>
              <th>Access</th>
              <th>Tanggal</th>
              <th>Client</th>
              <th>PO / WO</th>
              <th>Job Title</th>
              <th>Qty</th>
              <th class="text-end">Unit Price</th>
              <th class="text-end">Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoiceReports as $invoiceReport)
              <tr>
                <td class="fw-semibold">{{ $invoiceReport->invoice_no ?: '-' }}</td>
                <td><span class="badge iqm-pill {{ $invoiceReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $invoiceReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span></td>
                <td>{{ $invoiceReport->formattedDate() }}</td>
                <td>{{ $invoiceReport->client }}</td>
                <td>{{ $invoiceReport->po_wo_no ?: '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($invoiceReport->job_title, 42) }}</td>
                <td>{{ $invoiceReport->quantity ? rtrim(rtrim(number_format((float) $invoiceReport->quantity, 2, ',', '.'), '0'), ',') : '-' }} {{ $invoiceReport->unit }}</td>
                <td class="text-end">{{ $invoiceReport->formattedMoney('unit_price') }}</td>
                <td class="text-end">{{ $invoiceReport->formattedMoney('total_amount') }}</td>
                <td class="text-end"><a href="{{ route('iqm.invoice-reports.show', $invoiceReport) }}" class="btn btn-sm btn-outline-primary py-1 px-2">Detail</a></td>
              </tr>
            @empty
              <tr><td colspan="10" class="text-center text-secondary py-4">Belum ada invoice report yang dapat dilihat akun ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $invoiceReports->links() }}
    </div>
  </div>
</div>
@endsection
