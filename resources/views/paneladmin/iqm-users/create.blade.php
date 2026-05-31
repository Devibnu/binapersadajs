@extends('layouts.user_type.auth')

@section('content')
<div class="card">
  <div class="card-header pb-0"><h6>Tambah User Portal IQM</h6></div>
  <div class="card-body">
    <form method="POST" action="{{ route('paneladmin.iqm-users.store') }}">
      @include('paneladmin.iqm-users._form')
    </form>
  </div>
</div>
@endsection
