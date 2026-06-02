@extends('layouts.iqm')

@section('title', 'Project Reports')

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Project Reports</h3>
      <p class="text-secondary mb-0">{{ $user->company_name }} - {{ $user->pic_name }}</p>
    </div>
  </div>

  <div class="card iqm-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle iqm-table">
          <thead>
            <tr>
              <th>Project No</th>
              <th>Access</th>
              <th>Job Title</th>
              <th>Corporation</th>
              <th>Department</th>
              <th>PIC</th>
              <th>Contract No</th>
              <th class="text-end">Contract Price</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($projectReports as $projectReport)
              <tr>
                <td class="fw-semibold">{{ $projectReport->project_no ?: '-' }}</td>
                <td><span class="badge iqm-pill {{ $projectReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $projectReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span></td>
                <td>{{ \Illuminate\Support\Str::limit($projectReport->job_title, 42) }}</td>
                <td>{{ $projectReport->corporation ?: '-' }}</td>
                <td>{{ $projectReport->department ?: '-' }}</td>
                <td>{{ $projectReport->user_pic ?: '-' }}</td>
                <td>{{ $projectReport->contract_number ?: '-' }}</td>
                <td class="text-end">{{ $projectReport->formattedMoney('contract_price') }}</td>
                <td>{{ $projectReport->report_status ?: '-' }}</td>
                <td class="text-end"><a href="{{ route('iqm.project-reports.show', $projectReport) }}" class="btn btn-sm btn-outline-primary py-1 px-2">Detail</a></td>
              </tr>
            @empty
              <tr><td colspan="10" class="text-center text-secondary py-4">Belum ada project report yang dapat dilihat akun ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $projectReports->links() }}
    </div>
  </div>
</div>
@endsection
