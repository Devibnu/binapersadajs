@extends('layouts.user_type.auth')

@section('content')
@php
  $selectedAccountId = old('email_account_id', $draft?->email_account_id ?: request('account_id'));
  $bodyValue = old('body', $draft?->body ?: ($prefill['body'] ?? ''));
@endphp

<style>
  .email-editor {
    min-height: 280px;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    padding: 14px;
    background: #fff;
    outline: none;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>{{ $draft ? 'Edit Draft' : 'Tulis Email' }}</h6>
          <p class="text-sm mb-0">Kirim email perusahaan melalui Email Center.</p>
        </div>
        <a href="{{ route('paneladmin.email-center.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Kembali</a>
      </div>
      <div class="card-body">
        @if(session('error'))<div class="alert alert-danger text-white">{{ session('error') }}</div>@endif
        <form method="POST" enctype="multipart/form-data" id="emailComposeForm">
          @csrf
          @if($draft)<input type="hidden" name="draft_id" value="{{ $draft->id }}">@endif
          <input type="hidden" name="action_type" value="{{ old('action_type', $draft ? 'send' : ($prefill['action_type'] ?? 'send')) }}">
          <textarea name="body" id="emailBody" class="d-none">{{ $bodyValue }}</textarea>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>From</label>
                <select name="email_account_id" class="form-control @error('email_account_id') is-invalid @enderror" required>
                  <option value="">Pilih akun</option>
                  @foreach($accounts as $account)
                    <option value="{{ $account->id }}" @selected((int) $selectedAccountId === $account->id)>{{ $account->name }} - {{ $account->email }}</option>
                  @endforeach
                </select>
                @error('email_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>To</label>
                <input type="text" name="to_email" class="form-control @error('to_email') is-invalid @enderror" value="{{ old('to_email', $draft?->to_email ?: ($prefill['to_email'] ?? '')) }}" placeholder="email@domain.com, email2@domain.com">
                @error('to_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>CC</label>
                <input type="text" name="cc" class="form-control" value="{{ old('cc', $draft?->cc) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>BCC</label>
                <input type="text" name="bcc" class="form-control" value="{{ old('bcc', $draft?->bcc) }}">
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $draft?->subject ?: ($prefill['subject'] ?? '')) }}">
                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-12">
              <label>Message</label>
              <div class="email-editor mb-2" id="emailEditor" contenteditable="true">{!! $bodyValue !!}</div>
              @error('body')<div class="text-danger text-xs">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <div class="form-group mt-3">
                <label>Attachment</label>
                <input type="file" name="attachments[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.jpeg,.png">
                <small class="text-secondary">PDF, DOC, XLS, ZIP, JPG, PNG. Maksimal 10MB/file.</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mt-3">
                <label>Template</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="use_template" value="1" id="useTemplate" @checked(old('use_template', $draft?->use_template ?? true))>
                  <label class="form-check-label" for="useTemplate">Gunakan Template Email</label>
                </div>
                <small class="text-secondary">Matikan untuk mengirim email polos.</small>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" formaction="{{ route('paneladmin.email-center.drafts.store') }}" class="btn btn-outline-primary mb-0">Simpan Draft</button>
            <button type="submit" formaction="{{ route('paneladmin.email-center.send') }}" class="btn bg-gradient-primary mb-0">Kirim Email</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('dashboard')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('emailComposeForm');
    const editor = document.getElementById('emailEditor');
    const body = document.getElementById('emailBody');
    if (form && editor && body) {
      form.addEventListener('submit', function () {
        body.value = editor.innerHTML;
      });
    }
  });
</script>
@endpush
