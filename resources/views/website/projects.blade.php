@extends('layouts.website')

@section('title', 'Projects - PT. Bina Persada Jaya Sejahtera')

@push('styles')
<style>
  .project-card-meta {
    margin: 10px 0 14px;
  }

  .project-card-meta p {
    color: rgba(255, 255, 255, .88);
    font-size: 12px;
    line-height: 1.45;
    margin: 0 0 3px;
  }

  .project-card-meta-label {
    color: rgba(255, 255, 255, .68);
    display: inline-block;
    font-weight: 600;
    margin-right: 5px;
  }

  .project-item-title span {
    color: #fff;
  }

  .projects-gallery .project-img-container .project-item-info {
    align-items: center;
    bottom: 0;
    display: flex;
    margin-top: 0;
    top: 0;
  }

  .projects-gallery .project-item-info-content {
    width: 100%;
  }

  @media (max-width: 767px) {
    .projects-gallery {
      padding-top: 42px;
    }

    .projects-gallery .shuffle-btn-group {
      display: flex;
      flex-wrap: nowrap;
      gap: 5px;
      margin: 0 0 28px;
      overflow-x: auto;
      padding-bottom: 3px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }

    .projects-gallery .shuffle-btn-group::-webkit-scrollbar {
      display: none;
    }

    .projects-gallery .shuffle-btn-group label {
      flex: 0 0 auto;
      padding: 11px 14px;
      white-space: nowrap;
    }

    .projects-gallery .shuffle-item {
      width: 100%;
    }

    .projects-gallery .project-img-container {
      aspect-ratio: 1 / 1;
    }

    .projects-gallery .project-img-container .gallery-popup {
      display: block;
      height: 100%;
    }

    .projects-gallery .project-img-container img {
      height: 100%;
      object-fit: cover;
      width: 100%;
    }

    .projects-gallery .project-img-container:after,
    .projects-gallery .project-img-container .gallery-popup .gallery-icon,
    .projects-gallery .project-img-container .project-item-info-content {
      opacity: 1;
    }

    .projects-gallery .project-img-container .gallery-popup .gallery-icon,
    .projects-gallery .project-img-container .project-item-info-content {
      -webkit-transform: perspective(1px) translate3d(0, 0, 0);
      transform: perspective(1px) translate3d(0, 0, 0);
    }

    .projects-gallery .project-img-container .gallery-popup .gallery-icon {
      -webkit-transform: perspective(1px) scale3d(1, 1, 1);
      transform: perspective(1px) scale3d(1, 1, 1);
    }

    .projects-gallery .project-img-container .project-item-info {
      align-items: flex-end;
      padding: 22px 20px;
    }

    .projects-gallery .project-item-title {
      font-size: 17px !important;
      line-height: 1.3;
      margin-bottom: 10px;
    }

    .project-card-meta {
      margin: 7px 0 10px;
    }

    .project-card-meta p {
      font-size: 12px;
      line-height: 1.5;
    }
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Project';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'All Projects';
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
  $fallbackProjects = [
    ['title' => 'Industrial Fabrication Work', 'category' => 'Fabrication', 'image' => 'project1.jpg', 'client' => null, 'location' => 'Cilegon, Banten', 'year' => '2025'],
    ['title' => 'Plant Maintenance Support', 'category' => 'Maintenance', 'image' => 'project2.jpg', 'client' => null, 'location' => null, 'year' => '2025'],
    ['title' => 'Pipe Installation Project', 'category' => 'Piping', 'image' => 'project3.jpg', 'client' => null, 'location' => 'Banten', 'year' => null],
    ['title' => 'Scaffolding Site Access', 'category' => 'Scaffolding', 'image' => 'project4.jpg', 'client' => null, 'location' => null, 'year' => null],
    ['title' => 'Mechanical Equipment Work', 'category' => 'Mechanical', 'image' => 'project5.jpg', 'client' => null, 'location' => null, 'year' => null],
    ['title' => 'Electrical Installation Work', 'category' => 'Electrical', 'image' => 'project6.jpg', 'client' => null, 'location' => null, 'year' => null],
  ];
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
                <li class="breadcrumb-item"><a href="{{ route('website.projects') }}">Projects</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageHeroBreadcrumb }}</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section id="main-container" class="main-container projects-gallery">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="shuffle-btn-group">
          <label class="active" for="all">
            <input type="radio" name="shuffle-filter" id="all" value="all" checked="checked">Show All
          </label>
          @if($projectCategories->isNotEmpty())
            @foreach($projectCategories as $category)
              <label for="project-category-{{ $category->slug }}">
                <input type="radio" name="shuffle-filter" id="project-category-{{ $category->slug }}" value="{{ $category->slug }}">{{ $category->name }}
              </label>
            @endforeach
          @elseif($showProjectFallback)
            @foreach(['Fabrication', 'Maintenance', 'Piping', 'Scaffolding', 'Mechanical', 'Electrical', 'Construction'] as $category)
              <label for="project-category-{{ strtolower($category) }}">
                <input type="radio" name="shuffle-filter" id="project-category-{{ strtolower($category) }}" value="{{ strtolower($category) }}">{{ $category }}
              </label>
            @endforeach
          @endif
        </div>

        <div class="row shuffle-wrapper">
          <div class="col-1 shuffle-sizer"></div>
          @if($projects->isNotEmpty())
            @foreach($projects as $project)
              <div class="col-lg-4 col-md-6 shuffle-item" data-groups='["{{ $project->categoryKey() }}"]'>
                <div class="project-img-container">
                  <a class="gallery-popup" href="{{ $project->featuredImageUrl() }}">
                    <img class="img-fluid" src="{{ $project->featuredImageUrl() }}" alt="{{ $project->title }}">
                    <span class="gallery-icon"><i class="fa fa-plus"></i></span>
                  </a>
                  <div class="project-item-info">
                    <div class="project-item-info-content">
                      <h3 class="project-item-title"><span>{{ $project->title }}</span></h3>
                      <p class="project-cat">{{ $project->categoryName() }}</p>
                      @if($project->client_name || $project->project_location || $project->project_year)
                        <div class="project-card-meta">
                          @if($project->client_name)
                            <p><span class="project-card-meta-label">Client:</span>{{ $project->client_name }}</p>
                          @endif
                          @if($project->project_location)
                            <p><span class="project-card-meta-label">Lokasi:</span>{{ $project->project_location }}</p>
                          @endif
                          @if($project->project_year)
                            <p><span class="project-card-meta-label">Tahun:</span>{{ $project->project_year }}</p>
                          @endif
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @elseif($showProjectFallback)
            @foreach($fallbackProjects as $project)
              <div class="col-lg-4 col-md-6 shuffle-item" data-groups='["{{ strtolower($project['category']) }}"]'>
                <div class="project-img-container">
                  <a class="gallery-popup" href="{{ asset('web/images/projects/' . $project['image']) }}">
                    <img class="img-fluid" src="{{ asset('web/images/projects/' . $project['image']) }}" alt="{{ $project['title'] }}">
                    <span class="gallery-icon"><i class="fa fa-plus"></i></span>
                  </a>
                  <div class="project-item-info">
                    <div class="project-item-info-content">
                      <h3 class="project-item-title"><span>{{ $project['title'] }}</span></h3>
                      <p class="project-cat">{{ $project['category'] }}</p>
                      @if($project['client'] || $project['location'] || $project['year'])
                        <div class="project-card-meta">
                          @if($project['client'])
                            <p><span class="project-card-meta-label">Client:</span>{{ $project['client'] }}</p>
                          @endif
                          @if($project['location'])
                            <p><span class="project-card-meta-label">Lokasi:</span>{{ $project['location'] }}</p>
                          @endif
                          @if($project['year'])
                            <p><span class="project-card-meta-label">Tahun:</span>{{ $project['year'] }}</p>
                          @endif
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
