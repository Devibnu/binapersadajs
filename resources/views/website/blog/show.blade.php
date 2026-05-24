@extends('layouts.website')

@section('title', 'Constra - ' . $post['title'])

@section('content')
<div id="banner-area" class="banner-area" style="background-image:url({{ asset('web/images/banner/banner1.jpg') }})">
  <div class="banner-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="banner-heading">
            <h1 class="banner-title">{{ $post['title'] }}</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $post['title'] }}</li>
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
      <div class="col-lg-8 mb-5 mb-lg-0">
        <div class="post post-single">
          <div class="post-media post-image">
            <img loading="lazy" src="{{ asset($post['image']) }}" class="img-fluid" alt="{{ $post['title'] }}">
          </div>

          <div class="post-body">
            <div class="entry-header">
              <div class="post-meta">
                <span><i class="far fa-calendar"></i> {{ $post['date'] }}</span>
                <span class="post-comment"><i class="far fa-folder-open"></i> {{ $post['category'] }}</span>
              </div>
              <h2 class="entry-title">{{ $post['title'] }}</h2>
            </div>

            <div class="entry-content">
              @foreach ($post['content'] as $paragraph)
                <p>{{ $paragraph }}</p>
              @endforeach
            </div>

            <div class="tags-area d-flex align-items-center justify-content-between flex-wrap">
              <div class="post-tags">
                <a href="{{ route('website.blog.index') }}">{{ $post['category'] }}</a>
                <a href="{{ route('website.blog.index') }}">Bina Persada JS</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="sidebar sidebar-right">
          <div class="widget recent-posts">
            <h3 class="widget-title">Related Posts</h3>
            <ul class="list-unstyled">
              @foreach ($relatedPosts as $related)
                <li class="d-flex align-items-start">
                  <div class="posts-thumb">
                    <a href="{{ route('website.blog.show', $related['slug']) }}">
                      <img loading="lazy" alt="{{ $related['title'] }}" src="{{ asset($related['image']) }}">
                    </a>
                  </div>
                  <div class="post-info">
                    <h4 class="entry-title">
                      <a href="{{ route('website.blog.show', $related['slug']) }}">{{ $related['title'] }}</a>
                    </h4>
                    <p class="post-date">{{ $related['date'] }}</p>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>

          <div class="widget box solid">
            <h3 class="widget-title">Need Support?</h3>
            <p>Diskusikan kebutuhan maintenance, fabrication, installation, atau aktivitas HSE bersama tim kami.</p>
            <a class="btn btn-primary" href="{{ route('website.contact') }}">Contact Us</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
