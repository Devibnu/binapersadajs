@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card shadow-sm border-0">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6>Detail Invoice Report</h6>
        <p class="text-sm text-secondary mb-0">{{ $invoiceReport->invoice_no ?: '-' }}</p>
      </div>
      @if(auth()->user()?->canAccess('invoice-reports.update'))
        <a href="{{ route('paneladmin.invoice-reports.edit', $invoiceReport) }}" class="btn bg-gradient-primary mb-0">Edit Invoice</a>
      @endif
    </div>
    <div class="card-body">
      <h4>{{ $invoiceReport->job_title }}</h4>
      <div class="row mt-4">
        @foreach([
          'Client' => $invoiceReport->client,
          'Invoice No' => $invoiceReport->invoice_no ?: '-',
          'PO / WO No' => $invoiceReport->po_wo_no ?: '-',
          'Invoice Date' => $invoiceReport->formattedDate(),
          'Quantity' => $invoiceReport->quantity ? rtrim(rtrim(number_format((float) $invoiceReport->quantity, 2, ',', '.'), '0'), ',') : '-',
          'Unit' => $invoiceReport->unit ?: '-',
          'Unit Price' => $invoiceReport->formattedMoney('unit_price'),
          'Total Amount' => $invoiceReport->formattedMoney('total_amount'),
        ] as $label => $value)
          <div class="col-md-4 mb-3">
            <p class="text-xs text-uppercase text-secondary mb-1">{{ $label }}</p>
            <p class="text-sm font-weight-bold mb-0">{{ $value }}</p>
          </div>
        @endforeach
      </div>
      <div class="mb-2">
        <span class="badge badge-sm {{ $invoiceReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $invoiceReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
        <span class="badge badge-sm {{ $invoiceReport->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $invoiceReport->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
      </div>
      @if(! $invoiceReport->isPublic())
        <p class="text-xs text-uppercase text-secondary mb-2">IQM User Access</p>
        @forelse($invoiceReport->iqmUsers as $portalUser)
          <span class="badge bg-gradient-light text-dark me-1 mb-1">{{ $portalUser->company_name }} - {{ $portalUser->pic_name }}</span>
        @empty
          <p class="text-sm text-secondary">Belum ada IQM user dipilih.</p>
        @endforelse
      @endif
    </div>
  </div>
</div>
@endsection
