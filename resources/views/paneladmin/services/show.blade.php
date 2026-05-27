@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Detail Layanan</h6>
          <p class="text-sm mb-0">Informasi layanan yang dikelola untuk website publik.</p>
        </div>
        <a href="{{ route('paneladmin.services.edit', $service) }}" class="btn bg-gradient-primary mb-0">Edit Layanan</a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-5 mb-4 mb-lg-0">
            <img src="{{ $service->imageUrl() }}" class="img-fluid border-radius-lg" alt="{{ $service->title }}">
          </div>
          <div class="col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-3">
              <h4 class="mb-0">{{ $service->title }}</h4>
              <span class="badge badge-sm {{ $service->isActive() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                {{ $service->isActive() ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <p class="text-sm mb-1"><strong>Slug:</strong> {{ $service->slug }}</p>
            <p class="text-sm mb-3"><strong>Urutan:</strong> {{ $service->sort_order }}</p>

            @if($service->short_content || $service->short_description)
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4 mb-2">Ringkasan</h6>
              <p class="text-sm">{{ $service->short_content ?: $service->short_description }}</p>
            @endif

            @if($service->description)
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4 mb-2">Deskripsi</h6>
              <div class="text-sm mb-0">{!! $service->description !!}</div>
            @endif

            @if($service->content)
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4 mb-2">Isi Detail Layanan</h6>
              <p class="text-sm mb-0">{!! nl2br(e($service->content)) !!}</p>
            @endif
          </div>
        </div>

        @if($service->features())
          <hr class="horizontal dark my-4">
          <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Keunggulan Layanan</h6>
          <ul class="text-sm mb-0">
            @foreach($service->features() as $feature)
              <li class="mb-2">{{ $feature }}</li>
            @endforeach
          </ul>
        @endif

        @if($service->faqs())
          <hr class="horizontal dark my-4">
          <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">FAQ</h6>
          <div class="row">
            @foreach($service->faqs() as $faq)
              <div class="col-lg-6 mb-3">
                <p class="text-sm font-weight-bold mb-1">{{ $faq['question'] }}</p>
                <p class="text-sm text-secondary mb-0">{{ $faq['answer'] }}</p>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
