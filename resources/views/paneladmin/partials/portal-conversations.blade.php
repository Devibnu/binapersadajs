@once
  @push('styles')
    <style>
      .portal-admin-chat-list { display: flex; flex-direction: column; gap: 12px; }
      .portal-admin-chat-item { display: flex; }
      .portal-admin-chat-item.admin { justify-content: flex-end; }
      .portal-admin-chat-bubble { max-width: min(720px, 88%); border-radius: 8px; padding: .85rem 1rem; border: 1px solid #e9ecef; background: #e9f5ff; }
      .portal-admin-chat-item.admin .portal-admin-chat-bubble { background: #f4f5f7; border-color: #e9ecef; }
      .portal-admin-chat-meta { display: flex; flex-wrap: wrap; gap: .45rem; align-items: center; font-size: 11px; color: #67748e; margin-bottom: .35rem; }
      .portal-admin-chat-message { white-space: pre-wrap; overflow-wrap: anywhere; color: #344767; font-size: 13px; }
    </style>
  @endpush
@endonce

<div class="card shadow-sm border-0 mt-4">
  <div class="card-header bg-transparent border-0 pb-0">
    <h5 class="mb-1">Komunikasi Client</h5>
    <p class="text-sm text-secondary mb-0">Histori pertanyaan dan balasan terkait data ini.</p>
  </div>
  <div class="card-body">
    <div class="portal-admin-chat-list mb-4">
      @forelse($conversations as $conversation)
        <div class="portal-admin-chat-item {{ $conversation->sender_type === 'admin' ? 'admin' : 'client' }}">
          <div class="portal-admin-chat-bubble">
            <div class="portal-admin-chat-meta">
              <strong>{{ $conversation->senderName() }}</strong>
              <span>{{ $conversation->sender_type === 'admin' ? 'Admin' : 'Client' }}</span>
              <span>{{ $conversation->created_at?->format('d M Y H:i') }}</span>
            </div>
            <div class="portal-admin-chat-message">{{ $conversation->message }}</div>
          </div>
        </div>
      @empty
        <p class="text-sm text-secondary mb-0">Belum ada percakapan.</p>
      @endforelse
    </div>

    <form method="POST" action="{{ $storeRoute }}" class="mb-0">
      @csrf
      <div class="mb-3">
        <label class="form-label">Balasan Admin</label>
        <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn bg-gradient-primary mb-0"><i class="fas fa-paper-plane me-1"></i>Kirim Balasan</button>
      </div>
    </form>
  </div>
</div>
