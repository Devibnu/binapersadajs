@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Client</h6>
        @if(auth()->user()?->canAccess('clients.update'))
          <a href="{{ route('paneladmin.clients.edit', $client) }}" class="btn bg-gradient-primary mb-0">Edit Client</a>
        @endif
      </div>
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="border border-radius-lg bg-white p-4 text-center">
              @if($client->logo)
                <img src="{{ $client->logoUrl() }}" class="img-fluid" alt="Logo {{ $client->name }}" style="max-height: 140px;">
              @else
                <i class="fas fa-building fa-3x text-secondary"></i>
              @endif
            </div>
          </div>
          <div class="col-lg-8">
            <h4>{{ $client->name }}</h4>
            <p class="text-sm mb-1"><strong>Website:</strong>
              @if($client->website_url)
                <a href="{{ $client->website_url }}" target="_blank" rel="noopener">{{ $client->website_url }}</a>
              @else
                -
              @endif
            </p>
            <p class="text-sm mb-1"><strong>Urutan:</strong> {{ $client->sort_order }}</p>
            <p class="text-sm mb-3"><strong>Status:</strong>
              <span class="badge badge-sm {{ $client->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                {{ $client->is_active ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
