@once
  @push('styles')
    <style>
      .portal-chat-list { display: flex; flex-direction: column; gap: 12px; }
      .portal-chat-item { display: flex; }
      .portal-chat-item.client { justify-content: flex-end; }
      .portal-chat-bubble { max-width: min(680px, 88%); border-radius: 8px; padding: .85rem 1rem; border: 1px solid #e8edf5; background: #f4f6f9; }
      .portal-chat-item.client .portal-chat-bubble { background: #e0f2fe; border-color: #bae6fd; }
      .portal-chat-meta { display: flex; flex-wrap: wrap; gap: .45rem; align-items: center; font-size: 11px; color: #64748b; margin-bottom: .35rem; }
      .portal-chat-message { white-space: pre-wrap; overflow-wrap: anywhere; color: #172033; }
    </style>
  @endpush
@endonce

<div class="card iqm-card">
  <div class="card-body">
    <div class="iqm-card-header">
      <h5 class="fw-bold mb-1">Pertanyaan & Komunikasi</h5>
      <p class="text-secondary small mb-0">Diskusi terkait data yang sedang dibuka.</p>
    </div>

    <div class="portal-chat-list mb-4">
      @forelse($conversations as $conversation)
        <div class="portal-chat-item {{ $conversation->sender_type === 'client' ? 'client' : 'admin' }}">
          <div class="portal-chat-bubble">
            <div class="portal-chat-meta">
              <strong>{{ $conversation->senderName() }}</strong>
              <span>{{ $conversation->created_at?->format('d M Y H:i') }}</span>
            </div>
            <div class="portal-chat-message">{{ $conversation->message }}</div>
          </div>
        </div>
      @empty
        <div class="text-center text-secondary py-4">Belum ada percakapan.</div>
      @endforelse
    </div>

    <form method="POST" action="{{ $storeRoute }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold">Pesan</label>
        <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary mb-0"><i class="fas fa-paper-plane me-1"></i>Kirim</button>
      </div>
    </form>
  </div>
</div>
