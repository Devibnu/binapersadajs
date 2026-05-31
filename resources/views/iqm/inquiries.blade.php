@extends('layouts.iqm')

@section('title', 'Inquiry')

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Inquiry</h3>
      <p class="text-secondary mb-0">Daftar inquiry yang dibagikan untuk akun {{ $user->company_name }}.</p>
    </div>
  </div>
  <div class="card iqm-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle iqm-table">
          <thead><tr><th>No Inquiry</th><th>Tanggal</th><th>Client</th><th>Subject</th><th>Survey</th><th>Attachment</th><th></th></tr></thead>
          <tbody>
            @forelse($inquiries as $inquiry)
              <tr>
                <td class="fw-semibold">{{ $inquiry->inquiry_number }}</td>
                <td>{{ optional($inquiry->inquiry_date)->format('d/m/Y') }}</td>
                <td>{{ $inquiry->client_name }}</td>
                <td>{{ Str::limit($inquiry->subject, 48) }}</td>
                <td><span class="badge iqm-pill {{ $inquiry->surveyStatusBadgeClass() }}">{{ $inquiry->surveyStatusLabel() }}</span></td>
                <td>{{ $inquiry->attachments_count ?? $inquiry->attachments->count() }} file</td>
                <td class="text-end"><a href="{{ route('iqm.inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary py-1 px-2">Detail</a></td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada inquiry yang dikaitkan dengan akun ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $inquiries->links() }}
    </div>
  </div>
</div>
@endsection
