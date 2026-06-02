@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Trading Product</h6>
        @if(auth()->user()?->canAccess('trading-products.update'))
          <a href="{{ route('paneladmin.trading-products.edit', $tradingProduct) }}" class="btn bg-gradient-primary mb-0">Edit Produk</a>
        @endif
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <img src="{{ $tradingProduct->imageUrl() }}" alt="{{ $tradingProduct->name }}" class="img-fluid border-radius-lg">
          </div>
          <div class="col-lg-8">
            <h4>{{ $tradingProduct->name }}</h4>
            <p class="text-sm mb-1"><strong>Slug:</strong> {{ $tradingProduct->slug }}</p>
            <p class="text-sm mb-1"><strong>Kategori:</strong> {{ $tradingProduct->category }}</p>
            <p class="text-sm mb-1"><strong>Urutan:</strong> {{ $tradingProduct->sort_order }}</p>
            <p class="text-sm mb-3"><strong>Status:</strong>
              <span class="badge badge-sm {{ $tradingProduct->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                {{ $tradingProduct->is_active ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </p>
            @if($tradingProduct->short_description)
              <p class="text-sm">{{ $tradingProduct->short_description }}</p>
            @endif
            @if($tradingProduct->description)
              <h6>Deskripsi</h6>
              <p class="text-sm">{!! nl2br(e($tradingProduct->description)) !!}</p>
            @endif
            @if($tradingProduct->specifications)
              <h6>Spesifikasi</h6>
              <p class="text-sm">{!! nl2br(e($tradingProduct->specifications)) !!}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
