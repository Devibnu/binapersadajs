@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Edit Project Report</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.project-reports.update', $projectReport) }}" class="js-confirm-submit" enctype="multipart/form-data">
          @method('PUT')
          @include('paneladmin.project-reports._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
