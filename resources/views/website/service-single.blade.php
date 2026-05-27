@extends('layouts.website')

@php
  $serviceSeoSetting = \App\Models\SeoSetting::current();
  $serviceCanonical = $serviceSeoSetting->canonicalUrl(route('services.show', $service->slug));
@endphp

@section('title', $service->title . ' - PT. Bina Persada Jaya Sejahtera')
@if($service->short_content || $service->short_description || $service->description)
  @section('meta_description', $service->short_content ?: $service->short_description ?: strip_tags($service->description))
@endif
@section('og_image', $service->imageUrl())

@push('schema')
  @include('website.partials.breadcrumb-schema', ['items' => [
    ['name' => 'Beranda', 'url' => $serviceSeoSetting->canonicalUrl(route('website.home'))],
    ['name' => 'Layanan', 'url' => $serviceSeoSetting->canonicalUrl(route('services.index'))],
    ['name' => $service->title, 'url' => $serviceCanonical],
  ]])
@endpush

@push('styles')
<style>
  .service-rich-content img {
    height: auto;
    max-width: 100%;
  }

  .service-rich-content table {
    border-collapse: collapse;
    margin-bottom: 24px;
    width: 100%;
  }

  .service-rich-content td,
  .service-rich-content th {
    border: 1px solid #e5e5e5;
    padding: 10px 12px;
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
  $summary = $service->short_content ?: $service->short_description;
  $detailParagraphs = array_values(array_filter(preg_split('/\R{2,}/', trim($service->content ?? ''))));
  $galleryImages = $service->galleryImages();
  $features = $service->features();
  $faqs = $service->faqs();
  $legacyCtaTexts = ['interested with this service?', 'interested with this service'];
  $legacyCtaButtons = ['get a quote', 'get quote'];
  $ctaText = ! $service->cta_text || in_array(strtolower(trim($service->cta_text)), $legacyCtaTexts, true)
    ? 'Butuh dukungan layanan untuk proyek industri Anda?'
    : $service->cta_text;
  $ctaButtonText = ! $service->cta_button_text || in_array(strtolower(trim($service->cta_button_text)), $legacyCtaButtons, true)
    ? 'Hubungi Kami'
    : $service->cta_button_text;
@endphp
<div id="banner-area" class="banner-area page-hero-managed" style="background-image:url({{ $pageHeroBackground }}); --page-hero-overlay: {{ $pageHeroOpacity }};">
  <div class="banner-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="banner-heading {{ $pageHeroTextClass }}">
            <h1 class="banner-title">{{ $service->title }}</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb {{ $pageHeroBreadcrumbClass }}">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Layanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $service->title }}</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section id="main-container" class="main-container">
  <div class="container">
    <div class="row">
      <div class="col-xl-3 col-lg-4">
        <div class="sidebar sidebar-left">
          <div class="widget">
            <h3 class="widget-title">Layanan Kami</h3>
            <ul class="nav service-menu">
              @foreach($relatedServices as $relatedService)
                <li class="{{ $relatedService->is($service) ? 'active' : '' }}">
                  <a href="{{ route('services.show', $relatedService->slug) }}">{{ $relatedService->title }}</a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      <div class="col-xl-8 col-lg-8">
        <div class="content-inner-page">
          <h2 class="column-title mrt-0">{{ $service->title }}</h2>

          <div class="row">
            <div class="col-md-12">
              <img loading="lazy" class="img-fluid mb-4" src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
              @if($summary)
                <p class="lead">{{ $summary }}</p>
              @endif
              @if($service->description)
                <div class="service-rich-content">
                  {!! $service->description !!}
                </div>
              @endif
              @foreach($detailParagraphs as $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
              @endforeach
            </div>
          </div>

          @if($galleryImages)
            <div class="gap-40"></div>
            <div id="page-slider" class="page-slider">
              @foreach($galleryImages as $galleryImage)
                <div class="item">
                  <img loading="lazy" class="img-fluid" src="{{ $galleryImage }}" alt="Galeri {{ $service->title }}">
                </div>
              @endforeach
            </div>
          @endif

          @if($features || $faqs)
            <div class="gap-40"></div>
            <div class="row">
              @if($features)
                <div class="col-md-6">
                  <h3 class="column-title-small">Keunggulan Layanan</h3>
                  <ul class="list-arrow">
                    @foreach($features as $feature)
                      <li>{{ $feature }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              @if($faqs)
                <div class="col-md-6 {{ $features ? 'mt-5 mt-md-0' : '' }}">
                  <h3 class="column-title-small">Informasi Penting</h3>
                  <div class="accordion accordion-group accordion-classic" id="service-faq-accordion">
                    @foreach($faqs as $index => $faq)
                      <div class="card">
                        <div class="card-header p-0 bg-transparent" id="service-faq-heading-{{ $index }}">
                          <h2 class="mb-0">
                            <button class="btn btn-block text-left {{ $index ? 'collapsed' : '' }}" type="button" data-toggle="collapse"
                              data-target="#service-faq-collapse-{{ $index }}" aria-expanded="{{ $index ? 'false' : 'true' }}" aria-controls="service-faq-collapse-{{ $index }}">
                              {{ $faq['question'] }}
                            </button>
                          </h2>
                        </div>
                        <div id="service-faq-collapse-{{ $index }}" class="collapse {{ $index ? '' : 'show' }}"
                          aria-labelledby="service-faq-heading-{{ $index }}" data-parent="#service-faq-accordion">
                          <div class="card-body">{{ $faq['answer'] }}</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif
            </div>
          @endif

          <div class="gap-40"></div>
          <div class="call-to-action classic">
            <div class="row align-items-center">
              <div class="col-md-8 text-center text-md-left">
                <div class="call-to-action-text">
                  <h3 class="action-title">{{ $ctaText }}</h3>
                </div>
              </div>
              <div class="col-md-4 text-center text-md-right mt-3 mt-md-0">
                <div class="call-to-action-btn">
                  <a class="btn btn-primary" href="{{ $service->cta_button_link ?: route('website.contact') }}">{{ $ctaButtonText }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
