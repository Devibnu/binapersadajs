@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="font-weight-bold mb-1">Edit Inquiry & Quotation</h4>
            <p class="text-muted text-sm mb-0">Perbarui data inquiry dan quotation.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('paneladmin.inquiry-quotations.update', $inquiryQuotation) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('paneladmin.inquiry-quotations._form', ['entry' => $inquiryQuotation])
        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-2">
            <a href="{{ route('paneladmin.inquiry-quotations.show', $inquiryQuotation) }}" class="btn btn-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            <button type="submit" class="btn btn-primary mb-0"><i class="fas fa-save me-1"></i> Perbarui Inquiry</button>
        </div>
    </form>
</div>
@endsection
