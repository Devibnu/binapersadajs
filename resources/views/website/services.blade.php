@extends('layouts.website')

@section('title', 'Layanan - PT. Bina Persada Jaya Sejahtera')

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Layanan';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'Layanan Industri';
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
@endphp
<div id="banner-area" class="banner-area page-hero-managed" style="background-image:url({{ $pageHeroBackground }}); --page-hero-overlay: {{ $pageHeroOpacity }};">
  <div class="banner-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="banner-heading {{ $pageHeroTextClass }}">
            <h1 class="banner-title">{{ $pageHeroTitle }}</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb {{ $pageHeroBreadcrumbClass }}">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageHeroBreadcrumb }}</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section id="main-container" class="main-container pb-2 industrial-services-page">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <h2 class="section-title">Kapabilitas Industri</h2>
        <h3 class="section-sub-title">Layanan Kami</h3>
      </div>
    </div>

    @if($services->isNotEmpty())
    <div class="row">
      @foreach($services as $service)
      <div class="col-lg-4 col-md-6 mb-5">
        <div class="ts-service-box industrial-service-tile">
          <div class="ts-service-image-wrapper">
            <a href="{{ route('services.show', $service->slug) }}">
              <img loading="lazy" class="w-100" src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
            </a>
          </div>
          <div class="d-flex">
            <div class="industrial-service-icon small">
              @if($service->iconUrl())
                <img src="{{ $service->iconUrl() }}" alt="" style="max-width: 32px; max-height: 32px; object-fit: contain;">
              @else
                <i class="fas {{ $service->iconClass() }}"></i>
              @endif
            </div>
            <div class="ts-service-info">
              <h3 class="service-box-title"><a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a></h3>
              <p>{{ \Illuminate\Support\Str::limit($service->short_description ?? '', 120) }}</p>
              <a class="learn-more d-inline-block" href="{{ route('services.show', $service->slug) }}" aria-label="lihat-detail-layanan"><i class="fa fa-caret-right"></i> Selengkapnya</a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @elseif($showServiceFallback)
    @php
      $fallbackServices = [
        ['Mechanical Work', 'Instalasi, alignment, perbaikan mekanikal, dan dukungan peralatan untuk operasi industri.', 'service1.jpg', 'fa-cogs'],
        ['Electrical Work', 'Instalasi, pemeliharaan, dan penanganan masalah kelistrikan di area proyek.', 'service2.jpg', 'fa-bolt'],
        ['Fabrication', 'Fabrikasi struktur baja dan peralatan pendukung di workshop maupun lokasi kerja.', 'service3.jpg', 'fa-drafting-compass'],
        ['Maintenance', 'Pemeliharaan preventif dan korektif untuk menjaga keandalan fasilitas industri.', 'service4.jpg', 'fa-tools'],
        ['Scaffolding', 'Dukungan akses kerja aman untuk aktivitas pemeliharaan dan konstruksi.', 'service5.jpg', 'fa-layer-group'],
        ['Manpower Supply', 'Tenaga kerja terampil untuk kebutuhan proyek, shutdown, dan operasi lapangan.', 'service6.jpg', 'fa-users-cog'],
        ['Piping', 'Fabrikasi, instalasi, modifikasi, dan pemeliharaan sistem perpipaan industri.', 'service1.jpg', 'fa-project-diagram'],
        ['Civil Construction', 'Pekerjaan sipil, konstruksi, dan infrastruktur pendukung lokasi proyek.', 'service2.jpg', 'fa-road'],
      ];
    @endphp

    <div class="row">
      @foreach($fallbackServices as $service)
      <div class="col-lg-4 col-md-6 mb-5">
        <div class="ts-service-box industrial-service-tile">
          <div class="ts-service-image-wrapper">
            <img loading="lazy" class="w-100" src="{{ asset('web/images/services/' . $service[2]) }}" alt="{{ $service[0] }}">
          </div>
          <div class="d-flex">
            <div class="industrial-service-icon small">
              <i class="fas {{ $service[3] }}"></i>
            </div>
            <div class="ts-service-info">
              <h3 class="service-box-title"><a href="{{ route('website.service-single') }}">{{ $service[0] }}</a></h3>
              <p>{{ $service[1] }}</p>
              <a class="learn-more d-inline-block" href="{{ route('services.index') }}" aria-label="lihat-layanan"><i class="fa fa-caret-right"></i> Selengkapnya</a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</section>
@endsection
