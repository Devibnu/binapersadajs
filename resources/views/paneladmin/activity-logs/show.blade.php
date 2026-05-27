@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8 col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h6>Detail Activity Log</h6>
          <p class="text-sm mb-0">Informasi jejak aktivitas admin.</p>
        </div>
        <span class="badge {{ $activityLog->actionBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $activityLog->action)) }}</span>
      </div>
      <div class="card-body">
        <dl class="row text-sm mb-4">
          <dt class="col-md-3 text-secondary">Tanggal</dt>
          <dd class="col-md-9">{{ $activityLog->created_at->format('d M Y H:i:s') }}</dd>
          <dt class="col-md-3 text-secondary">User</dt>
          <dd class="col-md-9">{{ $activityLog->user_name ?: 'System' }}</dd>
          <dt class="col-md-3 text-secondary">Module</dt>
          <dd class="col-md-9">{{ $activityLog->module }}</dd>
          <dt class="col-md-3 text-secondary">Aksi</dt>
          <dd class="col-md-9">{{ ucwords(str_replace('_', ' ', $activityLog->action)) }}</dd>
          <dt class="col-md-3 text-secondary">Deskripsi</dt>
          <dd class="col-md-9">{{ $activityLog->description ?: '-' }}</dd>
          <dt class="col-md-3 text-secondary">Subject</dt>
          <dd class="col-md-9 text-break">{{ $activityLog->subject_type ?: '-' }}{{ $activityLog->subject_id ? ' #' . $activityLog->subject_id : '' }}</dd>
          <dt class="col-md-3 text-secondary">IP Address</dt>
          <dd class="col-md-9">{{ $activityLog->ip_address ?: '-' }}</dd>
          <dt class="col-md-3 text-secondary">User Agent</dt>
          <dd class="col-md-9 text-break">{{ $activityLog->user_agent ?: '-' }}</dd>
        </dl>

        <h6 class="text-sm">Properties</h6>
        <pre class="bg-gray-100 border-radius-lg p-3 text-xs mb-4" style="white-space: pre-wrap;">{{ $activityLog->properties ? json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '-' }}</pre>

        <a href="{{ route('paneladmin.activity-logs.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection
