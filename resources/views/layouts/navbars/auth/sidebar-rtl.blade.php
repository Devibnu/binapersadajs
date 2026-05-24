<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-end me-3 rotate-caret" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute start-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
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

  <div class="collapse navbar-collapse px-0 w-auto max-height-vh-100 h-100" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('paneladmin') || Request::is('paneladmin/dashboard') ? 'active' : '') }}" href="{{ route('paneladmin.dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-home text-dark text-sm"></i>
          </div>
          <span class="nav-link-text me-1">Dashboard</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="pe-4 me-2 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Website</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('paneladmin/settings') ? 'active' : '' }}" href="{{ route('paneladmin.settings.edit') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-cog text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text me-1">Pengaturan Website</span>
        </a>
      </li>

	      <li class="nav-item">
	        <a class="nav-link {{ Request::is('paneladmin/hero-banners*') ? 'active' : '' }}" href="{{ route('paneladmin.hero-banners.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-image text-dark text-sm opacity-10"></i>
          </div>
	          <span class="nav-link-text me-1">Hero Banner</span>
	        </a>
	      </li>

	      <li class="nav-item">
	        <a class="nav-link {{ Request::is('paneladmin/services*') ? 'active' : '' }}" href="{{ route('paneladmin.services.index') }}">
	          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
	            <i class="fas fa-tools text-dark text-sm opacity-10"></i>
	          </div>
	          <span class="nav-link-text me-1">Services</span>
	        </a>
	      </li>

	      <li class="nav-item">
	        <a class="nav-link {{ Request::is('paneladmin/project-categories*') ? 'active' : '' }}" href="{{ route('paneladmin.project-categories.index') }}">
	          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
	            <i class="fas fa-tags text-dark text-sm opacity-10 {{ Request::is('paneladmin/project-categories*') ? 'text-white' : 'text-dark' }}"></i>
	          </div>
	          <span class="nav-link-text me-1">Project Categories</span>
	        </a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link {{ Request::is('paneladmin/projects*') ? 'active' : '' }}" href="{{ route('paneladmin.projects.index') }}">
	          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
	            <i class="fas fa-hard-hat text-dark text-sm opacity-10"></i>
	          </div>
	          <span class="nav-link-text me-1">Projects</span>
	        </a>
	      </li>

	      <li class="nav-item">
	        <a class="nav-link {{ Request::is('paneladmin/page-heroes*') ? 'active' : '' }}" href="{{ route('paneladmin.page-heroes.index') }}">
	          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
	            <i class="fas fa-images text-dark text-sm opacity-10"></i>
	          </div>
	          <span class="nav-link-text me-1">Page Hero</span>
	        </a>
	      </li>
	    </ul>
  </div>
</aside>
