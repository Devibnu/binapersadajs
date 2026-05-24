@extends('layouts.website')

@section('title', ($websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera') . ' - Industrial Contractor & Fabrication')

@section('content')
<div class="banner-carousel banner-carousel-1 mb-0 industrial-hero">
  @forelse($heroBanners as $banner)
  @php
    $contentPosition = $banner->resolvedContentPosition($loop->iteration);
    $textClass = match ($contentPosition) {
      'left' => 'text-left text-start',
      'right' => 'text-right text-end',
      default => 'text-center',
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
            <p data-animation-in="slideInLeft" data-duration-in="1.2">
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
          <div class="col-md-12 text-center">
            <h2 class="slide-title" data-animation-in="slideInLeft">Industrial Contractor & Fabrication</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">PT. Bina Persada Jaya Sejahtera</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              Mechanical, piping, fabrication, maintenance, scaffolding, manpower supply, electrical, and civil construction support for industrial sites.
            </p>
            <p data-animation-in="slideInLeft" data-duration-in="1.2">
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
          <div class="col-md-12 text-left text-start">
            <h2 class="slide-title" data-animation-in="slideInLeft">Maintenance & Project Support</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">Reliable Site Execution</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              We support fabrication, installation, and shutdown work with safety-focused teams and practical field coordination.
            </p>
            <p data-animation-in="slideInLeft" data-duration-in="1.2">
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
          <div class="col-md-12 text-right text-end">
            <h2 class="slide-title" data-animation-in="slideInLeft">Engineering, Construction & Supply</h2>
            <h3 class="slide-sub-title" data-animation-in="slideInRight">Built for Industrial Demands</h3>
            <p class="slider-description lead" data-animation-in="slideInLeft">
              A contractor partner for fabrication, civil work, material support, and project manpower needs.
            </p>
            <p data-animation-in="slideInLeft" data-duration-in="1.2">
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
          <h2 class="into-title">About Company</h2>
          <h3 class="into-sub-title">Industrial work delivered with safety, speed, and precision</h3>
          <p>{{ $websiteSetting?->deskripsi_perusahaan ?: 'PT. Bina Persada Jaya Sejahtera is an industrial contractor and fabrication company supporting construction, maintenance, mechanical, electrical, piping, scaffolding, manpower supply, and supplier needs for project sites.' }}</p>
        </div>

        <div class="gap-20"></div>

        <div class="row industrial-values-list">
          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas fa-hard-hat"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">Safety First Execution</h3>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas fa-tools"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">Field Ready Team</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="row industrial-values-list">
          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas fa-industry"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">Industrial Experience</h3>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ts-service-box">
              <span class="ts-service-icon"><i class="fas fa-handshake"></i></span>
              <div class="ts-service-box-content">
                <h3 class="service-box-title">Long Term Partnership</h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mt-4 mt-lg-0">
        <h3 class="into-sub-title">Our Values</h3>
        <p>Every project is handled with practical coordination, clear communication, and a strong commitment to HSE standards.</p>

        <div class="accordion accordion-group" id="our-values-accordion">
          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingOne">
              <h2 class="mb-0">
                <button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Health, Safety & Environment
                </button>
              </h2>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#our-values-accordion">
              <div class="card-body">
                Work planning, toolbox briefings, PPE discipline, and site coordination are treated as core project requirements.
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingTwo">
              <h2 class="mb-0">
                <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Quality Workmanship
                </button>
              </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#our-values-accordion">
              <div class="card-body">
                Fabrication, installation, and maintenance work are executed with attention to measurements, documentation, and handover readiness.
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header p-0 bg-transparent" id="headingThree">
              <h2 class="mb-0">
                <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Responsive Support
                </button>
              </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#our-values-accordion">
              <div class="card-body">
                We support project teams with flexible manpower, site material needs, and practical coordination for industrial activities.
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
      <div class="row">
        <div class="col-md-3 col-sm-6 ts-facts">
          <div class="ts-facts-icon"><i class="fas fa-industry"></i></div>
          <div class="ts-facts-content">
            <h2 class="ts-facts-num"><span class="counterUp" data-count="8">0</span></h2>
            <h3 class="ts-facts-title">Core Services</h3>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 ts-facts mt-5 mt-sm-0">
          <div class="ts-facts-icon"><i class="fas fa-user-shield"></i></div>
          <div class="ts-facts-content">
            <h2 class="ts-facts-num"><span class="counterUp" data-count="100">0</span>%</h2>
            <h3 class="ts-facts-title">HSE Commitment</h3>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 ts-facts mt-5 mt-md-0">
          <div class="ts-facts-icon"><i class="fas fa-cogs"></i></div>
          <div class="ts-facts-content">
            <h2 class="ts-facts-num"><span class="counterUp" data-count="24">0</span>/7</h2>
            <h3 class="ts-facts-title">Project Support</h3>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 ts-facts mt-5 mt-md-0">
          <div class="ts-facts-icon"><i class="fas fa-certificate"></i></div>
          <div class="ts-facts-content">
            <h2 class="ts-facts-num"><span class="counterUp" data-count="9001">0</span></h2>
            <h3 class="ts-facts-title">ISO Standard</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="ts-service-area" class="ts-service-area industrial-services">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <h2 class="section-title">Industrial Capabilities</h2>
        <h3 class="section-sub-title">What We Do</h3>
      </div>
    </div>

	    <div class="row">
	      @if($services->isNotEmpty())
	        @foreach($services as $service)
	      <div class="col-lg-3 col-md-6 mb-4">
	        <div class="industrial-service-card">
	          <span class="industrial-service-icon">
	            @if($service->iconUrl())
	              <img src="{{ $service->iconUrl() }}" alt="" style="max-width: 36px; max-height: 36px; object-fit: contain;">
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
              <a class="gallery-popup" href="{{ asset('web/images/projects/project1.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project1.jpg') }}" alt="Industrial maintenance project">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Plant Maintenance Support</a></h3>
                  <p class="project-cat">Maintenance</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;fabrication&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project2.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project2.jpg') }}" alt="Fabrication project">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Steel Fabrication Work</a></h3>
                  <p class="project-cat">Fabrication</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;piping&quot;,&quot;fabrication&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project3.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project3.jpg') }}" alt="Pipe installation project">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Pipe Installation Project</a></h3>
                  <p class="project-cat">Piping</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;civil&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project4.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project4.jpg') }}" alt="Civil construction project">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Civil Construction Support</a></h3>
                  <p class="project-cat">Civil</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;maintenance&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project5.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project5.jpg') }}" alt="Equipment maintenance">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Equipment Maintenance</a></h3>
                  <p class="project-cat">Maintenance</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 shuffle-item" data-groups="[&quot;fabrication&quot;,&quot;civil&quot;]">
            <div class="project-img-container">
              <a class="gallery-popup" href="{{ asset('web/images/projects/project6.jpg') }}" aria-label="project-img">
                <img class="img-fluid" src="{{ asset('web/images/projects/project6.jpg') }}" alt="Site installation work">
                <span class="gallery-icon"><i class="fa fa-plus"></i></span>
              </a>
              <div class="project-item-info">
                <div class="project-item-info-content">
                  <h3 class="project-item-title"><a href="#">Site Installation Work</a></h3>
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
        <h3 class="column-title">Quality & HSE Commitment</h3>
        <p class="lead">Project work is only useful when it is delivered safely, documented clearly, and ready for operation.</p>
        <p>Our field approach combines practical supervision, safety briefings, work permits, coordination with client teams, and clear project documentation.</p>
      </div>

      <div class="col-lg-6 mt-5 mt-lg-0">
        <div class="industrial-check-grid">
          <div><i class="fas fa-check-circle"></i> Safety work procedure</div>
          <div><i class="fas fa-check-circle"></i> Site coordination</div>
          <div><i class="fas fa-check-circle"></i> Project documentation</div>
          <div><i class="fas fa-check-circle"></i> Responsive support team</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="subscribe no-padding industrial-contact-strip">
  <div class="container">
    <div class="row">
      <div class="col-lg-4">
        <div class="subscribe-call-to-acton">
          <h3>Need Site Support?</h3>
          <h4>{{ $websiteSetting?->telepon ?? '0254-7871299' }}</h4>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="ts-newsletter row align-items-center">
          <div class="col-md-8 newsletter-introtext">
            <h4 class="text-white mb-0">Talk to our industrial project team</h4>
            <p class="text-white">Mechanical, fabrication, maintenance, piping, scaffolding, manpower, supplier, and civil work.</p>
          </div>

          <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <a class="btn btn-primary border" href="{{ route('website.contact') }}">Contact Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="news" class="news industrial-news">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <h2 class="section-title">Company Updates</h2>
        <h3 class="section-sub-title">Latest Blog</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="latest-post">
          <div class="latest-post-media">
            <a href="{{ route('website.blog.show', 'project-maintenance-pt-sankyu') }}" class="latest-post-img">
              <img loading="lazy" class="img-fluid" src="{{ asset('web/images/news/news1.jpg') }}" alt="Project maintenance activity">
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
              <img loading="lazy" class="img-fluid" src="{{ asset('web/images/news/news2.jpg') }}" alt="Safety procedure">
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
              <img loading="lazy" class="img-fluid" src="{{ asset('web/images/news/news3.jpg') }}" alt="Pipe fabrication project">
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
@endsection
