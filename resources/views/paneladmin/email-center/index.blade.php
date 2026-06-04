@extends('layouts.user_type.auth')

@section('content')
@php
  $folders = [
    'inbox' => ['label' => 'Inbox', 'icon' => 'fa-inbox'],
    'sent' => ['label' => 'Sent Mail', 'icon' => 'fa-paper-plane'],
    'draft' => ['label' => 'Draft', 'icon' => 'fa-file-alt'],
    'trash' => ['label' => 'Trash', 'icon' => 'fa-trash'],
  ];
@endphp

<style>
  .email-center-shell { min-height: 680px; }
  .email-center-shell .min-width-0 { min-width: 0; }
  .email-sidebar-card, .email-list-card, .email-preview-card { height: calc(100vh - 160px); min-height: 620px; }
  .email-folder-link { border-radius: 10px; color: #344767; display: flex; gap: 10px; padding: 10px 12px; transition: all .18s ease; }
  .email-folder-link.active, .email-folder-link:hover { background: #eef5ff; color: #2152ff; }
  .email-list-card .card-body { overflow-y: auto; }
  .email-list { padding: 10px; }
  .email-list-item { background: #fff; border: 1px solid #eef0f4; border-radius: 8px; color: #344767; display: block; margin-bottom: 8px; padding: 13px 14px; text-align: left; transition: all .18s ease; width: 100%; }
  .email-list-item:hover { background: #f8fbff; border-color: #d8e3ff; box-shadow: 0 8px 18px rgba(20, 20, 43, .05); transform: translateY(-1px); }
  .email-list-item.active { background: #eef5ff; border-color: #2152ff; box-shadow: 0 10px 24px rgba(33, 82, 255, .11); }
  .email-list-item.unread { background: #fbfdff; border-left: 3px solid #2152ff; }
  .email-list-item.unread.active { background: #eef5ff; }
  .email-list-button { cursor: pointer; }
  .email-sender-name { color: #344767; font-weight: 700; min-width: 0; }
  .email-sender-address { color: #8392ab; min-width: 0; }
  .email-meta-time { color: #8392ab; flex: 0 0 auto; font-size: .72rem; margin-left: 10px; white-space: nowrap; }
  .email-subject { color: #344767; font-size: .9rem; font-weight: 700; line-height: 1.35; margin-bottom: 4px; }
  .email-preview-text { color: #67748e; font-size: .78rem; line-height: 1.45; margin-bottom: 0; }
  .email-line-clamp-1, .email-line-clamp-2 { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; }
  .email-line-clamp-1 { -webkit-line-clamp: 1; }
  .email-line-clamp-2 { -webkit-line-clamp: 2; }
  .email-status-badge { border-radius: 999px; font-size: .63rem; font-weight: 700; letter-spacing: 0; padding: 4px 8px; text-transform: uppercase; }
  .email-status-badge.read { background: #e9ecef; color: #67748e; }
  .email-status-badge.unread { background: #dfe8ff; color: #2152ff; }
  .email-attachment-icon { color: #8392ab; font-size: .78rem; }
  .email-preview-card { position: sticky; top: 1rem; }
  .email-preview-card .card-body { overflow-y: auto; }
  .email-preview-subject { color: #344767; line-height: 1.35; }
  .email-preview-meta { border-bottom: 1px solid #eef0f4; border-top: 1px solid #eef0f4; margin: 18px 0; padding: 14px 0; }
  .email-preview-meta-row { display: grid; gap: 10px; grid-template-columns: 54px 1fr; margin-bottom: 8px; }
  .email-preview-meta-row:last-child { margin-bottom: 0; }
  .email-preview-body { background: #f8f9fa; border-radius: 8px; color: #344767; line-height: 1.7; max-height: 380px; overflow-y: auto; white-space: pre-wrap; }
  .email-empty-state { align-items: center; color: #8392ab; display: flex; flex-direction: column; justify-content: center; min-height: 280px; text-align: center; }
  @media (max-width: 1199px) {
    .email-sidebar-card, .email-list-card, .email-preview-card { height: auto; min-height: auto; }
    .email-preview-card { position: static; }
  }
  @media (max-width: 767px) {
    .email-list { padding: 8px; }
    .email-list-item { padding: 12px; }
    .email-meta-time { margin-left: 0; margin-top: 6px; }
    .email-preview-meta-row { grid-template-columns: 1fr; gap: 2px; }
  }
</style>

<div class="row email-center-shell">
  <div class="col-xl-2 col-lg-3">
    <div class="card mb-4 email-sidebar-card">
      <div class="card-body">
        <a href="{{ route('paneladmin.email-center.compose') }}" class="btn bg-gradient-primary w-100 mb-3">
          <i class="fas fa-pen me-2"></i>Tulis Email
        </a>
        <a href="{{ route('paneladmin.email-center.accounts') }}" class="btn btn-outline-secondary w-100 mb-3">
          <i class="fas fa-user-cog me-2"></i>Email Accounts
        </a>

        <form method="GET" action="{{ route('paneladmin.email-center.index') }}" class="mb-3">
          <input type="hidden" name="folder" value="{{ $folder }}">
          <select name="account_id" class="form-control" onchange="this.form.submit()">
            @forelse($accounts as $item)
              <option value="{{ $item->id }}" @selected($account?->id === $item->id)>{{ $item->name }} - {{ $item->email }}</option>
            @empty
              <option value="">Belum ada akun</option>
            @endforelse
          </select>
        </form>

        <div class="d-flex flex-column gap-1">
          @foreach($folders as $key => $item)
            <a class="email-folder-link {{ $folder === $key ? 'active' : '' }}" href="{{ route('paneladmin.email-center.index', array_filter(['folder' => $key, 'account_id' => $account?->id])) }}">
              <i class="fas {{ $item['icon'] }} mt-1"></i>
              <span>{{ $item['label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-5 col-lg-4">
    <div class="card mb-4 email-list-card">
      <div class="card-header pb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h6 class="mb-1">{{ $folders[$folder]['label'] ?? 'Email Center' }}</h6>
            <p class="text-sm mb-0">{{ $account?->email ?: 'Pilih email account' }}</p>
          </div>
          <span class="badge bg-gradient-light text-dark">{{ method_exists($messages, 'total') ? $messages->total() : $messages->count() }} email</span>
        </div>
        <form method="GET" action="{{ route('paneladmin.email-center.index') }}" class="row g-2 mt-3">
          <input type="hidden" name="folder" value="{{ $folder }}">
          <input type="hidden" name="account_id" value="{{ $account?->id }}">
          <div class="col">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari pengirim, subject, isi email...">
          </div>
          <div class="col-auto">
            <button class="btn bg-gradient-primary mb-0" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
      <div class="card-body p-0">
        @if(session('success'))<div class="alert alert-success text-white m-3">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger text-white m-3">{{ session('error') }}</div>@endif
        @if($imapNotice)<div class="alert alert-warning text-white m-3">{{ $imapNotice }}</div>@endif

        <div class="email-list">
          @if($folder === 'inbox')
            @forelse($messages as $message)
              @php
                $fromName = $message->from;
                $fromEmail = $message->from;
                if (preg_match('/^(.*)<(.+)>$/', $message->from, $matches)) {
                  $fromName = trim($matches[1], " \"'");
                  $fromEmail = trim($matches[2]);
                }
                $messageBody = $message->body ?: $message->preview ?: '-';
              @endphp
              <button
                type="button"
                class="email-list-item email-list-button {{ $message->seen ? '' : 'unread' }}"
                data-email-row
                data-uid="{{ $message->uid }}"
                data-from-name="{{ $fromName ?: $fromEmail }}"
                data-from-email="{{ $fromEmail }}"
                data-to="{{ $account?->email ?: '-' }}"
                data-subject="{{ $message->subject ?: '(tanpa subject)' }}"
                data-date="{{ $message->date }}"
                data-body="{{ $messageBody }}"
                data-preview="{{ $message->preview ?: '-' }}"
                data-seen="{{ $message->seen ? '1' : '0' }}"
                data-has-attachment="{{ $message->has_attachment ? '1' : '0' }}"
                data-reply-url="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'to' => $fromEmail, 'subject' => 'Re: ' . $message->subject, 'action_type' => 'reply']) }}"
                data-forward-url="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'subject' => 'Fwd: ' . $message->subject, 'body' => $messageBody, 'action_type' => 'forward']) }}"
              >
                <span class="d-flex justify-content-between align-items-start gap-2">
                  <span class="min-width-0">
                    <span class="email-sender-name email-line-clamp-1">{{ $fromName ?: $fromEmail }}</span>
                    <span class="email-sender-address text-xs email-line-clamp-1">{{ $fromEmail }}</span>
                  </span>
                  <span class="email-meta-time">{{ $message->date }}</span>
                </span>
                <span class="email-subject email-line-clamp-1 mt-2">{{ $message->subject ?: '(tanpa subject)' }}</span>
                <span class="email-preview-text email-line-clamp-2">{{ $message->preview ?: '-' }}</span>
                <span class="d-flex align-items-center gap-2 mt-2">
                  <span class="email-status-badge {{ $message->seen ? 'read' : 'unread' }}">{{ $message->seen ? 'Read' : 'Unread' }}</span>
                  @if($message->has_attachment)
                    <i class="fas fa-paperclip email-attachment-icon"></i>
                  @endif
                </span>
              </button>
            @empty
              <div class="p-4 text-center text-sm text-secondary">Belum ada email untuk ditampilkan.</div>
            @endforelse
          @else
            @forelse($messages as $message)
              @php
                $isActive = $selectedMessage && $selectedMessage->id === $message->id;
                $previewText = \Illuminate\Support\Str::limit(strip_tags($message->body ?: '-'), 140);
              @endphp
              <a class="email-list-item text-decoration-none {{ $isActive ? 'active' : '' }}" href="{{ route('paneladmin.email-center.index', ['folder' => $folder, 'account_id' => $account?->id, 'q' => $search, 'message' => $message->id]) }}">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <div class="min-width-0">
                    <div class="email-sender-name email-line-clamp-1">{{ $folder === 'sent' ? ($message->to_email ?: '-') : ($message->from_email ?: $message->to_email ?: '-') }}</div>
                    <div class="email-sender-address text-xs email-line-clamp-1">{{ $message->account?->email ?: $account?->email ?: '-' }}</div>
                  </div>
                  <span class="email-meta-time">{{ ($message->sent_at ?? $message->updated_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="email-subject email-line-clamp-1 mt-2">{{ $message->subject ?: '(tanpa subject)' }}</div>
                <p class="email-preview-text email-line-clamp-2">{{ $previewText }}</p>
                <div class="d-flex align-items-center gap-2 mt-2">
                  <span class="email-status-badge read">{{ ucfirst($message->status ?: $message->folder) }}</span>
                  @if($message->attachments->isNotEmpty())
                    <i class="fas fa-paperclip email-attachment-icon"></i>
                  @endif
                </div>
              </a>
            @empty
              <div class="p-4 text-center text-sm text-secondary">Folder ini masih kosong.</div>
            @endforelse
            @if(method_exists($messages, 'hasPages') && $messages->hasPages())
              <div class="px-3 pt-3">{{ $messages->links() }}</div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-5 col-lg-5">
    <div class="card mb-4 email-preview-card">
      <div class="card-header pb-0">
        <h6 class="mb-1">Preview Email</h6>
        <p class="text-sm mb-0">Pilih email pada daftar untuk melihat detail.</p>
      </div>
      <div class="card-body">
        @if($folder === 'inbox')
          <div id="inboxPreviewEmpty" class="email-empty-state">
            <i class="fas fa-envelope-open-text text-lg mb-3"></i>
            <span class="text-sm">Tidak ada email yang dipilih.</span>
          </div>

          <div id="inboxPreviewContent" class="d-none">
            <div class="d-flex justify-content-between align-items-start gap-3">
              <div class="min-width-0">
                <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Subject</p>
                <h5 class="email-preview-subject mb-0" data-preview-subject></h5>
              </div>
              <span class="email-status-badge read" data-preview-status></span>
            </div>

            <div class="email-preview-meta">
              <div class="email-preview-meta-row">
                <span class="text-xs text-uppercase text-secondary font-weight-bolder">From</span>
                <span class="text-sm" data-preview-from></span>
              </div>
              <div class="email-preview-meta-row">
                <span class="text-xs text-uppercase text-secondary font-weight-bolder">To</span>
                <span class="text-sm" data-preview-to></span>
              </div>
              <div class="email-preview-meta-row">
                <span class="text-xs text-uppercase text-secondary font-weight-bolder">Tanggal</span>
                <span class="text-sm" data-preview-date></span>
              </div>
            </div>

            <div class="email-preview-body p-3 text-sm mb-3" data-preview-body></div>
            <div class="mb-3 d-none" data-preview-attachment>
              <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2">Attachment</p>
              <span class="badge bg-gradient-secondary"><i class="fas fa-paperclip me-1"></i>Ada attachment</span>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
              <a href="#" class="btn bg-gradient-info mb-0" data-preview-reply>Reply</a>
              <a href="#" class="btn btn-outline-secondary mb-0" data-preview-forward>Forward</a>
              <form method="POST" action="{{ route('paneladmin.email-center.imap-action') }}" class="mb-0">
                @csrf
                <input type="hidden" name="account_id" value="{{ $account?->id }}">
                <input type="hidden" name="uid" value="" data-preview-uid>
                <input type="hidden" name="action" value="read" data-preview-read-action>
                <button class="btn btn-outline-primary mb-0" type="submit" data-preview-read-label>Mark Read</button>
              </form>
              <form method="POST" action="{{ route('paneladmin.email-center.imap-action') }}" class="mb-0">
                @csrf
                <input type="hidden" name="account_id" value="{{ $account?->id }}">
                <input type="hidden" name="uid" value="" data-preview-delete-uid>
                <input type="hidden" name="action" value="delete">
                <button class="btn bg-gradient-danger mb-0" type="submit">Delete</button>
              </form>
            </div>
          </div>
        @elseif($selectedMessage)
          <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Subject</p>
          <h5 class="email-preview-subject">{{ $selectedMessage->subject ?: '(tanpa subject)' }}</h5>
          <div class="email-preview-meta">
            <div class="email-preview-meta-row">
              <span class="text-xs text-uppercase text-secondary font-weight-bolder">From</span>
              <span class="text-sm">{{ $selectedMessage->from_email ?: '-' }}</span>
            </div>
            <div class="email-preview-meta-row">
              <span class="text-xs text-uppercase text-secondary font-weight-bolder">To</span>
              <span class="text-sm">{{ $selectedMessage->to_email ?: '-' }}</span>
            </div>
            <div class="email-preview-meta-row">
              <span class="text-xs text-uppercase text-secondary font-weight-bolder">Tanggal</span>
              <span class="text-sm">{{ ($selectedMessage->sent_at ?? $selectedMessage->updated_at)->format('d/m/Y H:i') }}</span>
            </div>
          </div>
          <div class="email-preview-body p-3 text-sm mb-3">{!! nl2br(e($selectedMessage->body ?: '-')) !!}</div>
          @if($selectedMessage->attachments->isNotEmpty())
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2">Attachment</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
              @foreach($selectedMessage->attachments as $attachment)
                <span class="badge bg-gradient-secondary"><i class="fas fa-paperclip me-1"></i>{{ $attachment->original_name }}</span>
              @endforeach
            </div>
          @endif
          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'to' => $selectedMessage->from_email, 'subject' => 'Re: ' . $selectedMessage->subject, 'action_type' => 'reply']) }}" class="btn bg-gradient-info mb-0">Reply</a>
            <a href="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'subject' => 'Fwd: ' . $selectedMessage->subject, 'body' => $selectedMessage->body, 'action_type' => 'forward']) }}" class="btn btn-outline-secondary mb-0">Forward</a>
            @if($selectedMessage->folder === 'draft')
              <a href="{{ route('paneladmin.email-center.drafts.edit', $selectedMessage) }}" class="btn bg-gradient-info mb-0">Edit Draft</a>
            @endif
            @if($selectedMessage->folder !== 'trash')
              <form method="POST" action="{{ route('paneladmin.email-center.messages.delete', $selectedMessage) }}" class="js-confirm-submit mb-0">
                @csrf
                @method('PATCH')
                <button class="btn bg-gradient-danger mb-0" type="submit">Delete</button>
              </form>
            @else
              <form method="POST" action="{{ route('paneladmin.email-center.messages.restore', $selectedMessage) }}" class="js-confirm-submit mb-0">
                @csrf
                @method('PATCH')
                <button class="btn bg-gradient-success mb-0" type="submit">Restore</button>
              </form>
              <form method="POST" action="{{ route('paneladmin.email-center.messages.force-delete', $selectedMessage) }}" class="js-confirm-delete mb-0">
                @csrf
                @method('DELETE')
                <button class="btn bg-gradient-danger mb-0" type="submit">Delete Permanently</button>
              </form>
            @endif
          </div>
        @else
          <div class="email-empty-state">
            <i class="fas fa-envelope-open-text text-lg mb-3"></i>
            <span class="text-sm">Tidak ada email yang dipilih.</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if($folder === 'inbox')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const rows = Array.from(document.querySelectorAll('[data-email-row]'));
      const emptyState = document.getElementById('inboxPreviewEmpty');
      const content = document.getElementById('inboxPreviewContent');
      if (!rows.length || !content) {
        return;
      }

      const fields = {
        subject: content.querySelector('[data-preview-subject]'),
        status: content.querySelector('[data-preview-status]'),
        from: content.querySelector('[data-preview-from]'),
        to: content.querySelector('[data-preview-to]'),
        date: content.querySelector('[data-preview-date]'),
        body: content.querySelector('[data-preview-body]'),
        attachment: content.querySelector('[data-preview-attachment]'),
        reply: content.querySelector('[data-preview-reply]'),
        forward: content.querySelector('[data-preview-forward]'),
        uid: content.querySelector('[data-preview-uid]'),
        deleteUid: content.querySelector('[data-preview-delete-uid]'),
        readAction: content.querySelector('[data-preview-read-action]'),
        readLabel: content.querySelector('[data-preview-read-label]')
      };

      function selectEmail(row) {
        const isSeen = row.dataset.seen === '1';

        rows.forEach((item) => item.classList.remove('active'));
        row.classList.add('active');
        emptyState.classList.add('d-none');
        content.classList.remove('d-none');

        fields.subject.textContent = row.dataset.subject || '(tanpa subject)';
        fields.status.textContent = isSeen ? 'Read' : 'Unread';
        fields.status.className = 'email-status-badge ' + (isSeen ? 'read' : 'unread');
        fields.from.textContent = (row.dataset.fromName || '-') + ' <' + (row.dataset.fromEmail || '-') + '>';
        fields.to.textContent = row.dataset.to || '-';
        fields.date.textContent = row.dataset.date || '-';
        fields.body.textContent = row.dataset.body || row.dataset.preview || '-';
        fields.reply.href = row.dataset.replyUrl || '#';
        fields.forward.href = row.dataset.forwardUrl || '#';
        fields.uid.value = row.dataset.uid || '';
        fields.deleteUid.value = row.dataset.uid || '';
        fields.readAction.value = isSeen ? 'unread' : 'read';
        fields.readLabel.textContent = isSeen ? 'Mark Unread' : 'Mark Read';
        fields.attachment.classList.toggle('d-none', row.dataset.hasAttachment !== '1');
      }

      rows.forEach((row) => row.addEventListener('click', () => selectEmail(row)));
      selectEmail(rows[0]);
    });
  </script>
@endif
@endsection
