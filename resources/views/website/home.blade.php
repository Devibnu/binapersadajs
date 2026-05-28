@extends('layouts.website')

@section('title', ($websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera') . ' - Industrial Contractor & Fabrication')

@push('preload')
  @php
    $firstHeroBanner = $heroBanners->first();
    $firstHeroImage = $firstHeroBanner ? $firstHeroBanner->backgroundUrl() : asset('web/images/slider-main/bg4.jpg');
  @endphp
  <link rel="preload" as="image" href="{{ $firstHeroImage }}" fetchpriority="high">
@endpush

@section('content')
<div class="banner-carousel banner-carousel-1 mb-0 industrial-hero">
  @forelse($heroBanners as $banner)
  @php
    $contentPosition = $banner->resolvedContentPosition($loop->iteration);
    $textClass = match ($contentPosition) {
      'left' => 'hero-text-left text-left text-start',
      'right' => 'hero-text-right text-right text-end',
      default => 'hero-text-center text-center',
    };
    $justifyClass = match ($contentPosition) {
      'left' => 'justify-content-start',
      'right' => 'justify-content-end',
      default => 'justify-content-center',
    };
  @endphp
  <div class="banner-carousel-item" style="background-image:url({{ $banner->backgroundUrl() }})">
    <div class="slider-content">
      <div class="container h-100">
        <div class="row align-items-center h-100 {{ $justifyClass }}">
          <div class="col-md-12 {{ $textClass }}">
            @if($banner->display_small_text)
              <h2 class="slide-title" data-animation-in="slideInLeft">{{ $banner->display_small_text }}</h2>
            @endif
            <h3 class="slide-sub-title" data-animation-in="slideInRight">{{ $banner->display_title }}</h3>
            @if($banner->display_description)
              <p class="slider-description lead" data-animation-in="slideInLeft">{{ $banner->display_description }}</p>
            @endif
            <p class="hero-actions" data-animation-in="slideInLeft" data-duration-in="1.2">
              @if($banner->display_button_text && $banner->display_button_link)
                <a href="{{ $banner->display_button_link }}" class="slider btn btn-primary">{{ $banner->display_button_text }}</a>
              @endif
              <a href="{{ route('website.contact') }}" class="slider btn btn-primary border">Contact Now</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="banner-carousel-item" style="background-image:url({{ asset('web/images/slider-main/bg4.jpg') }})">
    <div class="slider-content">
      <div class="container h-100">
        <div class="row align-items-center h-100 justify-content-center">
          <div class="col-md-12 hero-text-center text-center">
            <h2 class="slide-title" data-animation-in="slideInLeft">Industrial Contractor & Fabrication</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">PT. Bina Persada Jaya Sejahtera</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              Mechanical, piping, fabrication, maintenance, scaffolding, manpower supply, electrical, and civil construction support for industrial sites.
            </p>
            <p class="hero-actions" data-animation-in="slideInLeft" data-duration-in="1.2">
              <a href="{{ route('services.index') }}" class="slider btn btn-primary">Our Services</a>
              <a href="{{ route('website.contact') }}" class="slider btn btn-primary border">Contact Now</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="banner-carousel-item" style="background-image:url({{ asset('web/images/projects/parallax1.jpg') }})">
    <div class="slider-content text-left text-start">
      <div class="container h-100">
        <div class="row align-items-center h-100 justify-content-start">
          <div class="col-md-12 hero-text-left text-left text-start">
            <h2 class="slide-title" data-animation-in="slideInLeft">Maintenance & Project Support</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">Reliable Site Execution</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              We support fabrication, installation, and shutdown work with safety-focused teams and practical field coordination.
            </p>
            <p class="hero-actions" data-animation-in="slideInLeft" data-duration-in="1.2">
              <a href="{{ route('website.projects') }}" class="slider btn btn-primary">View Projects</a>
              <a href="{{ route('website.contact') }}" class="slider btn btn-primary border">Contact Now</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="banner-carousel-item" style="background-image:url({{ asset('web/images/slider-main/bg5.jpg') }})">
    <div class="slider-content text-right text-end">
      <div class="container h-100">
        <div class="row align-items-center h-100 justify-content-end">
          <div class="col-md-12 hero-text-right text-right text-end">
            <h2 class="slide-title" data-animation-in="slideInLeft">Engineering, Construction & Supply</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">Built for Industrial Demands</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              A contractor partner for fabrication, civil work, material support, and project manpower needs.
            </p>
            <p class="hero-actions" data-animation-in="slideInLeft" data-duration-in="1.2">
              <a href="{{ route('website.contact') }}" class="slider btn btn-primary" aria-label="contact-with-us">Get A Quote</a>
              <a href="{{ route('website.contact') }}" class="slider btn btn-primary border">Contact Now</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforelse
</div>

@php
  $homepageVideoEmbed = $homepageVideo->embedUrl();
  $homepageVideoFeatures = array_filter([
    $homepageVideo->feature_1,
    $homepageVideo->feature_2,
    $homepageVideo->feature_3,
    $homepageVideo->feature_4,
  ]);
@endphp
@if($homepageVideo->is_active && $homepageVideoEmbed)
<section class="homepage-video" aria-label="{{ $homepageVideo->section_label }}">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <button type="button" class="homepage-video-media" data-toggle="modal" data-target="#homepageVideoModal" aria-label="Putar video {{ $homepageVideo->title }}">
          <img src="{{ $homepageVideo->thumbnailUrl() }}" alt="{{ $homepageVideo->title }}" width="750" height="422" loading="lazy" decoding="async">
          <span class="homepage-video-overlay"></span>
          <span class="homepage-video-play"><i class="fas fa-play"></i></span>
        </button>
      </div>
      <div class="col-lg-6 mt-5 mt-lg-0">
        <div class="homepage-video-content">
          <h2 class="section-title">{{ $homepageVideo->section_label }}</h2>
          <h3 class="section-sub-title">{{ $homepageVideo->title }}</h3>
          <p>{{ $homepageVideo->description }}</p>
          @if($homepageVideoFeatures)
            <div class="homepage-video-features">
              @foreach($homepageVideoFeatures as $feature)
                <span><i class="fas fa-check-circle"></i> {{ $feature }}</span>
              @endforeach
            </div>
          @endif
          <div class="homepage-video-actions">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#homepageVideoModal">
              <i class="fas fa-play"></i> {{ $homepageVideo->button_text ?: 'Tonton Video' }}
            </button>
            @if($homepageVideo->button_link)
              <a class="homepage-video-external" href="{{ $homepageVideo->button_link }}" target="_blank" rel="noopener" aria-label="Buka tautan video pada tab baru">
                <i class="fas fa-external-link-alt" aria-hidden="true"></i>
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade homepage-video-modal" id="homepageVideoModal" tabindex="-1" role="dialog" aria-labelledby="homepageVideoModalTitle" aria-hidden="true" inert data-video-url="{{ $homepageVideoEmbed }}">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="homepageVideoModalTitle">{{ $homepageVideo->title }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" id="homepageVideoFrame" src="" title="{{ $homepageVideo->title }}" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
      </div>
    </div>
  </div>
</div>
@endif

<section class="call-to-action-box no-padding industrial-cta">
  <div class="container">
    <div class="action-style-box">
      <div class="row align-items-center">
        <div class="col-md-8 text-center text-md-left">
          <div class="call-to-action-text">
            <h3 class="action-title">Need industrial maintenance, fabrication, or site manpower support?</h3>
          </div>
        </div>
        <div class="col-md-4 text-center text-md-right mt-3 mt-md-0">
          <div class="call-to-action-btn">
            <a class="btn btn-dark" href="{{ route('website.contact') }}">Request Quote</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="ts-features" class="ts-features industrial-about">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="ts-intro">
          <h2 class="into-title">{{ $homepageSetting->about_label }}</h2>
          <h3 class="into-sub-title">{{ $homepageSetting->about_title }}</h3>
          <p>{{ $homepageSetting->about_description }}</p>
        </div>

        <div class="gap-20"></div>

        <div class="row industrial-values-list">
          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas {{ $homepageSetting->about_feature_1_icon }}"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">{{ $homepageSetting->about_feature_1_title }}</h3>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas {{ $homepageSetting->about_feature_2_icon }}"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">{{ $homepageSetting->about_feature_2_title }}</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="row industrial-values-list">
          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas {{ $homepageSetting->about_feature_3_icon }}"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">{{ $homepageSetting->about_feature_3_title }}</h3>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas {{ $homepageSetting->about_feature_4_icon }}"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">{{ $homepageSetting->about_feature_4_title }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mt-4 mt-lg-0">
        <h3 class="into-sub-title">{{ $homepageSetting->values_title }}</h3>
        <p>{{ $homepageSetting->values_description }}</p>

        <div class="accordion accordion-group" id="our-values-accordion">
          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingOne">
              <h2 class="mb-0">
                <button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  {{ $homepageSetting->value_1_title }}
                </button>
              </h2>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#our-values-accordion">
              <div class="card-body">
                {{ $homepageSetting->value_1_description }}
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingTwo">
              <h2 class="mb-0">
                <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  {{ $homepageSetting->value_2_title }}
                </button>
              </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#our-values-accordion">
              <div class="card-body">
                {{ $homepageSetting->value_2_description }}
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingThree">
              <h2 class="mb-0">
                <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  {{ $homepageSetting->value_3_title }}
                </button>
              </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#our-values-accordion">
              <div class="card-body">
                {{ $homepageSetting->value_3_description }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="facts" class="facts-area dark-bg industrial-facts">
  <div class="container">
    <div class="facts-wrapper">
      @php
        $counters = [
          [$homepageSetting->counter_1_number, $homepageSetting->counter_1_label, $homepageSetting->counter_1_icon],
          [$homepageSetting->counter_2_number, $homepageSetting->counter_2_label, $homepageSetting->counter_2_icon],
          [$homepageSetting->counter_3_number, $homepageSetting->counter_3_label, $homepageSetting->counter_3_icon],
          [$homepageSetting->counter_4_number, $homepageSetting->counter_4_label, $homepageSetting->counter_4_icon],
        ];
      @endphp
      <div class="row">
        @foreach($counters as $counter)
          @php
            preg_match('/^(\d+)(.*)$/', trim((string) $counter[0]), $counterParts);
          @endphp
          <div class="col-md-3 col-sm-6 ts-facts {{ $loop->first ? '' : ($loop->iteration === 2 ? 'mt-5 mt-sm-0' : 'mt-5 mt-md-0') }}">
            <div class="ts-facts-icon"><i class="fas {{ $counter[2] }}"></i></div>
            <div class="ts-facts-content">
              <h2 class="ts-facts-num">
                @if($counterParts)
                  <span class="counterUp" data-count="{{ $counterParts[1] }}">0</span>{{ $counterParts[2] }}
                @else
                  {{ $counter[0] }}
                @endif
              </h2>
              <h3 class="ts-facts-title">{{ $counter[1] }}</h3>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section id="ts-service-area" class="ts-service-area industrial-services">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <h2 class="section-title">{{ $homepageSetting->service_section_label }}</h2>
        <h3 class="section-sub-title">{{ $homepageSetting->service_section_title }}</h3>
      </div>
    </div>

	    <div class="row">
	      @if($services->isNotEmpty())
	        @foreach($services as $service)
	      <div class="col-lg-3 col-md-6 mb-4">
	        <div class="industrial-service-card">
	          <span class="industrial-service-icon">
	            @if($service->iconUrl())
              <img src="{{ $service->iconUrl() }}" alt="" width="36" height="36" loading="lazy" decoding="async" style="max-width: 36px; max-height: 36px; object-fit: contain;">
	            @else
	              <i class="fas {{ $service->iconClass() }}"></i>
	            @endif
	          </span>
	          <h3>{{ $service->title }}</h3>
	          <p>{{ $service->short_description ?: $service->description }}</p>
	        </div>
	      </div>
	        @endforeach
	      @elseif($showServiceFallback)
	      @php
	        $fallbackServices = [
	          ['Mechanical Work', 'Installation, repair, alignment, and mechanical project support.', 'fa-cogs'],
	          ['Electrical Work', 'Electrical installation, maintenance, and field troubleshooting support.', 'fa-bolt'],
          ['Fabrication', 'Steel structure, equipment support, and workshop/site fabrication work.', 'fa-drafting-compass'],
          ['Maintenance', 'Preventive and corrective maintenance for industrial facilities.', 'fa-tools'],
          ['Scaffolding', 'Scaffolding manpower and access support for safe work areas.', 'fa-layer-group'],
          ['Manpower Supply', 'Skilled manpower support for project and shutdown activities.', 'fa-users-cog'],
          ['Piping', 'Pipe fabrication, installation, modification, and maintenance.', 'fa-project-diagram'],
          ['Civil Construction', 'Civil, construction, and infrastructure support for project sites.', 'fa-road'],
	        ];
	      @endphp

	      @foreach($fallbackServices as $service)
	      <div class="col-lg-3 col-md-6 mb-4">
	        <div class="industrial-service-card">
          <span class="industrial-service-icon"><i class="fas {{ $service[2] }}"></i></span>
          <h3>{{ $service[0] }}</h3>
          <p>{{ $service[1] }}</p>
        </div>
	      </div>
	      @endforeach
	      @endif
	    </div>
  </div>
</section>

<section id="project-area" class="project-area solid-bg industrial-projects">
  <div class="container">
    <div class="row text-center">
      <div class="col-lg-12">
        <h2 class="section-title">Project Activity</h2>
        <h3 class="section-sub-title">Industrial Works</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="shuffle-btn-group">
          <label class="active" for="all">
            <input type="radio" name="shuffle-filter" id="all" value="all" checked="checked">Show All
          </label>
          <label for="maintenance">
            <input type="radio" name="shuffle-filter" id="maintenance" value="maintenance">Maintenance
          </label>
          <label for="fabrication">
            <input type="radio" name="shuffle-filter" id="fabrication" value="fabrication">Fabrication
          </label>
          <label for="piping">
            <input type="radio" name="shuffle-filter" id="piping" value="piping">Piping
          </label>
          <label for="civil">
            <input type="radio" name="shuffle-filter" id="civil" value="civil">Civil
          </label>
        </div>

        <div class="row shuffle-wrapper">
          <div class="col-1 shuffle-sizer"></div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;maintenance&quot;,&quot;civil&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project1.jpg') }}" aria-label="Lihat gambar Plant Maintenance Support">
                <img class="img-fluid" src="{{ asset('web/images/projects/project1.jpg') }}" alt="Industrial maintenance project" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Plant Maintenance Support">Plant Maintenance Support</a></h3>
                  <p class="project-cat">Maintenance</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;fabrication&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project2.jpg') }}" aria-label="Lihat gambar Steel Fabrication Work">
                <img class="img-fluid" src="{{ asset('web/images/projects/project2.jpg') }}" alt="Fabrication project" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Steel Fabrication Work">Steel Fabrication Work</a></h3>
                  <p class="project-cat">Fabrication</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;piping&quot;,&quot;fabrication&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project3.jpg') }}" aria-label="Lihat gambar Pipe Installation Project">
                <img class="img-fluid" src="{{ asset('web/images/projects/project3.jpg') }}" alt="Pipe installation project" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Pipe Installation Project">Pipe Installation Project</a></h3>
                  <p class="project-cat">Piping</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;civil&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project4.jpg') }}" aria-label="Lihat gambar Civil Construction Support">
                <img class="img-fluid" src="{{ asset('web/images/projects/project4.jpg') }}" alt="Civil construction project" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Civil Construction Support">Civil Construction Support</a></h3>
                  <p class="project-cat">Civil</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;maintenance&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project5.jpg') }}" aria-label="Lihat gambar Equipment Maintenance">
                <img class="img-fluid" src="{{ asset('web/images/projects/project5.jpg') }}" alt="Equipment maintenance" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Equipment Maintenance">Equipment Maintenance</a></h3>
                  <p class="project-cat">Maintenance</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;fabrication&quot;,&quot;civil&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project6.jpg') }}" aria-label="Lihat gambar Site Installation Work">
                <img class="img-fluid" src="{{ asset('web/images/projects/project6.jpg') }}" alt="Site installation work" width="750" height="600" loading="lazy" decoding="async">
                <span class="gallery-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="{{ route('website.projects') }}" aria-label="Lihat daftar project Site Installation Work">Site Installation Work</a></h3>
                  <p class="project-cat">Installation</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="general-btn text-center">
          <a class="btn btn-primary" href="{{ route('website.projects') }}">View All Projects</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="content industrial-quality">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h3 class="column-title">{{ $homepageSetting->quality_title }}</h3>
        <p class="lead">{{ $homepageSetting->quality_description }}</p>
        <p>{{ $homepageSetting->quality_sub_description }}</p>
      </div>

      <div class="col-lg-6 mt-5 mt-lg-0">
        <div class="industrial-check-grid">
          <div><i class="fas fa-check-circle"></i> {{ $homepageSetting->quality_item_1 }}</div>
          <div><i class="fas fa-check-circle"></i> {{ $homepageSetting->quality_item_2 }}</div>
          <div><i class="fas fa-check-circle"></i> {{ $homepageSetting->quality_item_3 }}</div>
          <div><i class="fas fa-check-circle"></i> {{ $homepageSetting->quality_item_4 }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

@php
  $inquiryWhatsappValue = trim((string) ($websiteSetting?->whatsapp ?? ''));
  $inquiryWhatsappIsNumber = $inquiryWhatsappValue !== '' && ! preg_match('/[^\d\s+().-]/', $inquiryWhatsappValue);
  $inquiryWhatsappRaw = $inquiryWhatsappIsNumber
    ? $inquiryWhatsappValue
    : (string) ($websiteSetting?->telepon ?? '');
  $inquiryWhatsappNumber = preg_replace('/\D+/', '', $inquiryWhatsappRaw);

  if (str_starts_with($inquiryWhatsappNumber, '0')) {
    $inquiryWhatsappNumber = '62' . substr($inquiryWhatsappNumber, 1);
  } elseif (str_starts_with($inquiryWhatsappNumber, '8')) {
    $inquiryWhatsappNumber = '62' . $inquiryWhatsappNumber;
  }

  $inquiryWhatsappMessage = rawurlencode('Halo PT. Bina Persada Jaya Sejahtera, saya ingin konsultasi terkait layanan perusahaan.');
@endphp
<section class="industrial-inquiry" aria-label="Permintaan penawaran">
  <div class="container">
    <div class="row align-items-stretch">
      <div class="col-lg-6 d-flex">
        <div class="industrial-inquiry-cta">
          <span class="industrial-inquiry-icon" aria-hidden="true"><i class="fas fa-headset"></i></span>
          <p class="industrial-inquiry-label">{{ $homepageSetting->cta_title }}</p>
          <h3>BUTUH SUPPORT INDUSTRIAL?</h3>
          <a class="industrial-inquiry-phone" href="tel:{{ preg_replace('/[^\d+]/', '', (string) $homepageSetting->cta_phone) }}">
            <i class="fas fa-phone-alt"></i> {{ $homepageSetting->cta_phone }}
          </a>
          <p class="industrial-inquiry-description">
            {{ $homepageSetting->cta_description }}
          </p>
          @if($inquiryWhatsappNumber)
            <a class="btn industrial-whatsapp-button" href="https://wa.me/{{ $inquiryWhatsappNumber }}?text={{ $inquiryWhatsappMessage }}" target="_blank" rel="noopener">
              <i class="fab fa-whatsapp"></i> Chat WhatsApp
            </a>
          @endif
        </div>
      </div>

      <div class="col-lg-6 d-flex mt-4 mt-lg-0">
        <div class="industrial-inquiry-form">
          <div class="industrial-inquiry-form-header">
            <p class="industrial-inquiry-label">Quick Inquiry</p>
            <h3>MINTA PENAWARAN</h3>
            <p>Sampaikan kebutuhan pekerjaan Anda. Tim kami akan segera menghubungi Anda.</p>
          </div>

          @if(session('lead_success') && session('lead_source') === 'cta')
            <div class="alert alert-success industrial-inquiry-alert" role="alert">{{ session('lead_success') }}</div>
          @endif

          <form method="POST" action="{{ route('website.leads.inquiry') }}">
            @csrf
            <div class="d-none">
              <input type="text" name="website_url" tabindex="-1" autocomplete="off">
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label for="inquiry-name">Nama</label>
                <input id="inquiry-name" type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama lengkap" maxlength="100" required>
              </div>
              <div class="col-md-6 form-group">
                <label for="inquiry-email">Email</label>
                <input id="inquiry-email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" maxlength="150" required>
              </div>
              <div class="col-12 form-group">
                <label for="inquiry-phone">Telepon / WhatsApp</label>
                <input id="inquiry-phone" type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="Nomor yang dapat dihubungi" maxlength="30">
              </div>
              <div class="col-12 form-group">
                <label for="inquiry-message">Kebutuhan Anda</label>
                <textarea id="inquiry-message" name="message" class="form-control" rows="4" maxlength="1000" placeholder="Ceritakan kebutuhan pekerjaan atau layanan yang diperlukan">{{ old('message') }}</textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary industrial-inquiry-submit w-100 mb-0">KIRIM PENAWARAN</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="news" class="news industrial-news">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <h2 class="section-title">{{ $homepageSetting->blog_label }}</h2>
        <h3 class="section-sub-title">{{ $homepageSetting->blog_title }}</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="latest-post">
          <div class="latest-post-media">
            <a href="{{ route('website.blog.show', 'project-maintenance-pt-sankyu') }}" class="latest-post-img">
              <img loading="lazy" decoding="async" class="img-fluid" src="{{ asset('web/images/news/news1.jpg') }}" alt="Project maintenance activity" width="750" height="450">
            </a>
          </div>
          <div class="post-body">
            <h4 class="post-title">
              <a href="{{ route('website.blog.show', 'project-maintenance-pt-sankyu') }}" class="d-inline-block">Project Maintenance PT Sankyu</a>
            </h4>
            <div class="latest-post-meta">
              <span class="post-item-date"><i class="fa fa-clock-o"></i> May 22, 2026</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="latest-post">
          <div class="latest-post-media">
            <a href="{{ route('website.blog.show', 'safety-work-procedure-in-industrial-area') }}" class="latest-post-img">
              <img loading="lazy" decoding="async" class="img-fluid" src="{{ asset('web/images/news/news2.jpg') }}" alt="Safety procedure" width="750" height="450">
            </a>
          </div>
          <div class="post-body">
            <h4 class="post-title">
              <a href="{{ route('website.blog.show', 'safety-work-procedure-in-industrial-area') }}" class="d-inline-block">Safety Work Procedure in Industrial Area</a>
            </h4>
            <div class="latest-post-meta">
              <span class="post-item-date"><i class="fa fa-clock-o"></i> May 22, 2026</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="latest-post">
          <div class="latest-post-media">
            <a href="{{ route('website.blog.show', 'fabrication-pipe-installation-project') }}" class="latest-post-img">
              <img loading="lazy" decoding="async" class="img-fluid" src="{{ asset('web/images/news/news3.jpg') }}" alt="Pipe fabrication project" width="750" height="450">
            </a>
          </div>
          <div class="post-body">
            <h4 class="post-title">
              <a href="{{ route('website.blog.show', 'fabrication-pipe-installation-project') }}" class="d-inline-block">Fabrication & Pipe Installation Project</a>
            </h4>
            <div class="latest-post-meta">
              <span class="post-item-date"><i class="fa fa-clock-o"></i> May 22, 2026</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="general-btn text-center mt-4">
      <a class="btn btn-primary" href="{{ route('website.blog.index') }}">See All Posts</a>
    </div>
  </div>
</section>

@if($homepageVideo->is_active && $homepageVideoEmbed)
@push('scripts')
<script>
  (function ($) {
    var modal = $('#homepageVideoModal');
    var iframe = $('#homepageVideoFrame');

	    modal.on('show.bs.modal', function () {
	      modal.removeAttr('inert');
	      iframe.attr('src', modal.data('video-url'));
	    });

	    modal.on('hidden.bs.modal', function () {
	      iframe.attr('src', '');
	      modal.attr('inert', '');
	    });
  })(jQuery);
</script>
@endpush
@endif
@endsection
