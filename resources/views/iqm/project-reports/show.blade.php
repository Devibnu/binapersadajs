@extends('layouts.iqm')

@section('title', 'Detail Project Report')

@section('content')
<div class="container iqm-container py-4">
  <div class="mb-4">
    <a href="{{ route('iqm.project-reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
  </div>

  <div class="card iqm-card">
    <div class="card-body p-4">
      <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
          <p class="text-secondary small mb-1">{{ $projectReport->project_no ?: 'Project Report' }}</p>
          <h3 class="fw-bold mb-0">{{ $projectReport->job_title }}</h3>
        </div>
        <div>
          <span class="badge iqm-pill {{ $projectReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $projectReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
        </div>
      </div>

      <div class="row g-3">
        @foreach([
          'Quotation Price' => $projectReport->formattedMoney('quotation_price'),
          'Contract Number' => $projectReport->contract_number ?: '-',
          'Contract Price' => $projectReport->formattedMoney('contract_price'),
          'Invoice Amount' => $projectReport->formattedMoney('invoice_amount'),
          'Corporation' => $projectReport->corporation ?: '-',
          'Department' => $projectReport->department ?: '-',
          'User / PIC' => $projectReport->user_pic ?: '-',
          'E-WO Status' => $projectReport->e_wo_status ?: '-',
          'Report Status' => $projectReport->report_status ?: '-',
        ] as $label => $value)
          <div class="col-md-4">
            <div class="border rounded p-3 h-100">
              <p class="text-secondary small mb-1">{{ $label }}</p>
              <p class="fw-semibold mb-0">{{ $value }}</p>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        <p class="text-secondary small mb-1">Remark</p>
        <div class="border rounded p-3 bg-light">{!! nl2br(e($projectReport->remark ?: '-')) !!}</div>
      </div>
    </div>
  </div>

  <div class="mt-4">
    @include('iqm.partials.conversations', [
      'conversations' => $projectReport->portalConversations,
      'storeRoute' => route('iqm.project-reports.conversations.store', $projectReport),
    ])
  </div>
</div>
@endsection
