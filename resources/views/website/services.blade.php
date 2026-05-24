@extends('layouts.website')

@section('title', 'Services - PT. Bina Persada Jaya Sejahtera')

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Services';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'Industrial Services';
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
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
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
        <h2 class="section-title">Industrial Capabilities</h2>
        <h3 class="section-sub-title">Services We Provide</h3>
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
              <a class="learn-more d-inline-block" href="{{ route('services.show', $service->slug) }}" aria-label="learn-more-service"><i class="fa fa-caret-right"></i> Learn More</a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @elseif($showServiceFallback)
    @php
      $fallbackServices = [
        ['Mechanical Work', 'Installation, alignment, mechanical repair, and equipment support for industrial operations.', 'service1.jpg', 'fa-cogs'],
        ['Electrical Work', 'Electrical installation, maintenance, troubleshooting, and project site support.', 'service2.jpg', 'fa-bolt'],
        ['Fabrication', 'Steel structure, equipment support, custom fabrication, and workshop/site fabrication work.', 'service3.jpg', 'fa-drafting-compass'],
        ['Maintenance', 'Preventive maintenance, corrective maintenance, shutdown support, and plant reliability work.', 'service4.jpg', 'fa-tools'],
        ['Scaffolding', 'Safe access solutions and scaffolding manpower for maintenance and construction activities.', 'service5.jpg', 'fa-layer-group'],
        ['Manpower Supply', 'Skilled manpower support for project execution, maintenance, and industrial site operations.', 'service6.jpg', 'fa-users-cog'],
        ['Piping', 'Pipe fabrication, installation, modification, and maintenance for industrial systems.', 'service1.jpg', 'fa-project-diagram'],
        ['Civil Construction', 'Civil works, construction support, foundations, structures, and project infrastructure.', 'service2.jpg', 'fa-road'],
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
              <a class="learn-more d-inline-block" href="{{ route('services.index') }}" aria-label="learn-more-service"><i class="fa fa-caret-right"></i> Learn More</a>
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
