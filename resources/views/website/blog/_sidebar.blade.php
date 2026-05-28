<div class="sidebar sidebar-left">
  <div class="widget recent-posts">
    <h3 class="widget-title">Artikel Terbaru</h3>
    @if($recentPosts->isEmpty())
      <p class="mb-0">Belum ada artikel terbaru.</p>
    @else
      <ul class="list-unstyled">
        @foreach($recentPosts as $recentPost)
          <li class="d-flex align-items-start">
            <div class="posts-thumb">
              <a href="{{ route('website.blog.show', $recentPost->slug) }}">
                <img loading="lazy" decoding="async" alt="{{ $recentPost->title }}" src="{{ $recentPost->featuredImageUrl() }}" width="90" height="70">
              </a>
            </div>
            <div class="post-info">
              <h4 class="entry-title">
                <a href="{{ route('website.blog.show', $recentPost->slug) }}">{{ $recentPost->title }}</a>
              </h4>
              <p class="post-date">{{ $recentPost->displayDate() }}</p>
            </div>
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="widget">
    <h3 class="widget-title">Kategori</h3>
    <ul class="arrow nav nav-tabs">
      @forelse($categories as $category => $count)
        <li><a href="{{ route('website.blog.index', ['kategori' => $category]) }}">{{ $category }} <span class="float-right">({{ $count }})</span></a></li>
      @empty
        <li>Belum ada kategori.</li>
      @endforelse
    </ul>
  </div>

  <div class="widget">
    <h3 class="widget-title">Arsip</h3>
    <ul class="arrow nav nav-tabs">
      @forelse($archives as $archive)
        <li><a href="{{ route('website.blog.index', ['arsip' => $archive['key']]) }}">{{ $archive['label'] }} <span class="float-right">({{ $archive['count'] }})</span></a></li>
      @empty
        <li>Belum ada arsip.</li>
      @endforelse
    </ul>
  </div>

  <div class="widget widget-tags">
    <h3 class="widget-title">Tag</h3>
    <ul class="list-unstyled">
      @forelse($tags as $tag => $count)
        <li><a href="{{ route('website.blog.index', ['tag' => $tag]) }}">{{ $tag }}</a></li>
      @empty
        <li>Belum ada tag.</li>
      @endforelse
    </ul>
  </div>

  <div class="widget">
    <h3 class="widget-title">Berlangganan Update</h3>
    <p>Dapatkan kabar proyek dan informasi perusahaan terbaru.</p>
    @if(session('lead_success') && session('lead_source') === 'blog-sidebar')
      <div class="alert alert-success">{{ session('lead_success') }}</div>
    @endif
    <form method="POST" action="{{ route('website.leads.newsletter') }}">
      @csrf
      <input type="hidden" name="source" value="blog-sidebar">
      <div class="d-none">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
      </div>
      <div class="form-group mb-2">
        <input type="email" name="email" class="form-control" placeholder="Email Anda" required maxlength="150">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Berlangganan Update</button>
    </form>
  </div>
</div>
