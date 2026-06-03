@extends('layouts.user_type.auth')

@section('content')
@php
  $recordName = $activityLog->recordName();
  $recordId = $activityLog->recordId();
@endphp

<style>
  .activity-detail-label {
    color: #8392ab;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    margin-bottom: 6px;
    text-transform: uppercase;
  }

  .activity-detail-value {
    color: #344767;
    font-size: 14px;
    font-weight: 600;
    word-break: break-word;
  }

  .activity-detail-box {
    border: 1px solid #e9ecef;
    border-radius: 14px;
    padding: 16px;
    height: 100%;
    background: #fff;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h6 class="mb-1">Detail Activity Log</h6>
          <p class="text-sm mb-0">Informasi lengkap jejak aktivitas admin.</p>
        </div>
        <span class="badge {{ $activityLog->actionBadgeClass() }}">{{ $activityLog->actionLabel() }}</span>
      </div>

      <div class="card-body">
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Nama User</div>
              <div class="activity-detail-value">{{ $activityLog->displayUserName() }}</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Email User</div>
              <div class="activity-detail-value">{{ $activityLog->displayUserEmail() }}</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Role User</div>
              <div class="activity-detail-value">{{ $activityLog->displayRoleName() }}</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Module</div>
              <div class="activity-detail-value">{{ $activityLog->module }}</div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Action</div>
              <div class="activity-detail-value">{{ $activityLog->actionLabel() }}</div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Waktu Aktivitas</div>
              <div class="activity-detail-value">{{ $activityLog->created_at->translatedFormat('d F Y H:i:s') }}</div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Nama Data</div>
              <div class="activity-detail-value">{{ $recordName ?: '-' }}</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">ID Data</div>
              <div class="activity-detail-value">{{ $recordId ?: '-' }}</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">IP Address</div>
              <div class="activity-detail-value">{{ $activityLog->ip_address ?: '-' }}</div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Browser</div>
              <div class="activity-detail-value">{{ $activityLog->browserLabel() }}</div>
            </div>
          </div>
          <div class="col-lg-8 col-md-6">
            <div class="activity-detail-box">
              <div class="activity-detail-label">Model Type</div>
              <div class="activity-detail-value">{{ $activityLog->modelType() ?: '-' }}</div>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <div class="activity-detail-label">Deskripsi</div>
          <div class="bg-gray-100 border-radius-lg p-3 text-sm text-dark">
            {{ $activityLog->description ?: $activityLog->structuredDescription() }}
          </div>
        </div>

        <div class="mb-4">
          <div class="activity-detail-label">User Agent</div>
          <div class="bg-gray-100 border-radius-lg p-3 text-xs text-secondary text-break">
            {{ $activityLog->user_agent ?: '-' }}
          </div>
        </div>

        <div class="mb-4">
          <div class="activity-detail-label">Properties</div>
          <pre class="bg-gray-100 border-radius-lg p-3 text-xs mb-0" style="white-space: pre-wrap;">{{ $activityLog->properties ? json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
        </div>

        <a href="{{ route('paneladmin.activity-logs.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection
