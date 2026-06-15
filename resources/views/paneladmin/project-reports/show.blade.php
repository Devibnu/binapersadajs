@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card shadow-sm border-0">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6>Detail Project Report</h6>
        <p class="text-sm text-secondary mb-0">{{ $projectReport->project_no ?: '-' }}</p>
      </div>
      @if(auth()->user()?->canAccess('project-reports.update'))
        <a href="{{ route('paneladmin.project-reports.edit', $projectReport) }}" class="btn bg-gradient-primary mb-0">Edit Report</a>
      @endif
    </div>
    <div class="card-body">
      <h4>{{ $projectReport->job_title }}</h4>
      <div class="row mt-4">
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
          <div class="col-md-4 mb-3">
            <p class="text-xs text-uppercase text-secondary mb-1">{{ $label }}</p>
            <p class="text-sm font-weight-bold mb-0">{{ $value }}</p>
          </div>
        @endforeach
      </div>
      <div class="mb-4">
        <p class="text-xs text-uppercase text-secondary mb-1">Remark</p>
        <p class="text-sm mb-0">{!! nl2br(e($projectReport->remark ?: '-')) !!}</p>
      </div>
      <div class="mb-2">
        <span class="badge badge-sm {{ $projectReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $projectReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
        <span class="badge badge-sm {{ $projectReport->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $projectReport->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
      </div>
      @if(! $projectReport->isPublic())
        <p class="text-xs text-uppercase text-secondary mb-2">IQM User Access</p>
        @forelse($projectReport->iqmUsers as $portalUser)
          <span class="badge bg-gradient-light text-dark me-1 mb-1">{{ $portalUser->company_name }} - {{ $portalUser->pic_name }}</span>
        @empty
          <p class="text-sm text-secondary">Belum ada IQM user dipilih.</p>
        @endforelse
      @endif

      <div class="mt-4">
        <p class="text-xs text-uppercase text-secondary mb-2">Project Attachments</p>
        @if($projectReport->attachments->isEmpty())
          <p class="text-sm text-secondary mb-0">Belum ada attachment.</p>
        @else
          <div class="row g-3">
            @foreach($projectReport->attachments as $attachment)
              <div class="col-sm-6 col-lg-4">
                <div class="border rounded p-3 h-100">
                  <div class="bg-light rounded d-flex align-items-center justify-content-center overflow-hidden mb-3" style="height: 150px;">
                    @if($attachment->isImage())
                      <img src="{{ route('paneladmin.project-report-attachments.preview', $attachment) }}" alt="{{ $attachment->original_name }}" class="w-100 h-100" style="object-fit: cover;">
                    @elseif($attachment->isPdf())
                      <i class="fas fa-file-pdf fa-3x text-danger"></i>
                    @else
                      <i class="fas fa-file fa-3x text-secondary"></i>
                    @endif
                  </div>
                  <h6 class="text-sm font-weight-bold mb-1">{{ $attachment->original_name }}</h6>
                  <p class="text-xs text-muted mb-3">{{ $attachment->formattedSize() }} - {{ strtoupper($attachment->file_type) }}</p>
                  <a href="{{ route('paneladmin.project-report-attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary w-100 mb-0">
                    <i class="fas fa-download me-1"></i> Download
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  @include('paneladmin.partials.portal-conversations', [
    'conversations' => $projectReport->portalConversations,
    'storeRoute' => route('paneladmin.project-reports.conversations.store', $projectReport),
  ])
</div>
@endsection
