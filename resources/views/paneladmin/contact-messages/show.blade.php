@extends('layouts.user_type.auth')

@section('content')
<style>
  .contact-reply-history-item { border: 1px solid #eef0f4; border-radius: 8px; padding: 14px 16px; }
  .contact-reply-subject { color: #344767; font-size: .9rem; font-weight: 700; line-height: 1.3; }
  .contact-reply-meta { color: #8392ab; font-size: .72rem; line-height: 1.45; }
  .contact-reply-body { color: #475569; display: -webkit-box; font-size: .82rem; line-height: 1.5; margin-bottom: 0; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 3; white-space: pre-line; }
  .contact-line-clamp-1 { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Pesan Kontak</h6>
        <span class="badge badge-sm {{ $contactMessage->statusBadgeClass() }}">{{ $contactMessage->statusLabel() }}</span>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Nama</p>
            <p class="text-sm font-weight-bold">{{ $contactMessage->name }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Email</p>
            <p class="text-sm">{{ $contactMessage->email }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Telepon / WhatsApp</p>
            <p class="text-sm">{{ $contactMessage->phone ?: '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Subjek</p>
            <p class="text-sm">{{ $contactMessage->subject ?: '-' }}</p>
          </div>
        </div>
        <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Pesan</p>
        <p class="text-sm text-dark border-radius-lg bg-gray-100 p-3">{{ $contactMessage->message }}</p>
        <p class="text-xs text-secondary mb-1">Masuk pada {{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
        <p class="text-xs text-secondary mb-1">IP Address: {{ $contactMessage->ip_address ?: '-' }}</p>
        <p class="text-xs text-secondary mb-0">User Agent: {{ $contactMessage->user_agent ?: '-' }}</p>
      </div>
      <div class="card-footer pt-0 d-flex flex-wrap gap-2">
        <a href="{{ route('paneladmin.contact-messages.index') }}" class="btn btn-outline-secondary mb-0">Kembali</a>
        <a href="{{ route('paneladmin.email-center.compose', ['source' => 'contact_message', 'id' => $contactMessage->id]) }}" class="btn bg-gradient-info mb-0">Balas via Email Center</a>
        @if($contactMessage->whatsappUrl())
          <a href="{{ $contactMessage->whatsappUrl() }}" target="_blank" rel="noopener" class="btn bg-gradient-success mb-0">Chat WhatsApp</a>
        @endif
        @if($contactMessage->status === 'unread')
          <form method="POST" action="{{ route('paneladmin.contact-messages.read', $contactMessage) }}" class="d-inline js-confirm-submit">
            @csrf
            @method('PATCH')
            <button class="btn bg-gradient-info mb-0" type="submit">Tandai Dibaca</button>
          </form>
        @endif
        @if($contactMessage->status !== 'replied')
          <form method="POST" action="{{ route('paneladmin.contact-messages.replied', $contactMessage) }}" class="d-inline js-confirm-submit">
            @csrf
            @method('PATCH')
            <button class="btn bg-gradient-success mb-0" type="submit">Tandai Dibalas</button>
          </form>
        @endif
        <form method="POST" action="{{ route('paneladmin.contact-messages.destroy', $contactMessage) }}" class="d-inline js-confirm-delete">
          @csrf
          @method('DELETE')
          <button class="btn bg-gradient-danger mb-0" type="submit">Hapus</button>
        </form>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Riwayat Balasan Email</h6>
        <span class="text-xs text-secondary">{{ $contactMessage->replies->count() }} balasan</span>
      </div>
      <div class="card-body">
        @forelse($contactMessage->replies->sortByDesc('sent_at') as $reply)
          <div class="contact-reply-history-item mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
              <p class="contact-reply-subject contact-line-clamp-1 mb-0">{{ $reply->subject ?: '(tanpa subject)' }}</p>
              <span class="text-xs text-secondary">{{ ($reply->sent_at ?? $reply->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="d-flex flex-wrap gap-3 mb-2">
              <span class="contact-reply-meta">Kepada: {{ $reply->to_email ?: '-' }}</span>
              @if($reply->sender)
                <span class="contact-reply-meta">Dikirim oleh: {{ $reply->sender->name }}</span>
              @else
                <span class="contact-reply-meta">Dikirim oleh: -</span>
              @endif
            </div>
            <p class="contact-reply-body">{{ strip_tags($reply->body ?: '-') }}</p>
          </div>
        @empty
          <p class="text-sm text-secondary mb-0">Belum ada balasan email untuk pesan ini.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
