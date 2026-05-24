@extends('layouts.website')

@section('title', 'Constra - Blog')

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Blog';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'Blog';
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
      <div class="col-12">
        <h2 class="section-title">Company Updates</h2>
        <h3 class="section-sub-title">Latest Articles</h3>
      </div>
    </div>

    <div class="row">
      @foreach ($posts as $post)
        <div class="col-lg-4 col-md-6 mb-5">
          <div class="latest-post">
            <div class="latest-post-media">
              <a href="{{ route('website.blog.show', $post['slug']) }}" class="latest-post-img">
                <img loading="lazy" class="img-fluid" src="{{ asset($post['image']) }}" alt="{{ $post['title'] }}">
              </a>
            </div>
            <div class="post-body">
              <div class="post-meta">
                <span><i class="far fa-calendar"></i> {{ $post['date'] }}</span>
                <span class="post-comment"><i class="far fa-folder-open"></i> {{ $post['category'] }}</span>
              </div>
              <h4 class="post-title">
                <a href="{{ route('website.blog.show', $post['slug']) }}" class="d-inline-block">{{ $post['title'] }}</a>
              </h4>
              <p>{{ $post['excerpt'] }}</p>
              <div class="post-footer">
                <a class="btn btn-primary" href="{{ route('website.blog.show', $post['slug']) }}">Read More</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
