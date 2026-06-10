@extends('layouts.iqm')

@section('title', 'Detail Invoice Report')

@section('content')
<div class="container iqm-container py-4">
  <div class="mb-4">
    <a href="{{ route('iqm.invoice-reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
  </div>

  <div class="card iqm-card">
    <div class="card-body p-4">
      <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
          <p class="text-secondary small mb-1">{{ $invoiceReport->invoice_no ?: 'Invoice Report' }}</p>
          <h3 class="fw-bold mb-0">{{ $invoiceReport->job_title }}</h3>
        </div>
        <div>
          <span class="badge iqm-pill {{ $invoiceReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $invoiceReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
        </div>
      </div>

      <div class="row g-3">
        @foreach([
          'Client' => $invoiceReport->client,
          'Invoice No' => $invoiceReport->invoice_no ?: '-',
          'PO / WO No' => $invoiceReport->po_wo_no ?: '-',
          'Invoice Date' => $invoiceReport->formattedDate(),
          'Quantity' => ($invoiceReport->quantity ? rtrim(rtrim(number_format((float) $invoiceReport->quantity, 2, ',', '.'), '0'), ',') : '-') . ' ' . ($invoiceReport->unit ?: ''),
          'Unit Price' => $invoiceReport->formattedMoney('unit_price'),
          'Total Amount' => $invoiceReport->formattedMoney('total_amount'),
        ] as $label => $value)
          <div class="col-md-4">
            <div class="border rounded p-3 h-100">
              <p class="text-secondary small mb-1">{{ $label }}</p>
              <p class="fw-semibold mb-0">{{ $value }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="mt-4">
    @include('iqm.partials.conversations', [
      'conversations' => $invoiceReport->portalConversations,
      'storeRoute' => route('iqm.invoice-reports.conversations.store', $invoiceReport),
    ])
  </div>
</div>
@endsection
