@extends('layouts.website')

@section('title', $project->title . ' - PT. Bina Persada Jaya Sejahtera')

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
  $paragraphs = array_values(array_filter(preg_split('/\R{2,}/', trim($project->description ?? ''))));
  $galleryImages = $project->galleryImages();
@endphp
<div id="banner-area" class="banner-area page-hero-managed" style="background-image:url({{ $pageHeroBackground }}); --page-hero-overlay: {{ $pageHeroOpacity }};">
  <div class="banner-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="banner-heading {{ $pageHeroTextClass }}">
            <h1 class="banner-title">{{ $project->title }}</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb {{ $pageHeroBreadcrumbClass }}">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.projects') }}">Projects</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $project->title }}</li>
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
      <div class="col-lg-8">
        <div id="page-slider" class="page-slider">
          <div class="item">
            <img loading="lazy" class="img-fluid" src="{{ $project->featuredImageUrl() }}" alt="{{ $project->title }}">
          </div>
          @foreach($galleryImages as $galleryImage)
            <div class="item">
              <img loading="lazy" class="img-fluid" src="{{ $galleryImage }}" alt="{{ $project->title }} gallery image">
            </div>
          @endforeach
        </div>
      </div>

      <div class="col-lg-4 mt-5 mt-lg-0">
        <h3 class="column-title mrt-0">{{ $project->title }}</h3>
        @if($project->short_description)
          <p>{{ $project->short_description }}</p>
        @endif
        <ul class="project-info list-unstyled">
          <li><p class="project-info-label">Client</p><p class="project-info-content">{{ $project->client_name ?: '-' }}</p></li>
          <li><p class="project-info-label">Location</p><p class="project-info-content">{{ $project->project_location ?: '-' }}</p></li>
          <li><p class="project-info-label">Year</p><p class="project-info-content">{{ $project->project_year ?: '-' }}</p></li>
          <li><p class="project-info-label">Category</p><p class="project-info-content">{{ $project->categoryName() }}</p></li>
        </ul>
        <a class="btn btn-primary" href="{{ route('website.contact') }}">Request Quote</a>
      </div>
    </div>

    @if($paragraphs)
      <div class="gap-40"></div>
      <div class="row">
        <div class="col-lg-8">
          <h3 class="column-title">Project Overview</h3>
          @foreach($paragraphs as $paragraph)
            <p>{!! nl2br(e($paragraph)) !!}</p>
          @endforeach
        </div>
      </div>
    @endif

    @if($relatedProjects->isNotEmpty())
      <div class="gap-40"></div>
      <div class="row">
        <div class="col-12">
          <h3 class="column-title">Related Projects</h3>
        </div>
        @foreach($relatedProjects as $relatedProject)
          <div class="col-lg-4 col-md-6 mb-4 shuffle-item">
            <div class="project-img-container">
              <a href="{{ route('projects.show', $relatedProject->slug) }}">
                <img class="img-fluid" src="{{ $relatedProject->featuredImageUrl() }}" alt="{{ $relatedProject->title }}">
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('projects.show', $relatedProject->slug) }}">{{ $relatedProject->title }}</a></h3>
                  <p class="project-cat">{{ $relatedProject->categoryName() }}</p>
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
