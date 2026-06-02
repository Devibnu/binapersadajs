@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Edit Invoice Report</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.invoice-reports.update', $invoiceReport) }}" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.invoice-reports._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
