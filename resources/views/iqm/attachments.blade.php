@extends('layouts.iqm')

@section('title', 'Attachment')

@section('content')
<div class="container iqm-container py-4">
  <div class="mb-4">
    <h3 class="fw-bold mb-1">Attachment</h3>
    <p class="text-secondary mb-0">File pendukung inquiry dan quotation yang dapat Anda akses.</p>
  </div>
  <div class="row g-3">
    @forelse($attachments as $attachment)
      <div class="col-md-6 col-lg-4">
        <div class="card iqm-card h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              <span class="d-inline-flex align-items-center justify-content-center bg-light rounded-3" style="width:44px;height:44px">
                <i class="fas {{ $attachment->isPdf() ? 'fa-file-pdf text-danger' : ($attachment->isImage() ? 'fa-file-image text-primary' : 'fa-file text-secondary') }}"></i>
              </span>
              <div class="min-w-0">
                <h6 class="mb-1 text-truncate">{{ $attachment->original_name }}</h6>
                <p class="text-secondary small mb-0">{{ $attachment->formattedSize() }}</p>
              </div>
            </div>
            <p class="small text-secondary mb-3">{{ $attachment->inquiryQuotation?->inquiry_number }} - {{ Str::limit($attachment->inquiryQuotation?->subject, 44) }}</p>
            <div class="d-flex gap-2">
              <a href="{{ $attachment->fileUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">Preview</a>
              <a href="{{ $attachment->fileUrl() }}" download class="btn btn-sm btn-primary flex-fill">Download</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="card iqm-card"><div class="card-body text-center text-secondary py-4">Belum ada attachment.</div></div></div>
    @endforelse
  </div>
  <div class="mt-3">{{ $attachments->links() }}</div>
</div>
@endsection
