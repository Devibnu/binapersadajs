@extends('layouts.iqm')

@section('title', 'Inquiry & Quotation Management')

@section('content')
<section class="py-5">
  <div class="container py-lg-5">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <span class="badge text-bg-warning iqm-pill mb-3">Client Portal</span>
        <h1 class="display-5 fw-bold mb-3">Inquiry & Quotation Management (IQM)</h1>
        <p class="lead text-secondary mb-4">Portal Inquiry & Quotation Management untuk memantau status inquiry, site survey, quotation, dan progress proyek secara real-time.</p>
        <a href="{{ route('iqm.login') }}" class="btn btn-warning btn-lg px-4"><i class="fas fa-right-to-bracket me-2"></i> Login</a>
      </div>
      <div class="col-lg-5">
        <div class="card iqm-card p-4 text-center">
          <img src="{{ $websiteSetting?->logoUrl() ?? asset('web/images/logo.png') }}" class="img-fluid mx-auto mb-4" style="max-height:120px;object-fit:contain" alt="PT Bina Persada JS">
          <h5 class="fw-bold mb-2">PT Bina Persada JS</h5>
          <p class="text-secondary mb-0">Pantau inquiry, dokumen quotation, attachment, dan status pekerjaan dari satu portal aman.</p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
