@extends('layouts.iqm')

@section('title', 'Detail Inquiry')

@section('content')
<div class="container iqm-container py-4">
  <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
    <div><h3 class="fw-bold mb-1">{{ $entry->inquiry_number }}</h3><p class="text-secondary mb-0">{{ $entry->subject }}</p></div>
    <a href="{{ route('iqm.inquiries.index') }}" class="btn btn-outline-secondary">Kembali</a>
  </div>
  <div class="row g-4">
    <div class="col-lg-6"><div class="card iqm-card h-100"><div class="card-body"><h5 class="fw-bold mb-3">Data Inquiry</h5><p><strong>Tanggal:</strong> {{ optional($entry->inquiry_date)->format('d/m/Y') }}</p><p><strong>Inquiry By:</strong> {{ $entry->inquiryByLabel() }}</p><p><strong>Subject:</strong> {{ $entry->subject }}</p><p><strong>Deskripsi:</strong> {{ $entry->description ?: '-' }}</p></div></div></div>
    <div class="col-lg-6"><div class="card iqm-card h-100"><div class="card-body"><h5 class="fw-bold mb-3">Data Client</h5>@if($entry->client_logo)<img src="{{ $entry->clientLogoUrl() }}" class="img-fluid rounded shadow-sm mb-3" style="max-height:120px;object-fit:contain" alt="Logo {{ $entry->client_name }}">@else<p class="text-secondary">Logo belum diupload</p>@endif<p><strong>Client:</strong> {{ $entry->client_name }}</p><p><strong>PIC:</strong> {{ $entry->client_pic ?: '-' }}</p><p><strong>Email:</strong> {{ $entry->client_email ?: '-' }}</p><p><strong>Telepon:</strong> {{ $entry->client_phone ?: '-' }}</p><p><strong>Alamat:</strong> {{ $entry->client_address ?: '-' }}</p></div></div></div>
    <div class="col-lg-6"><div class="card iqm-card h-100"><div class="card-body"><h5 class="fw-bold mb-3">Site Survey</h5><p><strong>Status:</strong> <span class="badge iqm-pill {{ $entry->surveyStatusBadgeClass() }}">{{ $entry->surveyStatusLabel() }}</span></p><p><strong>Tanggal:</strong> {{ optional($entry->site_survey_date)->format('d/m/Y') ?: '-' }}</p><p><strong>Catatan:</strong> {{ $entry->site_survey_notes ?: '-' }}</p></div></div></div>
    <div class="col-lg-6"><div class="card iqm-card h-100"><div class="card-body"><h5 class="fw-bold mb-3">Quotation</h5><p><strong>Nomor:</strong> {{ $entry->quotation_number ?: '-' }}</p><p><strong>Tanggal:</strong> {{ optional($entry->quotation_date)->format('d/m/Y') ?: '-' }}</p><p><strong>Deadline:</strong> {{ optional($entry->deadline)->format('d/m/Y') ?: '-' }}</p><p><strong>Amount:</strong> {{ $entry->formattedAmount() }}</p><p><strong>Status:</strong> <span class="badge iqm-pill {{ $entry->quotationStatusBadgeClass() }}">{{ $entry->quotationStatusLabel() }}</span></p></div></div></div>
    <div class="col-12"><div class="card iqm-card"><div class="card-body"><h5 class="fw-bold mb-3">Catatan</h5><p class="mb-0 text-secondary">{{ $entry->notes ?: '-' }}</p></div></div></div>
    <div class="col-12"><div class="card iqm-card"><div class="card-body"><h5 class="fw-bold mb-3">Attachment</h5><div class="row g-3">
      @forelse($entry->attachments as $attachment)
        <div class="col-md-4"><div class="border rounded-4 p-3 h-100 bg-light">
          @if($attachment->isImage())<img src="{{ $attachment->fileUrl() }}" class="img-fluid rounded mb-3" style="height:150px;width:100%;object-fit:cover" alt="{{ $attachment->original_name }}">
          @elseif($attachment->isPdf())<div class="d-flex align-items-center justify-content-center bg-white rounded mb-3" style="height:150px"><i class="fas fa-file-pdf fa-3x text-danger"></i></div>
          @else<div class="d-flex align-items-center justify-content-center bg-white rounded mb-3" style="height:150px"><i class="fas fa-file fa-3x text-secondary"></i></div>@endif
          <h6 class="fw-bold small">{{ $attachment->original_name }}</h6>
          <div class="d-flex gap-2"><a href="{{ $attachment->fileUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">Preview</a><a href="{{ $attachment->fileUrl() }}" download class="btn btn-sm btn-primary flex-fill">Download</a></div>
        </div></div>
      @empty
        <p class="text-secondary mb-0">Belum ada attachment.</p>
      @endforelse
    </div></div></div></div>
  </div>
</div>
@endsection
