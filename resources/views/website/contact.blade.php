@extends('layouts.website')

@php
  $contactSeoSetting = \App\Models\SeoSetting::current();
@endphp

@section('title', 'Kontak - PT. Bina Persada Jaya Sejahtera')
@push('schema')
  @include('website.partials.breadcrumb-schema', ['items' => [
    ['name' => 'Beranda', 'url' => $contactSeoSetting->canonicalUrl(route('website.home'))],
    ['name' => 'Kontak', 'url' => $contactSeoSetting->canonicalUrl(route('website.contact'))],
  ]])
@endpush

@push('styles')
<style>
  .contact-info-row {
    margin-bottom: 44px;
  }

  .contact-info-row .ts-service-box-bg {
    margin-bottom: 20px;
  }

  .contact-map-frame {
    margin-bottom: 48px;
  }

  .contact-map-frame iframe {
    border: 0;
    display: block;
    height: 410px;
    width: 100% !important;
  }

  .contact-map-empty {
    background: #f5f7f8;
    border-left: 3px solid #1f8f5f;
    color: #5c6873;
    padding: 34px 24px;
    text-align: center;
  }

  .contact-feedback {
    border: 0;
    border-left: 4px solid;
    border-radius: 0;
    margin-bottom: 28px;
    padding: 16px 20px;
  }

  .contact-feedback.alert-success {
    background: #eef8f3;
    border-left-color: #1f8f5f;
    color: #155c3d;
  }

  .contact-feedback.alert-danger {
    background: #fbefee;
    border-left-color: #c0392b;
    color: #7b241c;
  }

  @media (max-width: 767px) {
    .contact-info-row {
      margin-bottom: 24px;
    }

    .contact-map-frame {
      margin-bottom: 36px;
    }

    .contact-map-frame iframe {
      height: 320px;
    }
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Kontak';
  $pageHeroBreadcrumb = $pageHero?->breadcrumb_text ?: 'Kontak';
  $pageHeroTextClass = $pageHero?->textClass() ?? 'text-center';
  $pageHeroBreadcrumbClass = $pageHero?->breadcrumbClass() ?? 'justify-content-center';
  $pageHeroOpacity = $pageHero?->overlay_opacity ?? 1;
  $contactSetting = $contactPageSetting ?? \App\Models\ContactPageSetting::current();
  $googleMapsValue = trim((string) ($contactSetting->map_embed ?: ($websiteSetting?->google_maps ?? '')));
  $hasGoogleMapsEmbed = preg_match('/<iframe\b[^>]*>.*?<\/iframe>/is', $googleMapsValue, $googleMapsMatch) === 1;
  $googleMapsEmbed = $hasGoogleMapsEmbed
    ? (preg_match('/\bloading\s*=/i', $googleMapsMatch[0])
      ? $googleMapsMatch[0]
      : preg_replace('/<iframe\b/i', '<iframe loading="lazy"', $googleMapsMatch[0], 1))
    : null;
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
                      <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Perusahaan</a></li>
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
        <h2 class="section-title">{{ $contactSetting->section_label }}</h2>
        <h3 class="section-sub-title">{{ $contactSetting->heading }}</h3>
      </div>
    </div>

    <div class="row contact-info-row">
      <div class="col-md-4">
        <div class="ts-service-box-bg text-center h-100">
          <span class="ts-service-icon icon-round">
            <i class="fas fa-map-marker-alt mr-0"></i>
          </span>
          <div class="ts-service-box-content">
            <h4>{{ $contactSetting->address_title }}</h4>
            <p>{{ $websiteSetting?->alamat ?: 'Alamat perusahaan belum tersedia.' }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="ts-service-box-bg text-center h-100">
          <span class="ts-service-icon icon-round">
            <i class="fa fa-envelope mr-0"></i>
          </span>
          <div class="ts-service-box-content">
            <h4>{{ $contactSetting->email_title }}</h4>
            <p>{{ $websiteSetting?->email ?: 'Email perusahaan belum tersedia.' }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="ts-service-box-bg text-center h-100">
          <span class="ts-service-icon icon-round">
            <i class="fa fa-phone-square mr-0"></i>
          </span>
          <div class="ts-service-box-content">
            <h4>{{ $contactSetting->phone_title }}</h4>
            <p>{{ $websiteSetting?->telepon ?: 'Nomor telepon belum tersedia.' }}</p>
          </div>
        </div>
      </div>

    </div>

    <div class="google-map contact-map-frame">
      @if($hasGoogleMapsEmbed)
        {!! $googleMapsEmbed !!}
      @else
        <div class="contact-map-empty">
          <i class="fas fa-map-marker-alt mr-2"></i> Lokasi Google Maps belum diatur.
        </div>
      @endif
    </div>

    <div class="row">
      <div class="col-md-12">
        <h3 class="column-title">{{ $contactSetting->form_heading }}</h3>
        @if(session('contact_success'))
          <div class="alert alert-success contact-feedback" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('contact_success') }}
          </div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger contact-feedback" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Mohon periksa kembali data yang Anda masukkan.
          </div>
        @endif
        <form id="contact-form" action="{{ route('website.contact.store') }}" method="post" role="form">
          @csrf
          <div class="error-container"></div>
          <div class="d-none" aria-hidden="true">
            <label for="website_url">Website</label>
            <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Nama</label>
                <input class="form-control form-control-name @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" type="text" required maxlength="100">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Email</label>
                <input class="form-control form-control-email @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" type="email" required maxlength="150">
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Telepon / WhatsApp</label>
                <input class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone') }}" type="text" maxlength="30">
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Subjek</label>
            <input class="form-control form-control-subject @error('subject') is-invalid @enderror" name="subject" id="subject" value="{{ old('subject') }}" type="text" maxlength="150">
            @error('subject')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Pesan</label>
            <textarea class="form-control form-control-message @error('message') is-invalid @enderror" name="message" id="message" rows="8" required minlength="10" maxlength="2000">{{ old('message') }}</textarea>
            @error('message')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="text-right"><br>
            <button class="btn btn-primary solid blank" type="submit">{{ $contactSetting->submit_button_text }}</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>
@endsection
