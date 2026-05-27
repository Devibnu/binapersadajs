@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8">
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
        <a href="#form-balasan-email" class="btn bg-gradient-info mb-0">Balas Email</a>
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
          <div class="border border-radius-lg p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between mb-2">
              <p class="text-sm font-weight-bold mb-0">{{ $reply->subject }}</p>
              <span class="text-xs text-secondary">{{ ($reply->sent_at ?? $reply->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-xs text-secondary mb-2">
              Kepada: {{ $reply->to_email }}
              @if($reply->sender)
                | Dikirim oleh: {{ $reply->sender->name }}
              @endif
            </p>
            <p class="text-sm text-dark mb-0" style="white-space: pre-line;">{{ $reply->body }}</p>
          </div>
        @empty
          <p class="text-sm text-secondary mb-0">Belum ada balasan email untuk pesan ini.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-4" id="form-balasan-email">
      <div class="card-header pb-0">
        <h6>Balas Email</h6>
        <p class="text-sm mb-0">Kirim balasan langsung melalui SMTP yang dikonfigurasi pada aplikasi.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.contact-messages.reply', $contactMessage) }}" class="js-confirm-submit">
          @csrf
          <div class="form-group">
            <label>Kepada</label>
            <input type="email" name="to_email" value="{{ old('to_email', $contactMessage->email) }}" class="form-control @error('to_email') is-invalid @enderror" required>
            @error('to_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Subjek</label>
            <input type="text" name="subject" value="{{ old('subject', 'Re: ' . ($contactMessage->subject ?: 'Pesan website Bina Persada JS')) }}" class="form-control @error('subject') is-invalid @enderror" maxlength="150" required>
            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Isi Balasan</label>
            <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="9" required minlength="10" placeholder="Tulis balasan email untuk pengirim...">{{ old('body') }}</textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn bg-gradient-success mb-0 w-100">Kirim Balasan</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
