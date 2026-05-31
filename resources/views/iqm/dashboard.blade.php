@extends('layouts.iqm')

@section('title', 'Dashboard')

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Dashboard IQM</h3>
      <p class="text-secondary mb-0">{{ $user->company_name }} - {{ $user->pic_name }}</p>
    </div>
  </div>
  <div class="row g-3 mb-4">
    @foreach([
      ['Total Inquiry', $totalInquiry, 'fa-file-lines', 'primary'],
      ['Site Survey', $siteSurvey, 'fa-calendar-check', 'warning'],
      ['Quotation Aktif', $quotationActive, 'fa-file-signature', 'info'],
      ['Quotation Selesai', $quotationDone, 'fa-circle-check', 'success'],
    ] as [$label, $value, $icon, $color])
      <div class="col-6 col-lg-3"><div class="card iqm-card h-100"><div class="card-body p-3"><div class="d-flex justify-content-between align-items-center gap-3"><div><p class="text-secondary small mb-1">{{ $label }}</p><h4 class="fw-bold mb-0">{{ $value }}</h4></div><span class="text-{{ $color }}"><i class="fas {{ $icon }} fa-lg"></i></span></div></div></div></div>
    @endforeach
  </div>
  <div class="card iqm-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle iqm-table">
          <thead><tr><th>No Inquiry</th><th>Access</th><th>Tanggal</th><th>Subject</th><th>Survey Status</th><th>Quotation Number</th><th>Quotation Status</th><th class="text-end">Amount</th><th>Attachment</th><th></th></tr></thead>
          <tbody>
            @forelse($inquiries as $inquiry)
              <tr>
                <td class="fw-semibold">{{ $inquiry->inquiry_number }}</td>
                <td><span class="badge iqm-pill {{ $inquiry->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $inquiry->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span></td>
                <td>{{ optional($inquiry->inquiry_date)->format('d/m/Y') }}</td>
                <td>{{ Str::limit($inquiry->subject, 40) }}</td>
                <td><span class="badge iqm-pill {{ $inquiry->surveyStatusBadgeClass() }}">{{ $inquiry->surveyStatusLabel() }}</span></td>
                <td>{{ $inquiry->quotation_number ?: '-' }}</td>
                <td><span class="badge iqm-pill {{ $inquiry->quotationStatusBadgeClass() }}">{{ $inquiry->quotationStatusLabel() }}</span></td>
                <td class="text-end">{{ $inquiry->formattedAmount() }}</td>
                <td>{{ $inquiry->attachments_count ?? $inquiry->attachments->count() }} file</td>
                <td class="text-end"><a href="{{ route('iqm.inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary py-1 px-2">Detail</a></td>
              </tr>
            @empty
              <tr><td colspan="10" class="text-center text-secondary py-4">Belum ada inquiry yang dikaitkan dengan akun ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $inquiries->links() }}
    </div>
  </div>
</div>
@endsection
