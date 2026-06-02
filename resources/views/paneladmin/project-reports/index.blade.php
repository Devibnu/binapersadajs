@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div>
      <h4 class="font-weight-bold mb-1">Project Reports</h4>
      <p class="text-sm text-secondary mb-0">Kelola Project Report / Job List untuk IQM Portal.</p>
    </div>
    @if(auth()->user()?->canAccess('project-reports.create'))
      <a href="{{ route('paneladmin.project-reports.create') }}" class="btn bg-gradient-primary mb-0 align-self-lg-center"><i class="fas fa-plus me-1"></i> Tambah Report</a>
    @endif
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('paneladmin.project-reports.index') }}" class="row g-3 align-items-end">
        <div class="col-lg-5">
          <label class="form-label text-xs font-weight-bold">Search</label>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari project no, job title, corporation, department, PIC">
        </div>
        <div class="col-lg-3">
          <label class="form-label text-xs font-weight-bold">Visibility</label>
          <select name="visibility" class="form-control">
            <option value="">Semua</option>
            <option value="public" @selected(request('visibility') === 'public')>Public</option>
            <option value="private" @selected(request('visibility') === 'private')>Private</option>
          </select>
        </div>
        <div class="col-lg-3">
          <label class="form-label text-xs font-weight-bold">Report Status</label>
          <input type="text" name="report_status" class="form-control" value="{{ request('report_status') }}" placeholder="Status">
        </div>
        <div class="col-lg-1 d-flex gap-2">
          <button type="submit" class="btn btn-info mb-0">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Corporation</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contract</th>
            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Invoice</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Access</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
            <th class="text-secondary opacity-7"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($projectReports as $projectReport)
            <tr>
              <td>
                <div class="px-2 py-1">
                  <h6 class="mb-0 text-sm">{{ $projectReport->job_title }}</h6>
                  <p class="text-xs text-secondary mb-0">{{ $projectReport->project_no ?: '-' }}</p>
                </div>
              </td>
              <td>
                <span class="text-xs font-weight-bold">{{ $projectReport->corporation ?: '-' }}</span>
                <p class="text-xs text-secondary mb-0">{{ $projectReport->department ?: '-' }}</p>
              </td>
              <td>
                <span class="text-xs font-weight-bold">{{ $projectReport->contract_number ?: '-' }}</span>
                <p class="text-xs text-secondary mb-0">{{ $projectReport->formattedMoney('contract_price') }}</p>
              </td>
              <td class="text-end"><span class="text-xs font-weight-bold">{{ $projectReport->formattedMoney('invoice_amount') }}</span></td>
              <td class="text-center">
                <span class="badge badge-sm {{ $projectReport->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">{{ $projectReport->isPublic() ? 'PUBLIC' : 'PRIVATE' }}</span>
                @if(! $projectReport->isPublic())
                  <div class="text-xs text-secondary mt-1">{{ $projectReport->iqmUsers->count() }} users</div>
                @endif
              </td>
              <td class="text-center">
                <span class="badge badge-sm {{ $projectReport->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $projectReport->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                @if($projectReport->report_status)
                  <div class="text-xs text-secondary mt-1">{{ $projectReport->report_status }}</div>
                @endif
              </td>
              <td class="align-middle">
                @if(auth()->user()?->canAccess('project-reports.view'))
                  <a href="{{ route('paneladmin.project-reports.show', $projectReport) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                @endif
                @if(auth()->user()?->canAccess('project-reports.update'))
                  <a href="{{ route('paneladmin.project-reports.edit', $projectReport) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                @endif
                @if(auth()->user()?->canAccess('project-reports.delete'))
                  <form method="POST" action="{{ route('paneladmin.project-reports.destroy', $projectReport) }}" class="d-inline js-confirm-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada project report.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $projectReports->links() }}</div>
  </div>
</div>
@endsection
