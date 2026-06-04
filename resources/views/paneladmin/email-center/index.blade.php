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
  .email-folder-link { border-radius: 10px; color: #344767; display: flex; gap: 10px; padding: 10px 12px; }
  .email-folder-link.active, .email-folder-link:hover { background: #eef5ff; color: #2152ff; }
  .email-list-item { border-bottom: 1px solid #eef0f4; padding: 14px 16px; }
  .email-list-item.unread { background: #f8fbff; }
  .email-preview-pane { min-height: 420px; }
  @media (max-width: 991px) { .email-preview-pane { min-height: auto; } }
</style>

<div class="row email-center-shell">
  <div class="col-lg-3">
    <div class="card mb-4">
      <div class="card-body">
        <a href="{{ route('paneladmin.email-center.compose') }}" class="btn bg-gradient-primary w-100 mb-3">+ Tulis Email</a>
        <a href="{{ route('paneladmin.email-center.accounts') }}" class="btn btn-outline-secondary w-100 mb-3">Email Accounts</a>

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

  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h6 class="mb-1">{{ $folders[$folder]['label'] ?? 'Email Center' }}</h6>
            <p class="text-sm mb-0">{{ $account?->email ?: 'Pilih email account' }}</p>
          </div>
        </div>
        <form method="GET" action="{{ route('paneladmin.email-center.index') }}" class="row g-2 mt-3">
          <input type="hidden" name="folder" value="{{ $folder }}">
          <input type="hidden" name="account_id" value="{{ $account?->id }}">
          <div class="col">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari pengirim, subject, isi email...">
          </div>
          <div class="col-auto">
            <button class="btn bg-gradient-primary mb-0" type="submit">Cari</button>
          </div>
        </form>
      </div>
      <div class="card-body p-0">
        @if(session('success'))<div class="alert alert-success text-white m-3">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger text-white m-3">{{ session('error') }}</div>@endif
        @if($imapNotice)<div class="alert alert-warning text-white m-3">{{ $imapNotice }}</div>@endif

        @if($folder === 'inbox')
          @forelse($messages as $message)
            <div class="email-list-item {{ $message->seen ? '' : 'unread' }}">
              <div class="d-flex justify-content-between gap-2">
                <strong class="text-sm">{{ $message->from }}</strong>
                <span class="text-xs text-secondary">{{ $message->date }}</span>
              </div>
              <p class="text-sm font-weight-bold mb-1">{{ $message->subject }}</p>
              <p class="text-xs text-secondary mb-2">{{ $message->preview ?: '-' }}</p>
              <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-link text-info text-xs p-0 mb-0" href="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'to' => $message->from, 'subject' => 'Re: ' . $message->subject, 'action_type' => 'reply']) }}">Reply</a>
                <a class="btn btn-link text-secondary text-xs p-0 mb-0" href="{{ route('paneladmin.email-center.compose', ['account_id' => $account?->id, 'subject' => 'Fwd: ' . $message->subject, 'body' => $message->preview, 'action_type' => 'forward']) }}">Forward</a>
                @foreach(['read' => 'Mark Read', 'unread' => 'Mark Unread', 'delete' => 'Delete'] as $action => $label)
                  <form method="POST" action="{{ route('paneladmin.email-center.imap-action') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="account_id" value="{{ $account?->id }}">
                    <input type="hidden" name="uid" value="{{ $message->uid }}">
                    <input type="hidden" name="action" value="{{ $action }}">
                    <button class="btn btn-link text-xs p-0 mb-0 {{ $action === 'delete' ? 'text-danger' : 'text-secondary' }}" type="submit">{{ $label }}</button>
                  </form>
                @endforeach
              </div>
            </div>
          @empty
            <div class="p-4 text-center text-sm text-secondary">Belum ada email untuk ditampilkan.</div>
          @endforelse
        @else
          @forelse($messages as $message)
            <a class="d-block email-list-item text-decoration-none" href="{{ route('paneladmin.email-center.index', ['folder' => $folder, 'account_id' => $account?->id, 'q' => $search, 'message' => $message->id]) }}">
              <div class="d-flex justify-content-between gap-2">
                <strong class="text-sm text-dark">{{ $folder === 'sent' ? $message->to_email : ($message->subject ?: '(tanpa subject)') }}</strong>
                <span class="text-xs text-secondary">{{ ($message->sent_at ?? $message->updated_at)->format('d/m/Y H:i') }}</span>
              </div>
              <p class="text-sm font-weight-bold text-dark mb-1">{{ $message->subject ?: '(tanpa subject)' }}</p>
              <p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($message->body ?: '-'), 120) }}</p>
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

  <div class="col-lg-4">
    <div class="card mb-4 email-preview-pane">
      <div class="card-header pb-0">
        <h6>Preview Email</h6>
        <p class="text-sm mb-0">Pilih email pada daftar untuk melihat detail.</p>
      </div>
      <div class="card-body">
        @if($selectedMessage)
          <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Subject</p>
          <h6>{{ $selectedMessage->subject ?: '(tanpa subject)' }}</h6>
          <p class="text-xs text-secondary mb-2">From: {{ $selectedMessage->from_email ?: '-' }}</p>
          <p class="text-xs text-secondary mb-3">To: {{ $selectedMessage->to_email ?: '-' }}</p>
          <div class="bg-gray-100 border-radius-lg p-3 text-sm mb-3" style="white-space: pre-wrap;">{!! nl2br(e($selectedMessage->body ?: '-')) !!}</div>
          @if($selectedMessage->attachments->isNotEmpty())
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2">Attachment</p>
            @foreach($selectedMessage->attachments as $attachment)
              <span class="badge bg-gradient-secondary me-1">{{ $attachment->original_name }}</span>
            @endforeach
          @endif
          <div class="d-flex flex-wrap gap-2 mt-4">
            @if($selectedMessage->folder === 'draft')
              <a href="{{ route('paneladmin.email-center.drafts.edit', $selectedMessage) }}" class="btn bg-gradient-info mb-0">Edit Draft</a>
            @endif
            @if($selectedMessage->folder !== 'trash')
              <form method="POST" action="{{ route('paneladmin.email-center.messages.delete', $selectedMessage) }}" class="js-confirm-submit">
                @csrf
                @method('PATCH')
                <button class="btn bg-gradient-danger mb-0" type="submit">Delete</button>
              </form>
            @else
              <form method="POST" action="{{ route('paneladmin.email-center.messages.restore', $selectedMessage) }}" class="js-confirm-submit">
                @csrf
                @method('PATCH')
                <button class="btn bg-gradient-success mb-0" type="submit">Restore</button>
              </form>
              <form method="POST" action="{{ route('paneladmin.email-center.messages.force-delete', $selectedMessage) }}" class="js-confirm-delete">
                @csrf
                @method('DELETE')
                <button class="btn bg-gradient-danger mb-0" type="submit">Delete Permanently</button>
              </form>
            @endif
          </div>
        @else
          <div class="text-center py-5 text-secondary text-sm">Tidak ada email yang dipilih.</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
