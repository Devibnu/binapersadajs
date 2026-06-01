@extends('layouts.website')

@php
  $aboutSeoSetting = \App\Models\SeoSetting::current();
@endphp

@section('title', 'About - ' . ($websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera'))
@section('meta_description', $aboutPageSetting->section_description)
@push('styles')
<style>
  .about-video-grid {
    display: grid;
    gap: 22px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .about-video-section {
    padding-top: 0;
  }

  .about-video-grid.is-single {
    grid-template-columns: minmax(0, 700px);
    justify-content: center;
  }

  .about-video-card {
    background: #fff;
  }

  .about-video-frame {
    aspect-ratio: 16 / 9;
    background: #0c1e35;
    overflow: hidden;
    width: 100%;
  }

  .about-video-frame iframe {
    border: 0;
    display: block;
    height: 100%;
    width: 100%;
  }

  .about-video-title {
    color: #0c1e35;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.4;
    margin: 12px 0 0;
  }

  @media (max-width: 767px) {
    .about-video-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush
@push('schema')
  @include('website.partials.breadcrumb-schema', ['items' => [
    ['name' => 'Beranda', 'url' => $aboutSeoSetting->canonicalUrl(route('website.home'))],
    ['name' => $pageHero?->breadcrumb_text ?: 'Tentang Kami', 'url' => $aboutSeoSetting->canonicalUrl(route('website.about'))],
  ]])
@endpush

@section('content')
<!--/ Header end -->
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'About';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'About Us';
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
                      <li class="breadcrumb-item"><span>Company</span></li>
                      <li class="breadcrumb-item active" aria-current="page">{{ $pageHeroBreadcrumb }}</li>
                    </ol>
                </nav>
              </div>
          </div><!-- Col end -->
        </div><!-- Row end -->
    </div><!-- Container end -->
  </div><!-- Banner text end -->
</div><!-- Banner area end --> 

<section id="main-container" class="main-container">
  <div class="container">
    <div class="row">
        <div class="col-lg-6">
          <h3 class="column-title">{{ $aboutPageSetting->section_label }}</h3>
          @if($aboutPageSetting->section_title)
            <h4>{{ $aboutPageSetting->section_title }}</h4>
          @endif
          <p>{{ $aboutPageSetting->section_description }}</p>
          <blockquote><p>{{ $aboutPageSetting->quote_text }}</p></blockquote>
          <p>{{ $aboutPageSetting->section_description_bottom }}</p>

        </div><!-- Col end -->

        <div class="col-lg-6 mt-5 mt-lg-0">
          @php
            $aboutSlides = [
              [$aboutPageSetting->slider_1_title, $aboutPageSetting->imageUrl('slider_1_image')],
              [$aboutPageSetting->slider_2_title, $aboutPageSetting->imageUrl('slider_2_image')],
              [$aboutPageSetting->slider_3_title, $aboutPageSetting->imageUrl('slider_3_image')],
            ];
          @endphp
          <div id="page-slider" class="page-slider small-bg">
            @foreach($aboutSlides as $aboutSlide)
              <div class="item" style="background-image:url({{ $aboutSlide[1] }})">
                <div class="container">
                    <div class="box-slider-content">
                      <div class="box-slider-text">
                          <h2 class="box-slide-title">{{ $aboutSlide[0] }}</h2>
                      </div>
                    </div>
                </div>
              </div>
            @endforeach
          </div><!-- Page slider end-->
        </div><!-- Col end -->
    </div><!-- Content row end -->

  </div><!-- Container end -->
</section><!-- Main container end -->

@if($aboutVideos->isNotEmpty())
  <section class="about-video-section">
    <div class="container">
      <div class="row text-center">
        <div class="col-lg-12">
          <h2 class="section-title">Video Perusahaan</h2>
          <h3 class="section-sub-title">Aktivitas &amp; Dokumentasi</h3>
        </div>
      </div>

      <div class="about-video-grid {{ $aboutVideos->count() === 1 ? 'is-single' : '' }}">
        @foreach($aboutVideos as $aboutVideo)
          @if($aboutVideo->embedUrl())
            <article class="about-video-card">
              <div class="about-video-frame">
                <iframe
                  src="{{ $aboutVideo->embedUrl() }}"
                  title="{{ $aboutVideo->displayTitle() }}"
                  loading="lazy"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen></iframe>
              </div>
              <h4 class="about-video-title text-center">{{ $aboutVideo->displayTitle() }}</h4>
            </article>
          @endif
        @endforeach
      </div>
    </div>
  </section>
@endif

<section id="facts" class="facts-area dark-bg">
  <div class="container">
    <div class="facts-wrapper">
        @php
          $aboutCounters = [
            [$aboutPageSetting->counter_1_number, $aboutPageSetting->counter_1_label, $aboutPageSetting->counter_1_icon],
            [$aboutPageSetting->counter_2_number, $aboutPageSetting->counter_2_label, $aboutPageSetting->counter_2_icon],
            [$aboutPageSetting->counter_3_number, $aboutPageSetting->counter_3_label, $aboutPageSetting->counter_3_icon],
            [$aboutPageSetting->counter_4_number, $aboutPageSetting->counter_4_label, $aboutPageSetting->counter_4_icon],
          ];
        @endphp
        <div class="row">
          @foreach($aboutCounters as $counter)
            <div class="col-md-3 col-sm-6 ts-facts {{ $loop->first ? '' : ($loop->iteration === 2 ? 'mt-5 mt-sm-0' : 'mt-5 mt-md-0') }}">
                <div class="ts-facts-icon"><i class="fas {{ $counter[2] }}"></i></div>
                <div class="ts-facts-content">
                  <h2 class="ts-facts-num"><span class="counterUp" data-count="{{ $counter[0] }}">0</span></h2>
                  <h3 class="ts-facts-title">{{ $counter[1] }}</h3>
                </div>
            </div>
          @endforeach
        </div> <!-- Facts end -->
    </div>
    <!--/ Content row end -->
  </div>
  <!--/ Container end -->
</section><!-- Facts end -->

<section id="ts-team" class="ts-team">
  <div class="container">
    <div class="row text-center">
        <div class="col-lg-12">
          <h2 class="section-title">{{ $aboutPageSetting->team_label }}</h2>
          <h3 class="section-sub-title">{{ $aboutPageSetting->team_title }}</h3>
        </div>
    </div><!--/ Title row end -->

    <div class="row">
        <div class="col-lg-12">
          @if($aboutTeams->isNotEmpty())
            <div id="team-slide" class="team-slide">
              @foreach($aboutTeams as $aboutTeam)
                <div class="item">
                  <div class="ts-team-wrapper">
                    <div class="team-img-wrapper">
                      <img loading="lazy" decoding="async" class="w-100" src="{{ $aboutTeam->imageUrl() }}" alt="{{ $aboutTeam->name }}" width="360" height="360">
                    </div>
                    <div class="ts-team-content">
                      <h3 class="ts-name">{{ $aboutTeam->name }}</h3>
                      <p class="ts-designation">{{ $aboutTeam->position }}</p>
                      @if($aboutTeam->description)
                        <p class="ts-description">{{ $aboutTeam->description }}</p>
                      @endif
                      @if($aboutTeam->linkedin_url || $aboutTeam->instagram_url || $aboutTeam->twitter_url)
                        <div class="team-social-icons">
                          @if($aboutTeam->twitter_url)<a target="_blank" rel="noopener" href="{{ $aboutTeam->twitter_url }}"><i class="fab fa-twitter"></i></a>@endif
                          @if($aboutTeam->instagram_url)<a target="_blank" rel="noopener" href="{{ $aboutTeam->instagram_url }}"><i class="fab fa-instagram"></i></a>@endif
                          @if($aboutTeam->linkedin_url)<a target="_blank" rel="noopener" href="{{ $aboutTeam->linkedin_url }}"><i class="fab fa-linkedin"></i></a>@endif
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div><!-- Team slide end -->
          @else
            <p class="text-center text-muted mb-0">Informasi tim akan segera tersedia.</p>
          @endif
        </div>
    </div><!--/ Content row end -->
  </div><!--/ Container end -->
</section><!--/ Team end -->
@endsection
