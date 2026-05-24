@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Edit Hero Banner</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.hero-banners.update', $heroBanner) }}" enctype="multipart/form-data" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.hero-banners._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
