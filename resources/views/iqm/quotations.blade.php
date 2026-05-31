@extends('layouts.iqm')

@section('title', 'Quotation')

@section('content')
<div class="container iqm-container py-4">
  <div class="mb-4">
    <h3 class="fw-bold mb-1">Quotation</h3>
    <p class="text-secondary mb-0">Ringkasan quotation dari inquiry yang dibagikan ke akun Anda.</p>
  </div>
  <div class="card iqm-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle iqm-table">
          <thead><tr><th>No Quotation</th><th>Tanggal</th><th>Subject</th><th>Status</th><th class="text-end">Amount</th><th>Deadline</th><th></th></tr></thead>
          <tbody>
            @forelse($inquiries as $inquiry)
              <tr>
                <td class="fw-semibold">{{ $inquiry->quotation_number }}</td>
                <td>{{ optional($inquiry->quotation_date)->format('d/m/Y') ?: '-' }}</td>
                <td>{{ Str::limit($inquiry->subject, 50) }}</td>
                <td><span class="badge iqm-pill {{ $inquiry->quotationStatusBadgeClass() }}">{{ $inquiry->quotationStatusLabel() }}</span></td>
                <td class="text-end">{{ $inquiry->formattedAmount() }}</td>
                <td>{{ optional($inquiry->deadline)->format('d/m/Y') ?: '-' }}</td>
                <td class="text-end"><a href="{{ route('iqm.inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary py-1 px-2">Detail</a></td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada quotation yang tersedia.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $inquiries->links() }}
    </div>
  </div>
</div>
@endsection
