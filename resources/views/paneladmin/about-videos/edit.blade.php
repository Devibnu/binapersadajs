@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Edit Video About</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.about-videos.update', $aboutVideo) }}" enctype="multipart/form-data" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.about-videos._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
