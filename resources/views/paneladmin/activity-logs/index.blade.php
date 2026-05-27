@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Activity Logs</h6>
        <p class="text-sm mb-3">Jejak aktivitas penting admin pada pengelolaan website.</p>
        <form method="GET" action="{{ route('paneladmin.activity-logs.index') }}" class="row g-2">
          <div class="col-xl-3 col-md-6">
            <input type="text" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Cari deskripsi atau aksi...">
          </div>
          <div class="col-xl-2 col-md-3">
            <select name="module" class="form-control">
              <option value="">Semua Module</option>
              @foreach($modules as $module)
                <option value="{{ $module }}" {{ ($filters['module'] ?? '') === $module ? 'selected' : '' }}>{{ $module }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xl-2 col-md-3">
            <select name="action" class="form-control">
              <option value="">Semua Aksi</option>
              @foreach($actions as $action)
                <option value="{{ $action }}" {{ ($filters['action'] ?? '') === $action ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xl-2 col-md-4">
            <input type="text" name="user" class="form-control" value="{{ $filters['user'] ?? '' }}" placeholder="Nama admin...">
          </div>
          <div class="col-xl-3 col-md-8 d-flex gap-2">
            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" title="Tanggal dari">
            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" title="Tanggal sampai">
          </div>
          <div class="col-12 d-flex gap-2 mt-3">
            <button type="submit" class="btn bg-gradient-primary mb-0">Filter</button>
            <a href="{{ route('paneladmin.activity-logs.index') }}" class="btn btn-outline-secondary mb-0">Reset</a>
          </div>
        </form>
      </div>
      <div class="card-body px-0 pt-4 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal / User</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Module</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aksi</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP Address</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($activityLogs as $activityLog)
                <tr>
                  <td>
                    <div class="d-flex flex-column px-3 py-1">
                      <span class="text-sm font-weight-bold">{{ $activityLog->created_at->format('d M Y H:i') }}</span>
                      <span class="text-xs text-secondary">{{ $activityLog->user_name ?: 'System' }}</span>
                    </div>
                  </td>
                  <td><span class="text-sm">{{ $activityLog->module }}</span></td>
                  <td>
                    <span class="badge badge-sm {{ $activityLog->actionBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $activityLog->action)) }}</span>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($activityLog->description ?: '-', 70) }}</p></td>
                  <td><span class="text-xs text-secondary">{{ $activityLog->ip_address ?: '-' }}</span></td>
                  <td>
                    <a href="{{ route('paneladmin.activity-logs.show', $activityLog) }}" class="text-secondary font-weight-bold text-xs">Detail</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-sm text-secondary">Belum ada aktivitas pada filter ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">{{ $activityLogs->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
