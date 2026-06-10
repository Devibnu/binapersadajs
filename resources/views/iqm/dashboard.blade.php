@extends('layouts.iqm')

@section('title', 'Dashboard')

@push('styles')
  <style>
    .iqm-project-report-list { grid-template-columns: 1fr; gap: 10px; }
    .iqm-project-report-step {
      display: flex;
      align-items: center;
      gap: 12px;
      text-align: left;
      border: 1px solid #eef2f7;
      border-radius: 8px;
      padding: .75rem;
      background: #fbfdff;
    }
    .iqm-project-report-step .iqm-project-step-label {
      margin-top: 0;
      white-space: normal;
      overflow-wrap: anywhere;
    }
  </style>
@endpush

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Portal Monitoring Proyek Client</h3>
      <p class="text-secondary mb-0">{{ $user->company_name }} - {{ $user->pic_name }}</p>
    </div>
  </div>

  <div class="card iqm-card mb-4">
    <div class="card-body p-4">
      <div class="iqm-card-header d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
          <p class="text-secondary small mb-1">Project Overview</p>
          <h4 class="fw-bold mb-0">{{ $activeProject['name'] ?? 'Belum ada project aktif' }}</h4>
        </div>
        @if($activeProject)
          <a href="{{ $activeProject['detail_url'] }}" class="btn btn-sm btn-outline-primary align-self-lg-center mb-0">
            <i class="fas fa-clipboard-list me-1"></i>Detail Project
          </a>
        @endif
      </div>

      @if($activeProject)
        @php($hasProjectRemark = filled($activeProject['remark']))
        <div class="row g-3 align-items-stretch">
          @if($hasProjectRemark)
            <div class="col-lg-6">
              <p class="text-secondary small mb-0">{{ \Illuminate\Support\Str::limit($activeProject['remark'], 180) }}</p>
            </div>
          @endif

          <div class="col-sm-6 {{ $hasProjectRemark ? 'col-lg-2' : 'col-lg-4' }}">
            <div class="iqm-overview-metric">
              <p class="text-secondary small mb-1">PIC Project</p>
              <p class="fw-semibold mb-0">{{ $activeProject['pic'] }}</p>
            </div>
          </div>
          <div class="col-sm-6 {{ $hasProjectRemark ? 'col-lg-2' : 'col-lg-4' }}">
            <div class="iqm-overview-metric">
              <p class="text-secondary small mb-1">Update Terakhir</p>
              <p class="fw-semibold mb-0">{{ $activeProject['last_update']?->format('d F Y') ?: '-' }}</p>
            </div>
          </div>
          <div class="col-sm-6 {{ $hasProjectRemark ? 'col-lg-2' : 'col-lg-4' }}">
            <div class="iqm-overview-metric">
              <p class="text-secondary small mb-1">Report Status</p>
              <p class="fw-semibold mb-0">{{ $activeProject['status'] }}</p>
            </div>
          </div>
        </div>
      @else
        <div class="text-center text-secondary py-4">Belum ada project report aktif yang dapat ditampilkan.</div>
      @endif
    </div>
  </div>

  <div class="row g-3 mb-4">
    @foreach([
      ['Total Inquiry', $totalInquiry, 'fa-file-lines', 'primary'],
      ['Total Project', $totalProject, 'fa-clipboard-list', 'info'],
      ['Quotation Aktif', $totalQuotationActive, 'fa-file-signature', 'warning'],
      ['Invoice Aktif', $totalInvoiceActive, 'fa-file-invoice-dollar', 'success'],
    ] as [$label, $value, $icon, $color])
      <div class="col-6 col-lg-3">
        <div class="card iqm-card h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center gap-3">
              <div>
                <p class="text-secondary small mb-1">{{ $label }}</p>
                <h4 class="fw-bold mb-0">{{ $value }}</h4>
              </div>
              <span class="iqm-summary-icon bg-gradient-{{ $color }}">
                <i class="fas {{ $icon }}"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card iqm-card mb-4">
    <div class="card-body p-3">
      <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
          <p class="text-secondary small mb-1">Pertanyaan Belum Dibaca</p>
          <h5 class="fw-bold mb-0">Pertanyaan Baru: {{ $conversationSummary['total'] }}</h5>
          <p class="text-secondary small mb-0">Pesan Terbaru: {{ $conversationSummary['latest_at'] ? \Illuminate\Support\Carbon::parse($conversationSummary['latest_at'])->format('d M Y H:i') : '-' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
          @foreach($conversationSummary['items'] as $item)
            <span class="badge iqm-pill bg-gradient-light">{{ $item['label'] }}: {{ $item['count'] }} pesan baru</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-7">
      <div class="card iqm-card h-100">
        <div class="card-body">
          <div class="iqm-card-header">
            <h5 class="fw-bold mb-1">Project Report Terbaru</h5>
            <p class="text-secondary small mb-0">Status terbaru berdasarkan data project report.</p>
          </div>

          <div class="iqm-project-timeline iqm-project-report-list">
            @forelse($projectTimeline as $project)
              <a href="{{ $project['detail_url'] }}" class="iqm-project-step iqm-project-report-step active text-decoration-none">
                <span class="iqm-project-step-marker">
                  <i class="fas fa-clipboard-list"></i>
                </span>
                <span class="iqm-project-step-label">
                  <span class="fw-semibold d-block">{{ $project['name'] }}</span>
                  <span class="text-secondary small">{{ $project['status'] }} - {{ $project['date']?->format('d F Y') ?: '-' }}</span>
                </span>
              </a>
            @empty
              <div class="text-center text-secondary py-4">Belum ada project report.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card iqm-card h-100">
        <div class="card-body">
          <div class="iqm-card-header">
            <h5 class="fw-bold mb-1">Recent Activity</h5>
            <p class="text-secondary small mb-0">Aktivitas terbaru dari inquiry, quotation, project, dan invoice.</p>
          </div>

          <div class="iqm-timeline">
            @forelse($latestActivities as $activity)
              <div class="iqm-timeline-item">
                <span class="iqm-timeline-icon {{ $activity['class'] }}">
                  <i class="fas {{ $activity['icon'] }}"></i>
                </span>
                <div class="d-flex flex-wrap justify-content-between gap-2">
                  <div class="small text-secondary">{{ $activity['date']?->format('d F Y') }}</div>
                  <span class="badge iqm-pill bg-gradient-light">{{ $activity['type'] }}</span>
                </div>
                <div class="fw-semibold">{{ $activity['title'] }}</div>
                <div class="text-secondary small">{{ $activity['description'] }}</div>
              </div>
            @empty
              <div class="text-center text-secondary py-4">Belum ada aktivitas terbaru.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card iqm-card h-100">
        <div class="card-body">
          <div class="iqm-card-header d-flex justify-content-between align-items-center gap-3">
            <div>
              <h5 class="fw-bold mb-1">Latest Invoice</h5>
              <p class="text-secondary small mb-0">Invoice terbaru client.</p>
            </div>
            <a href="{{ route('iqm.invoice-reports.index') }}" class="btn btn-sm btn-outline-primary mb-0">Lihat Semua</a>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle iqm-table mb-0">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th class="text-end">Nominal</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($latestInvoices as $invoice)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $invoice['invoice_no'] }}</div>
                      <div class="text-secondary small">{{ $invoice['date']?->format('d M Y') ?: '-' }}</div>
                    </td>
                    <td class="text-end">{{ $invoice['amount'] }}</td>
                    <td class="text-end">
                      <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ $invoice['detail_url'] }}" class="btn btn-sm btn-outline-primary py-1 px-2 mb-0">Detail</a>
                        <button type="button" class="btn btn-sm btn-primary py-1 px-2 mb-0 js-print-invoice" data-url="{{ $invoice['detail_url'] }}">
                          <i class="fas fa-download me-1"></i>PDF
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada invoice report.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card iqm-card h-100">
        <div class="card-body">
          <div class="iqm-card-header d-flex justify-content-between align-items-center gap-3">
            <div>
              <h5 class="fw-bold mb-1">Latest Inquiry</h5>
              <p class="text-secondary small mb-0">Inquiry dan quotation terakhir.</p>
            </div>
            <a href="{{ route('iqm.inquiries.index') }}" class="btn btn-sm btn-outline-primary mb-0">Lihat Semua</a>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle iqm-table mb-0">
              <thead>
                <tr>
                  <th>No Inquiry</th>
                  <th>Subject</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                @forelse($latestInquiries as $inquiry)
                  <tr>
                    <td class="fw-semibold">{{ $inquiry['inquiry_no'] }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($inquiry['subject'], 34) }}</td>
                    <td><span class="badge iqm-pill {{ $inquiry['status_class'] }}">{{ $inquiry['status'] }}</span></td>
                    <td>{{ $inquiry['date']?->format('d M Y') ?: '-' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-secondary py-4">Belum ada inquiry.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.js-print-invoice').forEach(function (button) {
    button.addEventListener('click', function () {
      var printWindow = window.open(button.dataset.url, '_blank');

      if (!printWindow) {
        return;
      }

      printWindow.addEventListener('load', function () {
        printWindow.print();
      });
    });
  });
</script>
@endpush
