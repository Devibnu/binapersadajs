@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0"><h6>Tambah User Admin</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.users.store') }}" class="js-confirm-submit">
          @include('paneladmin.users._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
