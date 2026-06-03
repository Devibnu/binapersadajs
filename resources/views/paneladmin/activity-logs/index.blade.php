@extends('layouts.user_type.auth')

@section('content')
@php
  $actionLabel = fn (?string $action) => ucwords(str_replace('_', ' ', (string) $action));
@endphp

<style>
  .activity-log-filter .form-control,
  .activity-log-filter .form-select {
    min-height: 42px;
  }

  .activity-log-description {
    max-width: 360px;
    white-space: normal;
  }

  .activity-log-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .activity-log-pages {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .activity-log-pages .page-link {
    min-width: 36px;
    height: 36px;
    border-radius: 10px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e9ecef;
    color: #344767;
    font-size: 13px;
    font-weight: 600;
    padding: 0 12px;
    background: #fff;
  }

  .activity-log-pages .page-item.active .page-link {
    color: #fff;
    border-color: transparent;
    background-image: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%);
    box-shadow: 0 4px 10px rgba(33, 82, 255, .25);
  }

  .activity-log-pages .page-item.disabled .page-link {
    color: #adb5bd;
    background: #f8f9fa;
    pointer-events: none;
  }

  @media (max-width: 575.98px) {
    .activity-log-pagination {
      align-items: flex-start;
      flex-direction: column;
    }

    .activity-log-pages {
      width: 100%;
      overflow-x: auto;
      flex-wrap: nowrap;
      padding-bottom: 4px;
    }
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
          <div>
            <h6 class="mb-1">Activity Logs</h6>
            <p class="text-sm mb-0">Jejak aktivitas penting admin pada pengelolaan website.</p>
          </div>
          <span class="badge bg-gradient-dark">{{ number_format($activityLogs->total()) }} Aktivitas</span>
        </div>

        <form method="GET" action="{{ route('paneladmin.activity-logs.index') }}" class="activity-log-filter">
          <div class="row g-2">
            <div class="col-xl-3 col-lg-4 col-md-6">
              <input type="text" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Cari deskripsi, data, IP...">
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
              <input type="text" name="user" class="form-control" value="{{ $filters['user'] ?? '' }}" placeholder="Nama user...">
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
              <select name="role" class="form-control">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ $role }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
              <select name="module" class="form-control">
                <option value="">Semua Module</option>
                @foreach($modules as $module)
                  <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
              <select name="action" class="form-control">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                  <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ $actionLabel($action) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-xl-1 col-lg-4 col-md-6">
              <button type="submit" class="btn bg-gradient-primary w-100 mb-0">Filter</button>
            </div>
            <div class="col-lg-3 col-md-6">
              <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" title="Tanggal dari">
            </div>
            <div class="col-lg-3 col-md-6">
              <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" title="Tanggal sampai">
            </div>
            <div class="col-lg-3 col-md-6">
              <a href="{{ route('paneladmin.activity-logs.index') }}" class="btn btn-outline-secondary w-100 mb-0">Reset Filter</a>
            </div>
          </div>
        </form>
      </div>

      <div class="card-body px-0 pt-4 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal / User</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Module</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP Address</th>
                <th class="text-secondary opacity-7">Detail</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activityLogs as $activityLog)
                <tr>
                  <td>
                    <div class="d-flex flex-column px-3 py-1">
                      <span class="text-sm font-weight-bold">{{ $activityLog->created_at->format('d M Y H:i') }}</span>
                      <span class="text-xs font-weight-bold text-dark">{{ $activityLog->displayUserName() }}</span>
                      <span class="text-xs text-secondary">{{ $activityLog->displayUserEmail() }}</span>
                    </div>
                  </td>
                  <td><span class="text-xs font-weight-bold">{{ $activityLog->displayRoleName() }}</span></td>
                  <td><span class="text-sm">{{ $activityLog->module }}</span></td>
                  <td>
                    <span class="badge badge-sm {{ $activityLog->actionBadgeClass() }}">{{ $activityLog->actionLabel() }}</span>
                  </td>
                  <td>
                    <div class="activity-log-description">
                      <p class="text-xs text-dark font-weight-bold mb-1">{{ \Illuminate\Support\Str::limit($activityLog->structuredDescription(), 90) }}</p>
                      @if($activityLog->recordId())
                        <span class="text-xxs text-secondary">ID Data: {{ $activityLog->recordId() }}</span>
                      @endif
                    </div>
                  </td>
                  <td><span class="text-xs text-secondary">{{ $activityLog->ip_address ?: '-' }}</span></td>
                  <td>
                    <a href="{{ route('paneladmin.activity-logs.show', $activityLog) }}" class="btn btn-link text-secondary font-weight-bold text-xs mb-0 px-0">Detail</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-sm text-secondary">Belum ada aktivitas pada filter ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="px-4 pt-4 pb-2">
          <div class="activity-log-pagination">
            <p class="text-sm text-secondary mb-0">
              Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} aktivitas
            </p>

            @if($activityLogs->hasPages())
              <ul class="activity-log-pages">
                <li class="page-item {{ $activityLogs->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $activityLogs->previousPageUrl() ?: '#' }}">&lt; Previous</a>
                </li>
                @foreach($activityLogs->getUrlRange(1, $activityLogs->lastPage()) as $page => $url)
                  @if($page === 1 || $page === $activityLogs->lastPage() || abs($page - $activityLogs->currentPage()) <= 2)
                    <li class="page-item {{ $page === $activityLogs->currentPage() ? 'active' : '' }}">
                      <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                  @elseif(abs($page - $activityLogs->currentPage()) === 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                  @endif
                @endforeach
                <li class="page-item {{ $activityLogs->hasMorePages() ? '' : 'disabled' }}">
                  <a class="page-link" href="{{ $activityLogs->nextPageUrl() ?: '#' }}">Next &gt;</a>
                </li>
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
