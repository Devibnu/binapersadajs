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

  @media (max-width: 575px) {
    .project-card-meta {
      margin: 7px 0 10px;
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
    ['Capital Teltway Building', 'Fabrication', 'Industrial construction and project execution support.', 'project1.jpg'],
    ['Ghum Touch Hospital', 'Maintenance', 'Facility installation and maintenance support.', 'project2.jpg'],
    ['TNT East Facility', 'Piping', 'Site execution and industrial coordination work.', 'project3.jpg'],
    ['Narriot Headquarters', 'Scaffolding', 'Construction and fabrication project support.', 'project4.jpg'],
    ['Kalas Metrorail', 'Mechanical', 'Mechanical and civil installation works.', 'project5.jpg'],
    ['Ancraft Avenue House', 'Electrical', 'Building execution and maintenance support.', 'project6.jpg'],
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

<section id="main-container" class="main-container">
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
            @foreach(['Commercial', 'Healthcare', 'Government', 'Infrastructure', 'Residential'] as $category)
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
                      <h3 class="project-item-title"><a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a></h3>
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
                      <a class="btn btn-primary btn-sm" href="{{ route('projects.show', $project->slug) }}">View Project</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @elseif($showProjectFallback)
            @foreach($fallbackProjects as $project)
              <div class="col-lg-4 col-md-6 shuffle-item" data-groups='["{{ strtolower($project[1]) }}"]'>
                <div class="project-img-container">
                  <a class="gallery-popup" href="{{ asset('web/images/projects/' . $project[3]) }}">
                    <img class="img-fluid" src="{{ asset('web/images/projects/' . $project[3]) }}" alt="{{ $project[0] }}">
                    <span class="gallery-icon"><i class="fa fa-plus"></i></span>
                  </a>
                  <div class="project-item-info">
                    <div class="project-item-info-content">
                      <h3 class="project-item-title"><a href="#">{{ $project[0] }}</a></h3>
                      <p class="project-cat">{{ $project[1] }}</p>
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
