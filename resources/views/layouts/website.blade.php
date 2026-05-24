<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $websiteSetting?->deskripsi_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera - Industrial Contractor & Fabrication Company' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0c1e35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BPJS">
    <title>@yield('title', ($websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera') . ' - Company Profile')</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/pwa/icons/icon-192x192.png">
    <link rel="icon" type="image/png" href="{{ $websiteSetting?->faviconUrl() ?? asset('web/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/animate-css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/slick/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('web/plugins/colorbox/colorbox.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}?v={{ filemtime(public_path('web/css/style.css')) }}">
    <style>
        .header-one .logo img.site-logo-img {
            height: auto !important;
            max-width: 100% !important;
            width: 165px !important;
        }

        @media (max-width: 991px) {
            .header-one .logo img.site-logo-img {
                width: 175px !important;
            }
        }

        @media (max-width: 575px) {
            .header-one .logo img.site-logo-img {
                width: 185px !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="body-inner">
    <div id="top-bar" class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <ul class="top-info text-center text-md-left">
                        <li><i class="fas fa-map-marker-alt"></i>
                            <p class="info-text">{{ $websiteSetting?->alamat ?? '9051 Constra Incorporate, USA' }}</p>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 top-social text-center text-md-right">
                    <ul class="list-unstyled">
                        <li>
                            @if($websiteSetting?->facebook)<a title="Facebook" href="{{ $websiteSetting->facebook }}"><span class="social-icon"><i class="fab fa-facebook-f"></i></span></a>@endif
                            @if($websiteSetting?->instagram)<a title="Instagram" href="{{ $websiteSetting->instagram }}"><span class="social-icon"><i class="fab fa-instagram"></i></span></a>@endif
                            @if($websiteSetting?->linkedin)<a title="LinkedIn" href="{{ $websiteSetting->linkedin }}"><span class="social-icon"><i class="fab fa-linkedin-in"></i></span></a>@endif
                            @if($websiteSetting?->youtube)<a title="YouTube" href="{{ $websiteSetting->youtube }}"><span class="social-icon"><i class="fab fa-youtube"></i></span></a>@endif
                            @unless($websiteSetting?->facebook || $websiteSetting?->instagram || $websiteSetting?->linkedin || $websiteSetting?->youtube)
                                <a title="Facebook" href="#"><span class="social-icon"><i class="fab fa-facebook-f"></i></span></a>
                                <a title="Instagram" href="#"><span class="social-icon"><i class="fab fa-instagram"></i></span></a>
                                <a title="LinkedIn" href="#"><span class="social-icon"><i class="fab fa-linkedin-in"></i></span></a>
                            @endunless
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <header id="header" class="header-one">
        <div class="bg-white">
            <div class="container">
                <div class="logo-area">
                    <div class="row align-items-center">
                        <div class="logo col-lg-3 text-center text-lg-left mb-3 mb-md-5 mb-lg-0">
                            <a class="d-block" href="{{ route('website.home') }}">
                                <img class="site-logo-img" loading="lazy" src="{{ $websiteSetting?->logoUrl() ?? asset('web/images/logo.png') }}" alt="{{ $websiteSetting?->nama_perusahaan ?? 'Constra' }}">
                            </a>
                        </div>
                        <div class="col-lg-9 header-right">
                            <ul class="top-info-box">
                                <li>
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <p class="info-box-title">Call Us</p>
                                            <p class="info-box-subtitle">{{ $websiteSetting?->telepon ?? '(+9) 847-291-4353' }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <p class="info-box-title">Email Us</p>
                                            <p class="info-box-subtitle">{{ $websiteSetting?->email ?? 'office@Constra.com' }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="last certificate-with-quote">
                                    <div class="info-box last">
                                        <div class="info-box-content">
                                            <p class="info-box-title">{{ $websiteSetting?->certificate_label ?: 'Global Certificate' }}</p>
                                            <p class="info-box-subtitle">{{ $websiteSetting?->certificate_value ?: 'ISO 9001:2017' }}</p>
                                        </div>
                                    </div>
                                    <div class="header-get-a-quote">
                                        <a class="btn btn-primary" href="{{ route('website.contact') }}">Get A Quote</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-navigation">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-dark p-0">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div id="navbar-collapse" class="collapse navbar-collapse">
                                <ul class="nav navbar-nav mr-auto">
                                    <li class="nav-item {{ request()->routeIs('website.home') ? 'active' : '' }}"><a class="nav-link" href="{{ route('website.home') }}">Home</a></li>
                                    <li class="nav-item {{ request()->routeIs('website.about') ? 'active' : '' }}"><a class="nav-link" href="{{ route('website.about') }}">About</a></li>
                                    <li class="nav-item {{ request()->routeIs('services.*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('services.index') }}">Services</a></li>
                                    <li class="nav-item {{ request()->routeIs('website.projects') ? 'active' : '' }}"><a class="nav-link" href="{{ route('website.projects') }}">Projects</a></li>
                                    <li class="nav-item {{ request()->routeIs('website.blog.*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('website.blog.index') }}">Blog</a></li>
                                    <li class="nav-item {{ request()->routeIs('website.contact') ? 'active' : '' }}"><a class="nav-link" href="{{ route('website.contact') }}">Contact</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="nav-search">
                    <span id="search"><i class="fa fa-search"></i></span>
                </div>
                <div class="search-block" style="display: none;">
                    <label for="search-field" class="w-100 mb-0">
                        <input type="text" class="form-control" id="search-field" placeholder="Type what you want and enter">
                    </label>
                    <span class="search-close">&times;</span>
                </div>
            </div>
        </div>
    </header>
    @yield('content')
    <footer id="footer" class="footer bg-overlay">
        <div class="footer-main">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-4 col-md-6 footer-widget footer-about">
                        <a class="footer-brand" href="{{ route('website.home') }}">
                            @if(!empty($websiteSetting?->logo))
                                <img loading="lazy" class="footer-logo-img" src="{{ asset('storage/' . $websiteSetting->logo) }}" alt="{{ $websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera' }}">
                            @else
                                <img loading="lazy" class="footer-logo-img" src="{{ asset('web/images/logo.png') }}" alt="PT. Bina Persada Jaya Sejahtera">
                            @endif
                        </a>
                        <p>{{ $websiteSetting?->deskripsi_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera is an industrial contractor and fabrication company supporting maintenance, construction, fabrication, supplier, and manpower needs.' }}</p>
                        <div class="footer-social">
                            <ul>
                                @if($websiteSetting?->facebook)<li><a href="{{ $websiteSetting->facebook }}" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>@endif
                                @if($websiteSetting?->instagram)<li><a href="{{ $websiteSetting->instagram }}" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>@endif
                                @if($websiteSetting?->linkedin)<li><a href="{{ $websiteSetting->linkedin }}" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>@endif
                                @if($websiteSetting?->youtube)<li><a href="{{ $websiteSetting->youtube }}" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>@endif
                                @unless($websiteSetting?->facebook || $websiteSetting?->instagram || $websiteSetting?->linkedin || $websiteSetting?->youtube)
                                    <li><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
                                @endunless
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-widget mt-5 mt-md-0">
                        <h3 class="widget-title">Quick Menu</h3>
                        <ul class="list-arrow">
                            <li><a href="{{ route('website.home') }}">Home</a></li>
                            <li><a href="{{ route('website.about') }}">About</a></li>
                            <li><a href="{{ route('services.index') }}">Services</a></li>
                            <li><a href="{{ route('website.projects') }}">Projects</a></li>
                            <li><a href="{{ route('website.blog.index') }}">Blog</a></li>
                            <li><a href="{{ route('website.contact') }}">Contact</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 mt-5 mt-lg-0 footer-widget">
                        <h3 class="widget-title">Services</h3>
                        <ul class="list-arrow">
                            <li><a href="{{ route('services.index') }}">Mechanical Work</a></li>
                            <li><a href="{{ route('services.index') }}">Electrical Work</a></li>
                            <li><a href="{{ route('services.index') }}">Fabrication</a></li>
                            <li><a href="{{ route('services.index') }}">Maintenance</a></li>
                            <li><a href="{{ route('services.index') }}">Piping & Civil Construction</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 mt-5 mt-lg-0 footer-widget footer-contact">
                        <h3 class="widget-title">Contact Info</h3>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $websiteSetting?->alamat ?? 'Ruko Cilegon Green Mega Blok, Blok E2 No.10 Cilegon, Banten 42441 Indonesia' }}</p>
                        <p><i class="fas fa-phone"></i> {{ $websiteSetting?->telepon ?? '0254-7871299' }}</p>
                        <p><i class="fas fa-envelope"></i> {{ $websiteSetting?->email ?? 'binapersada.teknik@gmail.com' }}</p>
                        @if($websiteSetting?->whatsapp)
                            @php($whatsappNumber = preg_replace('/\D+/', '', $websiteSetting->whatsapp))
                            <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener">{{ $websiteSetting->whatsapp }}</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="copyright-info text-center">
                            <span>{{ $websiteSetting?->footer_text ?? '© ' . date('Y') . ' PT. Bina Persada Jaya Sejahtera. All Rights Reserved.' }}</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="footer-menu text-center">
                            <ul class="list-unstyled mb-0">
                                <li><a href="{{ route('website.about') }}">About</a></li>
                                <li><a href="{{ route('services.index') }}">Services</a></li>
                                <li><a href="{{ route('website.projects') }}">Projects</a></li>
                                <li><a href="{{ route('website.blog.index') }}">Blog</a></li>
                                <li><a href="{{ route('website.contact') }}">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="back-to-top" data-spy="affix" data-offset-top="10" class="back-to-top position-fixed">
                    <button class="btn btn-primary" title="Back to Top"><i class="fa fa-angle-double-up"></i></button>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('web/plugins/jQuery/jquery.min.js') }}"></script>
    <script src="{{ asset('web/plugins/bootstrap/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('web/plugins/slick/slick.min.js') }}"></script>
    <script src="{{ asset('web/plugins/slick/slick-animation.min.js') }}"></script>
    <script src="{{ asset('web/plugins/colorbox/jquery.colorbox.js') }}"></script>
    <script src="{{ asset('web/plugins/shuffle/shuffle.min.js') }}" defer></script>
    @if(config('services.google_maps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}" defer></script>
    @endif
    <script src="{{ asset('web/plugins/google-map/map.js') }}" defer></script>
    <script src="{{ asset('web/js/script.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    @stack('scripts')
</div>
</body>
</html>
