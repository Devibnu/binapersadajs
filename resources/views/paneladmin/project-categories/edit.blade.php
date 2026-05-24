@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Edit Project Category</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.project-categories.update', $projectCategory) }}" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.project-categories._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
