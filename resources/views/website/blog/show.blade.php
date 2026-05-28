@extends('layouts.website')

@php
  $seoTitle = $post->meta_title ?: $post->title;
  $seoDescription = $post->meta_description ?: $post->excerpt;
  $seoImage = $post->ogImageUrl();
  $pageSeoSetting = \App\Models\SeoSetting::current();
  $seoUrl = $pageSeoSetting->canonicalUrl(route('website.blog.show', $post->slug));
@endphp

@section('title', $seoTitle)
@if($seoDescription)
  @section('meta_description', $seoDescription)
@endif
@if($post->meta_keywords)
  @section('meta_keywords', $post->meta_keywords)
@endif
@section('og_image', $seoImage)
@section('og_type', 'article')

@push('schema')
  @include('website.partials.breadcrumb-schema', ['items' => [
    ['name' => 'Beranda', 'url' => $pageSeoSetting->canonicalUrl(route('website.home'))],
    ['name' => 'Blog', 'url' => $pageSeoSetting->canonicalUrl(route('website.blog.index'))],
    ['name' => $post->title, 'url' => $seoUrl],
  ]])
  <script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $seoDescription,
    'image' => [$seoImage],
    'author' => [
      '@type' => 'Person',
      'name' => $post->displayAuthor(),
    ],
    'publisher' => [
      '@type' => 'Organization',
      'name' => $websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera',
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $pageSeoSetting->schemaLogoUrl($websiteSetting),
      ],
    ],
    'datePublished' => $post->published_at?->toAtomString(),
    'dateModified' => $post->updated_at?->toAtomString(),
    'mainEntityOfPage' => $seoUrl,
    'articleBody' => trim(strip_tags($post->content ?? '')),
  ], fn ($value) => $value !== null && $value !== ''), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush

@push('styles')
<style>
  .blog-rich-content img {
    height: auto;
    max-width: 100%;
  }

  .blog-rich-content table {
    border-collapse: collapse;
    margin-bottom: 24px;
    width: 100%;
  }

  .blog-rich-content td,
  .blog-rich-content th {
    border: 1px solid #e5e5e5;
    padding: 10px 12px;
  }

  .admin-comment-reply {
    background: #f5f8f6;
    border-left: 3px solid #1f8f5f;
    margin: 18px 0 0 24px;
    padding: 14px 16px;
  }

  .admin-comment-reply .reply-author {
    color: #1f8f5f;
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 5px;
  }

  .admin-comment-reply p {
    font-size: 14px;
    margin-bottom: 0;
  }

  @media (max-width: 575px) {
    .admin-comment-reply {
      margin-left: 12px;
    }
  }
</style>
@endpush

@section('content')
@php
  $pageHeroBackground = $pageHero?->backgroundUrl() ?? asset('web/images/banner/banner1.jpg');
  $pageHeroTitle = $pageHero?->title ?? 'Blog';
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
                <li class="breadcrumb-item"><a href="{{ route('website.blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
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

      <div class="col-lg-8 order-0 order-lg-1">
        <div class="post post-single">
          <div class="post-media post-image">
            <img loading="lazy" decoding="async" src="{{ $post->featuredImageUrl() }}" class="img-fluid" alt="{{ $post->title }}" width="750" height="450">
          </div>
          <div class="post-body">
            <div class="entry-header">
              <div class="post-meta">
                <span class="post-author"><i class="far fa-user"></i> {{ $post->displayAuthor() }}</span>
                <span class="post-cat"><i class="far fa-folder-open"></i> {{ $post->category }}</span>
                <span class="post-meta-date"><i class="far fa-calendar"></i> {{ $post->displayDate() }}</span>
              </div>
              <h2 class="entry-title">{{ $post->title }}</h2>
            </div>
            <div class="entry-content blog-rich-content">
              {!! $post->content !!}
            </div>
            @if($post->tagList())
              <div class="tags-area clearfix">
                <div class="post-tags">
                  @foreach($post->tagList() as $tag)
                    <a href="{{ route('website.blog.index', ['tag' => $tag]) }}">{{ $tag }}</a>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </div>

        <div id="comments" class="comments-area">
          <h3 class="comments-counter">{{ $comments->count() }} Komentar</h3>

          @if(session('comment_success'))
            <div class="alert alert-success" role="alert">
              {{ session('comment_success') }}
            </div>
          @endif

          @if($comments->isEmpty())
            <p>Belum ada komentar.</p>
          @else
            <ul class="comments-list">
              @foreach($comments as $comment)
                <li class="comment {{ $loop->last ? 'last' : '' }}">
                  <div class="comment-body">
                    <div class="meta-data">
                      <span class="comment-author">{{ $comment->name }}</span>
                      <span class="comment-date float-right">
                        {{ $comment->approved_at?->locale('id')->translatedFormat('d F Y') ?? $comment->created_at->locale('id')->translatedFormat('d F Y') }}
                      </span>
                    </div>
                    <div class="comment-content">
                      <p>{{ $comment->comment }}</p>
                    </div>
                    @foreach($comment->replies as $reply)
                      <div class="admin-comment-reply">
                        <span class="reply-author">Tim Bina Persada</span>
                        <p>{{ $reply->comment }}</p>
                      </div>
                    @endforeach
                  </div>
                </li>
              @endforeach
            </ul>
          @endif

          <div class="comments-form border-top pt-4 mt-4">
            <h3 class="title-normal">Tinggalkan Komentar</h3>
            <p class="text-muted">Komentar akan tampil setelah disetujui oleh admin.</p>
            <form action="{{ route('website.blog.comments.store', $post) }}" method="POST">
              @csrf
              <div class="d-none">
                <label for="website_url">Website</label>
                <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="comment_name">Nama</label>
                    <input class="form-control form-control-name @error('name') is-invalid @enderror" name="name" id="comment_name" value="{{ old('name') }}" type="text" required maxlength="100">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="comment_email">Email</label>
                    <input class="form-control form-control-email @error('email') is-invalid @enderror" name="email" id="comment_email" value="{{ old('email') }}" type="email" required maxlength="150">
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="comment_text">Komentar</label>
                <textarea class="form-control form-control-message @error('comment') is-invalid @enderror" name="comment" id="comment_text" rows="6" required minlength="5" maxlength="1000">{{ old('comment') }}</textarea>
                @error('comment')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button class="btn btn-primary solid blank" type="submit">Kirim Komentar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
