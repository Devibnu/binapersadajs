@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Edit Role: {{ $role->name }}</h6>
        @if($role->is_super_admin)
          <p class="text-sm text-secondary mb-0">Role Super Admin selalu aktif dan memiliki seluruh akses.</p>
        @endif
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.roles.update', $role) }}" class="js-confirm-submit">
          @method('PUT')
          @include('paneladmin.roles._form')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
