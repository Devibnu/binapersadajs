@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Edit User Portal IQM</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.iqm-users.update', $user) }}">
          @method('PUT')
          @include('paneladmin.iqm-users._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
