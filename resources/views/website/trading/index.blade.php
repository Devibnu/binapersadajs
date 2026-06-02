@extends('layouts.website')

@section('title', 'Trading - PT. Bina Persada Jaya Sejahtera')

@push('styles')
<style>
  .trading-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-bottom: 36px;
  }

  .trading-filter a {
    background: #f2f5f7;
    border-radius: 4px;
    color: #0c1e35;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 16px;
    text-transform: uppercase;
  }

  .trading-filter a.active,
  .trading-filter a:hover {
    background: #1f8f5f;
    color: #fff;
    text-decoration: none;
  }

  .trading-card {
    background: #fff;
    border: 1px solid #edf1f4;
    height: 100%;
    transition: box-shadow .2s ease, transform .2s ease;
  }

  .trading-card:hover {
    box-shadow: 0 14px 36px rgba(12, 30, 53, .12);
    transform: translateY(-3px);
  }

  .trading-card-img {
    background: #f6f8fa;
    height: 245px;
    overflow: hidden;
  }

  .trading-card-img img {
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
  }

  .trading-card-body {
    padding: 20px;
  }

  .trading-category {
    color: #1f8f5f;
    display: block;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 8px;
    text-transform: uppercase;
  }

  .trading-title {
    font-size: 18px;
    line-height: 1.35;
    margin-bottom: 12px;
  }

  .trading-title a {
    color: #0c1e35;
  }

  @media (max-width: 575px) {
    .trading-filter {
      flex-wrap: nowrap;
      justify-content: flex-start;
      overflow-x: auto;
      padding-bottom: 10px;
      -webkit-overflow-scrolling: touch;
    }

    .trading-filter a {
      flex: 0 0 auto;
      white-space: nowrap;
    }

    .trading-card-img {
      height: 210px;
    }
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Trading';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'Product Catalog';
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

<section id="main-container" class="main-container">
  <div class="container">
    <div class="row text-center">
      <div class="col-lg-12">
        <h2 class="section-title">Katalog Produk</h2>
        <h3 class="section-sub-title">Trading Product</h3>
      </div>
    </div>

    <div class="trading-filter">
      <a class="{{ $activeCategory ? '' : 'active' }}" href="{{ route('website.trading.index') }}">Show All</a>
      @foreach($categories as $category)
        @php($categoryKey = \Illuminate\Support\Str::slug($category))
        <a class="{{ $activeCategory === $categoryKey ? 'active' : '' }}" href="{{ route('website.trading.index', ['category' => $categoryKey]) }}">{{ $category }}</a>
      @endforeach
    </div>

    <div class="row">
      @forelse($products as $product)
        <div class="col-lg-4 col-md-6 mb-4">
          <article class="trading-card">
            <div class="trading-card-img">
              <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="600" height="420" loading="lazy" decoding="async">
            </div>
            <div class="trading-card-body">
              <span class="trading-category">{{ $product->category }}</span>
              <h3 class="trading-title"><a href="{{ route('website.trading.show', $product->slug) }}">{{ $product->name }}</a></h3>
              @if($product->short_description)
                <p>{{ \Illuminate\Support\Str::limit($product->short_description, 110) }}</p>
              @endif
              <a href="{{ route('website.trading.show', $product->slug) }}" class="btn btn-primary btn-sm">Detail</a>
            </div>
          </article>
        </div>
      @empty
        <div class="col-12">
          <p class="text-center text-muted mb-0">Produk trading akan segera tersedia.</p>
        </div>
      @endforelse
    </div>
  </div>
</section>
@endsection
