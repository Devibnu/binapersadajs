@extends('layouts.website')

@section('title', 'Blog - PT. Bina Persada Jaya Sejahtera')

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

<section id="main-container" class="main-container">
  <div class="container">
    <div class="row">
      <aside class="col-lg-4 order-1 order-lg-0 mb-5 mb-lg-0">
        @include('website.blog._sidebar')
      </aside>

      <div class="col-lg-8 order-0 order-lg-1 mb-5 mb-lg-0">
        @forelse($posts as $post)
          <div class="post">
            <div class="post-media post-image">
              <a href="{{ route('website.blog.show', $post->slug) }}">
                <img loading="lazy" decoding="async" src="{{ $post->featuredImageUrl() }}" class="img-fluid" alt="{{ $post->title }}" width="750" height="450">
              </a>
            </div>
            <div class="post-body">
              <div class="entry-header">
                <div class="post-meta">
                  <span class="post-author"><i class="far fa-user"></i> {{ $post->displayAuthor() }}</span>
                  <span class="post-cat"><i class="far fa-folder-open"></i> {{ $post->category }}</span>
                  <span class="post-meta-date"><i class="far fa-calendar"></i> {{ $post->displayDate() }}</span>
                </div>
                <h2 class="entry-title">
                  <a href="{{ route('website.blog.show', $post->slug) }}">{{ $post->title }}</a>
                </h2>
              </div>
              <div class="entry-content">
                <p>{{ $post->excerpt }}</p>
              </div>
              <div class="post-footer">
                <a href="{{ route('website.blog.show', $post->slug) }}" class="btn btn-primary">Lanjutkan Membaca</a>
              </div>
            </div>
          </div>
        @empty
          <div class="post">
            <div class="post-body">
              <p class="mb-0">Belum ada artikel yang tersedia.</p>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</section>
@endsection
