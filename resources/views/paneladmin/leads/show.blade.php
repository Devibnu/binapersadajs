@extends('layouts.user_type.auth')

@section('content')
<style>
  .lead-email-history-item { border: 1px solid #eef0f4; border-radius: 8px; padding: 14px 16px; }
  .lead-email-subject { color: #344767; font-size: .9rem; font-weight: 700; line-height: 1.3; }
  .lead-email-meta { color: #8392ab; font-size: .72rem; line-height: 1.45; }
  .lead-email-body { color: #475569; display: -webkit-box; font-size: .82rem; line-height: 1.5; margin-bottom: 0; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 3; white-space: pre-line; }
  .lead-line-clamp-1 { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
</style>

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Lead</h6>
        <span class="badge badge-sm {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success text-white">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger text-white">{{ session('error') }}</div>
        @endif
        <div class="row">
          @foreach([
            'Nama' => $lead->name ?: '-',
            'Email' => $lead->email,
            'Telepon / WhatsApp' => $lead->phone ?: '-',
            'Perusahaan' => $lead->company ?: '-',
            'Minat' => $lead->interest ?: '-',
            'Sumber' => $lead->sourceLabel(),
          ] as $label => $value)
            <div class="col-md-6 mb-3">
              <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">{{ $label }}</p>
              <p class="text-sm mb-0">{{ $value }}</p>
            </div>
          @endforeach
        </div>
        <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Pesan / Kebutuhan</p>
        <p class="text-sm text-dark border-radius-lg bg-gray-100 p-3">{{ $lead->message ?: '-' }}</p>
        <p class="text-xs text-secondary mb-1">Masuk pada {{ $lead->created_at->format('d/m/Y H:i') }}</p>
        <p class="text-xs text-secondary mb-1">IP Address: {{ $lead->ip_address ?: '-' }}</p>
        <p class="text-xs text-secondary mb-0">User Agent: {{ $lead->user_agent ?: '-' }}</p>
      </div>
      <div class="card-footer pt-0 d-flex flex-wrap gap-2">
        <a href="{{ route('paneladmin.leads.index') }}" class="btn btn-outline-secondary mb-0">Kembali</a>
        @if($lead->whatsappUrl())
          <a href="{{ $lead->whatsappUrl() }}" target="_blank" rel="noopener" class="btn bg-gradient-success mb-0">Chat WhatsApp</a>
        @endif
        @if(auth()->user()->canAccess('leads.delete'))
          <form method="POST" action="{{ route('paneladmin.leads.destroy', $lead) }}" class="d-inline js-confirm-delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn bg-gradient-danger mb-0">Hapus</button>
          </form>
        @endif
      </div>
    </div>
  </div>

  @if(auth()->user()->canAccess('leads.update') || auth()->user()->canAccess('leads.email'))
  <div class="col-lg-4">
    @if(auth()->user()->canAccess('leads.email'))
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Kirim Email</h6>
      </div>
      <div class="card-body">
        <a href="{{ route('paneladmin.email-center.compose', ['source' => 'lead', 'id' => $lead->id]) }}" class="btn bg-gradient-info mb-0 w-100">Kirim via Email Center</a>
      </div>
    </div>
    @endif

    @if(auth()->user()->canAccess('leads.update'))
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Update Status</h6>
        <p class="text-sm mb-0">Pantau perkembangan prospek sampai konversi.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.leads.status', $lead) }}" class="js-confirm-submit">
          @csrf
          @method('PATCH')
          <div class="form-group">
            <label>Status Lead</label>
            <select name="status" class="form-control" required>
              @foreach(['new' => 'Baru', 'contacted' => 'Dihubungi', 'qualified' => 'Prospek', 'converted' => 'Konversi', 'closed' => 'Ditutup'] as $key => $label)
                <option value="{{ $key }}" @selected($lead->status === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn bg-gradient-primary mb-0 w-100">Simpan Status</button>
        </form>
      </div>
    </div>
    @endif
  </div>
  @endif

  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Riwayat Email</h6>
        <p class="text-sm mb-0">Email follow up yang dikirim dari panel admin.</p>
      </div>
      <div class="card-body">
        @forelse($lead->emailHistories as $history)
          <div class="lead-email-history-item mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
              <p class="lead-email-subject lead-line-clamp-1 mb-0">{{ $history->subject ?: '(tanpa subject)' }}</p>
              <span class="text-xs text-secondary">{{ $history->sent_at?->format('d/m/Y H:i') ?: '-' }}</span>
            </div>
            <div class="d-flex flex-wrap gap-3 mb-2">
              <span class="lead-email-meta">Kepada: {{ $history->to_email ?: '-' }}</span>
              <span class="lead-email-meta">Dikirim oleh: {{ $history->sender?->name ?: 'System' }}</span>
            </div>
            <p class="lead-email-body">{{ strip_tags($history->body ?: '-') }}</p>
          </div>
        @empty
          <p class="text-sm text-secondary mb-0">Belum ada email follow up.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
