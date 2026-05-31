@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="font-weight-bold mb-1">Tambah Inquiry & Quotation</h4>
            <p class="text-muted text-sm mb-0">Isi data inquiry dan quotation baru.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('paneladmin.inquiry-quotations.store') }}" enctype="multipart/form-data">
        @csrf
        @include('paneladmin.inquiry-quotations._form', ['entry' => null])
        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-2">
            <a href="{{ route('paneladmin.inquiry-quotations.index') }}" class="btn btn-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            <button type="submit" class="btn btn-primary mb-0"><i class="fas fa-save me-1"></i> Simpan Inquiry</button>
        </div>
    </form>
</div>
@endsection
