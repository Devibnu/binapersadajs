@extends('layouts.website')

@section('title', $product->name . ' - Trading Product')
@section('meta_description', $product->short_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 150))

@push('styles')
<style>
  .trading-detail-image {
    background: #f6f8fa;
    border: 1px solid #edf1f4;
    overflow: hidden;
  }

  .trading-detail-image img {
    display: block;
    height: auto;
    width: 100%;
  }

  .trading-detail-category {
    color: #1f8f5f;
    display: inline-block;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 12px;
    text-transform: uppercase;
  }

  .trading-detail-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 24px;
  }

  .trading-spec-box {
    background: #f7f9fb;
    border-left: 4px solid #1f8f5f;
    padding: 20px;
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $product->name;
  $pageHeroBreadcrumb = 'Trading Product';
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
  $whatsappValue = trim((string) ($websiteSetting?->whatsapp ?? $websiteSetting?->telepon ?? ''));
  $whatsappNumber = preg_replace('/\D+/', '', $whatsappValue);
  if (str_starts_with($whatsappNumber, '0')) {
      $whatsappNumber = '62' . substr($whatsappNumber, 1);
  } elseif (str_starts_with($whatsappNumber, '8')) {
      $whatsappNumber = '62' . $whatsappNumber;
  }
  $whatsappMessage = 'Halo PT. Bina Persada Jaya Sejahtera, saya ingin bertanya tentang produk trading: ' . $product->name;
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
                <li class="breadcrumb-item"><a href="{{ route('website.trading.index') }}">Trading</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageHeroBreadcrumb }}</li>
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
      <div class="col-lg-6 mb-5 mb-lg-0">
        <div class="trading-detail-image">
          <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="900" height="650" loading="lazy" decoding="async">
        </div>
      </div>
      <div class="col-lg-6">
        <span class="trading-detail-category">{{ $product->category }}</span>
        <h2 class="column-title">{{ $product->name }}</h2>
        @if($product->short_description)
          <p class="lead">{{ $product->short_description }}</p>
        @endif
        @if($product->description)
          <div>{!! nl2br(e($product->description)) !!}</div>
        @endif
        <div class="trading-detail-actions">
          <a href="{{ route('website.contact') }}" class="btn btn-primary">Hubungi Kami</a>
          @if($whatsappNumber)
            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" target="_blank" rel="noopener" class="btn btn-success">
              <i class="fab fa-whatsapp mr-1" aria-hidden="true"></i> WhatsApp
            </a>
          @endif
        </div>
      </div>
    </div>

    @if($product->specifications)
      <div class="row mt-5">
        <div class="col-lg-12">
          <h3 class="column-title">Spesifikasi</h3>
          <div class="trading-spec-box">{!! nl2br(e($product->specifications)) !!}</div>
        </div>
      </div>
    @endif

    @if($relatedProducts->isNotEmpty())
      <div class="row mt-5">
        <div class="col-lg-12">
          <h3 class="column-title">Produk Terkait</h3>
        </div>
        @foreach($relatedProducts as $relatedProduct)
          <div class="col-md-4 mb-4">
            <a href="{{ route('website.trading.show', $relatedProduct->slug) }}">
              <img src="{{ $relatedProduct->imageUrl() }}" alt="{{ $relatedProduct->name }}" class="img-fluid mb-3" loading="lazy" decoding="async">
              <h4 class="mb-1">{{ $relatedProduct->name }}</h4>
            </a>
            <span class="text-muted">{{ $relatedProduct->category }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection
