
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('paneladmin.dashboard') }}">
        @if(!empty($websiteSetting?->logo))
          <img src="{{ asset('storage/' . $websiteSetting->logo) }}" class="navbar-brand-img h-100" alt="Bina Persada JS">
        @else
          <img src="{{ asset('web/images/logo.png') }}" class="navbar-brand-img h-100" alt="Bina Persada JS">
        @endif
        <span class="ms-1 font-weight-bold">Bina Persada JS<span class="d-block text-xs">Panel Admin</span></span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
    @php
      $adminUser = auth()->user();
      $can = fn (string $permission): bool => (bool) $adminUser?->canAccess($permission);
      $hasWebsiteMenus = $can('website-settings.view') || $can('homepage-sections.view') || $can('hero-banners.view') || $can('page-heroes.view') || $can('contact-page.view') || $can('seo-settings.view') || $can('media-library.view');
      $hasContentMenus = $can('homepage-video.view') || $can('services.view') || $can('project-categories.view') || $can('projects.view') || $can('clients.view') || $can('blogs.view') || $can('blog-comments.view') || $can('about-page.view') || $can('about-videos.view') || $can('music-playlists.view') || $can('about-teams.view');
      $hasCommunicationMenus = $can('contact-messages.view') || $can('leads.view') || $can('inquiry-quotation.view') || $can('iqm-user.view');
      $hasAnalyticsMenus = $can('analytics.view') || $can('activity-logs.view');
      $hasSystemMenus = $can('email-settings.view') || $can('roles.view') || $can('users.view') || $adminUser;
    @endphp
    <ul class="navbar-nav">
      @if($can('dashboard.view'))
        <li class="nav-item mt-2 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Dashboard</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('paneladmin') || Request::is('paneladmin/dashboard') ? 'active' : '' }}" href="{{ route('paneladmin.dashboard') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-chart-pie text-dark text-sm opacity-10 {{ Request::is('paneladmin') || Request::is('paneladmin/dashboard') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
      @endif
      @if($hasWebsiteMenus)
        <li class="nav-item mt-3 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Website</h6>
        </li>
        @if($can('website-settings.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/settings') ? 'active' : '' }}" href="{{ route('paneladmin.settings.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-cog text-sm opacity-10 {{ Request::is('paneladmin/settings') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Pengaturan Website</span></a></li>
        @endif
        @if($can('homepage-sections.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/homepage-sections') ? 'active' : '' }}" href="{{ route('paneladmin.homepage-sections.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-home text-sm opacity-10 {{ Request::is('paneladmin/homepage-sections') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Homepage Sections</span></a></li>
        @endif
        @if($can('hero-banners.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/hero-banners*') ? 'active' : '' }}" href="{{ route('paneladmin.hero-banners.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-image text-sm opacity-10 {{ Request::is('paneladmin/hero-banners*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Hero Banner</span></a></li>
        @endif
        @if($can('page-heroes.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/page-heroes*') ? 'active' : '' }}" href="{{ route('paneladmin.page-heroes.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-images text-sm opacity-10 {{ Request::is('paneladmin/page-heroes*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Page Heroes</span></a></li>
        @endif
        @if($can('contact-page.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/contact-page') ? 'active' : '' }}" href="{{ route('paneladmin.contact-page.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-address-book text-sm opacity-10 {{ Request::is('paneladmin/contact-page') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Contact Page</span></a></li>
        @endif
        @if($can('seo-settings.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/seo-settings') ? 'active' : '' }}" href="{{ route('paneladmin.seo-settings.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-search text-sm opacity-10 {{ Request::is('paneladmin/seo-settings') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">SEO Settings</span></a></li>
        @endif
        @if($can('media-library.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/media-library*') ? 'active' : '' }}" href="{{ route('paneladmin.media-library.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-photo-video text-sm opacity-10 {{ Request::is('paneladmin/media-library*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Media Library</span></a></li>
        @endif
      @endif

      @if($hasContentMenus)
        <li class="nav-item mt-3 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Konten Website</h6>
        </li>
        @if($can('homepage-video.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/homepage-video*') ? 'active' : '' }}" href="{{ route('paneladmin.homepage-video.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-play-circle text-sm opacity-10 {{ Request::is('paneladmin/homepage-video*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Homepage Video</span></a></li>
        @endif
        @if($can('services.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/services*') ? 'active' : '' }}" href="{{ route('paneladmin.services.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-tools text-sm opacity-10 {{ Request::is('paneladmin/services*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Services</span></a></li>
        @endif
        @if($can('project-categories.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/project-categories*') ? 'active' : '' }}" href="{{ route('paneladmin.project-categories.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-tags text-sm opacity-10 {{ Request::is('paneladmin/project-categories*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Project Categories</span></a></li>
        @endif
        @if($can('projects.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/projects*') ? 'active' : '' }}" href="{{ route('paneladmin.projects.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-hard-hat text-sm opacity-10 {{ Request::is('paneladmin/projects*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Projects</span></a></li>
        @endif
        @if($can('clients.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/clients*') ? 'active' : '' }}" href="{{ route('paneladmin.clients.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-handshake text-sm opacity-10 {{ Request::is('paneladmin/clients*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Klien / Our Clients</span></a></li>
        @endif
        @if($can('blogs.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/blogs*') ? 'active' : '' }}" href="{{ route('paneladmin.blogs.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-newspaper text-sm opacity-10 {{ Request::is('paneladmin/blogs*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Blogs</span></a></li>
        @endif
        @if($can('blog-comments.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/blog-comments*') ? 'active' : '' }}" href="{{ route('paneladmin.blog-comments.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-comments text-sm opacity-10 {{ Request::is('paneladmin/blog-comments*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Blog Comments</span></a></li>
        @endif
        @if($can('about-page.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/about-page') ? 'active' : '' }}" href="{{ route('paneladmin.about-page.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-building text-sm opacity-10 {{ Request::is('paneladmin/about-page') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">About Page</span></a></li>
        @endif
        @if($can('about-videos.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/about-videos*') ? 'active' : '' }}" href="{{ route('paneladmin.about-videos.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-video text-sm opacity-10 {{ Request::is('paneladmin/about-videos*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">About Videos</span></a></li>
        @endif
        @if($can('music-playlists.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/music-playlists*') ? 'active' : '' }}" href="{{ route('paneladmin.music-playlists.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-music text-sm opacity-10 {{ Request::is('paneladmin/music-playlists*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Music Playlist</span></a></li>
        @endif
        @if($can('about-teams.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/about-teams*') ? 'active' : '' }}" href="{{ route('paneladmin.about-teams.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-users text-sm opacity-10 {{ Request::is('paneladmin/about-teams*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">About Teams</span></a></li>
        @endif
      @endif

      @if($hasCommunicationMenus)
        <li class="nav-item mt-3 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Komunikasi</h6>
        </li>
        @if($can('contact-messages.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/contact-messages*') ? 'active' : '' }}" href="{{ route('paneladmin.contact-messages.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-envelope text-sm opacity-10 {{ Request::is('paneladmin/contact-messages*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Contact Messages</span></a></li>
        @endif
        @if($can('leads.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/leads*') ? 'active' : '' }}" href="{{ route('paneladmin.leads.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-user-plus text-sm opacity-10 {{ Request::is('paneladmin/leads*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Leads / Newsletter</span></a></li>
        @endif
        @if($can('inquiry-quotation.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/inquiry-quotations*') ? 'active' : '' }}" href="{{ route('paneladmin.inquiry-quotations.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-file-alt text-sm opacity-10 {{ Request::is('paneladmin/inquiry-quotations*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Inquiry & Quotation</span></a></li>
        @endif
        @if($can('iqm-user.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/iqm-users*') ? 'active' : '' }}" href="{{ route('paneladmin.iqm-users.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-user-lock text-sm opacity-10 {{ Request::is('paneladmin/iqm-users*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">IQM User Portal</span></a></li>
        @endif
      @endif

      @if($hasAnalyticsMenus)
        <li class="nav-item mt-3 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Analytics &amp; Log</h6>
        </li>
        @if($can('analytics.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/analytics*') ? 'active' : '' }}" href="{{ route('paneladmin.analytics.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-chart-line text-sm opacity-10 {{ Request::is('paneladmin/analytics*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Analytics Visitor</span></a></li>
        @endif
        @if($can('activity-logs.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/activity-logs*') ? 'active' : '' }}" href="{{ route('paneladmin.activity-logs.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-history text-sm opacity-10 {{ Request::is('paneladmin/activity-logs*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Activity Logs</span></a></li>
        @endif
      @endif

      @if($hasSystemMenus)
        <li class="nav-item mt-3 mb-2">
          <h6 class="ps-4 ms-2 mb-0 text-uppercase text-xs font-weight-bolder opacity-6">Sistem</h6>
        </li>
        @if($can('email-settings.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/email-settings') ? 'active' : '' }}" href="{{ route('paneladmin.email-settings.edit') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-at text-sm opacity-10 {{ Request::is('paneladmin/email-settings') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Email Settings</span></a></li>
        @endif
        @if($can('roles.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/roles*') ? 'active' : '' }}" href="{{ route('paneladmin.roles.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-user-shield text-sm opacity-10 {{ Request::is('paneladmin/roles*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Roles &amp; Permissions</span></a></li>
        @endif
        @if($can('users.view'))
          <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/users*') ? 'active' : '' }}" href="{{ route('paneladmin.users.index') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-user-cog text-sm opacity-10 {{ Request::is('paneladmin/users*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Users</span></a></li>
        @endif
        <li class="nav-item"><a class="nav-link {{ Request::is('paneladmin/profile*') ? 'active' : '' }}" href="{{ route('paneladmin.profile') }}"><div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center"><i class="fas fa-id-badge text-sm opacity-10 {{ Request::is('paneladmin/profile*') ? 'text-white' : 'text-dark' }}"></i></div><span class="nav-link-text ms-1">Profile Saya</span></a></li>
      @endif
    </ul>
  </div>
</aside>
