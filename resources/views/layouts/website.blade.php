<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $globalSeo = \App\Models\SeoSetting::current();
        $seoTitle = html_entity_decode(trim($__env->yieldContent('title')) ?: $globalSeo->meta_title, ENT_QUOTES, 'UTF-8');
        $seoDescription = html_entity_decode(trim($__env->yieldContent('meta_description')) ?: $globalSeo->meta_description, ENT_QUOTES, 'UTF-8');
        $seoKeywords = html_entity_decode(trim($__env->yieldContent('meta_keywords')) ?: $globalSeo->meta_keywords, ENT_QUOTES, 'UTF-8');
        $seoImage = html_entity_decode(trim($__env->yieldContent('og_image')) ?: $globalSeo->ogImageUrl($websiteSetting), ENT_QUOTES, 'UTF-8');
        $seoCanonical = html_entity_decode(trim($__env->yieldContent('canonical')) ?: $globalSeo->canonicalUrl(url()->current()), ENT_QUOTES, 'UTF-8');
        $seoType = html_entity_decode(trim($__env->yieldContent('og_type')) ?: 'website', ENT_QUOTES, 'UTF-8');
        $companyName = $globalSeo->schema_company_name ?: ($websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera');
        $companyPhone = $globalSeo->schema_phone ?: $websiteSetting?->telepon;
        $companyEmail = $globalSeo->schema_email ?: $websiteSetting?->email;
        $companyAddress = $globalSeo->schema_address ?: $websiteSetting?->alamat;
        $socialProfiles = array_values(array_filter([
            $websiteSetting?->facebook,
            $websiteSetting?->instagram,
            $websiteSetting?->linkedin,
            $websiteSetting?->youtube,
        ]));
        $organizationSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $companyName,
            'url' => rtrim($globalSeo->canonical_url ?: url('/'), '/'),
            'logo' => $globalSeo->schemaLogoUrl($websiteSetting),
            'email' => $companyEmail,
            'telephone' => $companyPhone,
            'sameAs' => $socialProfiles ?: null,
        ], fn ($value) => $value !== null && $value !== '');
        $localBusinessSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $companyName,
            'url' => rtrim($globalSeo->canonical_url ?: url('/'), '/'),
            'image' => $seoImage,
            'logo' => $globalSeo->schemaLogoUrl($websiteSetting),
            'email' => $companyEmail,
            'telephone' => $companyPhone,
            'address' => $companyAddress ? array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $companyAddress,
                'addressLocality' => $globalSeo->schema_city,
                'postalCode' => $globalSeo->schema_postal_code,
                'addressCountry' => $globalSeo->schema_country,
            ], fn ($value) => $value !== null && $value !== '') : null,
        ], fn ($value) => $value !== null && $value !== '');
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="{{ $globalSeo->robotsContent() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0d1b2f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bina Persada">
    <title>{{ $seoTitle }}</title>
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:type" content="{{ $seoType }}">
    <meta name="twitter:card" content="{{ $globalSeo->twitter_card_type }}">
    @if($globalSeo->twitter_site)
        <meta name="twitter:site" content="{{ $globalSeo->twitter_site }}">
    @endif
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    @if($globalSeo->google_site_verification)
        <meta name="google-site-verification" content="{{ $globalSeo->google_site_verification }}">
    @endif
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script type="application/ld+json">{!! json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @stack('schema')
    @stack('meta')
    @if($globalSeo->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $globalSeo->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($globalSeo->google_analytics_id));
        </script>
    @endif
    @if($globalSeo->google_tag_manager)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer',@json($globalSeo->google_tag_manager));
        </script>
    @endif
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="mask-icon" href="{{ asset('icons/safari-pinned-tab.svg') }}" color="#0d1b2f">
    <meta name="msapplication-TileColor" content="#0d1b2f">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    @stack('preload')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@400;600;700;800&display=swap">
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

        @media (max-width: 767px) {
            .header-one .logo img.site-logo-img {
                width: 170px !important;
            }
        }

        .whatsapp-widget {
            bottom: 72px;
            position: fixed;
            right: 28px;
            z-index: 1050;
        }

        .whatsapp-widget-toggle {
            align-items: center;
            background: #25d366;
            border: 0;
            border-radius: 50%;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .24);
            color: #fff;
            display: flex;
            font-size: 29px;
            height: 58px;
            justify-content: center;
            margin-left: auto;
            transition: transform .2s ease, background-color .2s ease;
            width: 58px;
        }

        .whatsapp-widget-toggle:hover,
        .whatsapp-widget-toggle:focus {
            background: #1fbd59;
            color: #fff;
            outline: 0;
            transform: translateY(-2px);
        }

        .whatsapp-widget-panel {
            background: #fff;
            border-radius: 8px;
            bottom: 72px;
            box-shadow: 0 16px 42px rgba(12, 30, 53, .2);
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            position: absolute;
            right: 0;
            transform: translateY(10px);
            transition: opacity .2s ease, transform .2s ease;
            width: 344px;
        }

        .whatsapp-widget.is-open .whatsapp-widget-panel {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .whatsapp-widget-header {
            align-items: center;
            background: #128c4e;
            color: #fff;
            display: flex;
            gap: 12px;
            padding: 16px 16px;
        }

        .whatsapp-widget-avatar {
            align-items: center;
            background: rgba(255, 255, 255, .18);
            border-radius: 50%;
            display: flex;
            flex: 0 0 42px;
            font-size: 22px;
            height: 42px;
            justify-content: center;
        }

        .whatsapp-widget-brand {
            flex: 1;
            line-height: 1.35;
        }

        .whatsapp-widget-brand strong {
            color: #fff;
            display: block;
            font-size: 15px;
        }

        .whatsapp-widget-brand span {
            color: #daf5e5;
            font-size: 12px;
        }

        .whatsapp-widget-close {
            background: transparent;
            border: 0;
            color: #fff;
            font-size: 20px;
            line-height: 1;
            padding: 4px;
        }

        .whatsapp-widget-body {
            padding: 16px;
        }

        .whatsapp-widget-greeting {
            background: #f2f6f4;
            border-radius: 6px;
            color: #263544;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 14px;
            padding: 11px 13px;
        }

        .whatsapp-widget-quick {
            background: #fff;
            border: 1px solid #dce5df;
            border-radius: 5px;
            color: #36454f;
            display: block;
            font-size: 13px;
            margin-bottom: 8px;
            padding: 9px 10px;
            text-align: left;
            transition: border-color .2s ease, background-color .2s ease;
            width: 100%;
        }

        .whatsapp-widget-quick:hover,
        .whatsapp-widget-quick:focus {
            background: #effaf4;
            border-color: #1f8f5f;
            color: #163d2c;
            outline: 0;
        }

        .whatsapp-widget-input {
            border: 1px solid #dce5df;
            border-radius: 5px;
            font-size: 13px;
            line-height: 1.45;
            margin-top: 7px;
            min-height: 74px;
            padding: 10px;
            resize: vertical;
            width: 100%;
        }

        .whatsapp-widget-input:focus {
            border-color: #1f8f5f;
            outline: 0;
        }

        .whatsapp-widget-send {
            align-items: center;
            background: #1f8f5f;
            border-radius: 4px;
            color: #fff;
            display: flex;
            font-size: 13px;
            font-weight: 700;
            gap: 8px;
            justify-content: center;
            margin-top: 12px;
            padding: 12px 14px;
            text-decoration: none;
            width: 100%;
        }

        .whatsapp-widget-send:hover,
        .whatsapp-widget-send:focus {
            background: #16744c;
            color: #fff;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .whatsapp-widget {
                bottom: 85px;
                right: 18px;
            }

            .whatsapp-widget-panel {
                max-width: calc(100vw - 36px);
                width: 336px;
            }

            .whatsapp-widget-toggle {
                font-size: 25px;
                height: 52px;
                width: 52px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
@if($globalSeo->google_tag_manager)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $globalSeo->google_tag_manager }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
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
                            @if($websiteSetting?->facebook)<a title="Facebook" aria-label="Buka Facebook PT. Bina Persada JS" href="{{ $websiteSetting->facebook }}" target="_blank" rel="noopener"><span class="social-icon"><i class="fab fa-facebook-f" aria-hidden="true"></i></span></a>@endif
                            @if($websiteSetting?->instagram)<a title="Instagram" aria-label="Buka Instagram PT. Bina Persada JS" href="{{ $websiteSetting->instagram }}" target="_blank" rel="noopener"><span class="social-icon"><i class="fab fa-instagram" aria-hidden="true"></i></span></a>@endif
                            @if($websiteSetting?->linkedin)<a title="LinkedIn" aria-label="Buka LinkedIn PT. Bina Persada JS" href="{{ $websiteSetting->linkedin }}" target="_blank" rel="noopener"><span class="social-icon"><i class="fab fa-linkedin-in" aria-hidden="true"></i></span></a>@endif
                            @if($websiteSetting?->youtube)<a title="YouTube" aria-label="Buka YouTube PT. Bina Persada JS" href="{{ $websiteSetting->youtube }}" target="_blank" rel="noopener"><span class="social-icon"><i class="fab fa-youtube" aria-hidden="true"></i></span></a>@endif
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
                                <img class="site-logo-img" src="{{ $websiteSetting?->logoUrl() ?? asset('web/images/logo.png') }}" alt="{{ $websiteSetting?->nama_perusahaan ?? 'Constra' }}" width="207" height="39" decoding="async" fetchpriority="high">
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
                    <button id="search" type="button" aria-label="Buka pencarian website"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
                <div class="search-block" style="display: none;">
                    <label for="search-field" class="w-100 mb-0">
                        <input type="text" class="form-control" id="search-field" placeholder="Type what you want and enter">
                    </label>
                    <button class="search-close" type="button" aria-label="Tutup pencarian">&times;</button>
                </div>
            </div>
        </div>
    </header>
	    <main id="main-content" role="main">
	        @yield('content')
	    </main>
    <footer id="footer" class="footer bg-overlay">
        <div class="footer-main">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-4 col-md-6 footer-widget footer-about">
                        <a class="footer-brand" href="{{ route('website.home') }}">
                            @if(!empty($websiteSetting?->logo))
                                <img loading="lazy" decoding="async" class="footer-logo-img" src="{{ asset('storage/' . $websiteSetting->logo) }}" alt="{{ $websiteSetting?->nama_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera' }}" width="207" height="39">
                            @else
                                <img loading="lazy" decoding="async" class="footer-logo-img" src="{{ asset('web/images/logo.png') }}" alt="PT. Bina Persada Jaya Sejahtera" width="207" height="39">
                            @endif
                        </a>
                        <p>{{ $websiteSetting?->deskripsi_perusahaan ?? 'PT. Bina Persada Jaya Sejahtera is an industrial contractor and fabrication company supporting maintenance, construction, fabrication, supplier, and manpower needs.' }}</p>
                        <div class="footer-social">
                            <ul>
                                @if($websiteSetting?->facebook)<li><a href="{{ $websiteSetting->facebook }}" target="_blank" rel="noopener" aria-label="Buka Facebook PT. Bina Persada JS"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>@endif
                                @if($websiteSetting?->instagram)<li><a href="{{ $websiteSetting->instagram }}" target="_blank" rel="noopener" aria-label="Buka Instagram PT. Bina Persada JS"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>@endif
                                @if($websiteSetting?->linkedin)<li><a href="{{ $websiteSetting->linkedin }}" target="_blank" rel="noopener" aria-label="Buka LinkedIn PT. Bina Persada JS"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a></li>@endif
                                @if($websiteSetting?->youtube)<li><a href="{{ $websiteSetting->youtube }}" target="_blank" rel="noopener" aria-label="Buka YouTube PT. Bina Persada JS"><i class="fab fa-youtube" aria-hidden="true"></i></a></li>@endif
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
                            @php
                                $footerWhatsappNumber = preg_replace('/\D+/', '', $websiteSetting->whatsapp);

                                if (str_starts_with($footerWhatsappNumber, '0')) {
                                    $footerWhatsappNumber = '62' . substr($footerWhatsappNumber, 1);
                                } elseif (str_starts_with($footerWhatsappNumber, '8')) {
                                    $footerWhatsappNumber = '62' . $footerWhatsappNumber;
                                }
                            @endphp
                            <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/{{ $footerWhatsappNumber }}" target="_blank" rel="noopener">{{ $websiteSetting->whatsapp }}</a></p>
                        @endif
                        <h3 class="widget-title mt-4">Newsletter</h3>
                        @if(session('lead_success') && session('lead_source') === 'footer')
                            <p class="text-success small">{{ session('lead_success') }}</p>
                        @endif
                        <form method="POST" action="{{ route('website.leads.newsletter') }}" class="footer-lead-form">
                            @csrf
                            <input type="hidden" name="source" value="footer">
                            <div class="d-none">
                                <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                            </div>
                            <div class="form-group mb-2">
                                <input type="email" name="email" class="form-control" value="{{ old('source') === 'footer' ? old('email') : '' }}" placeholder="Email Anda" required maxlength="150">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Berlangganan</button>
                        </form>
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
	                    <button class="btn btn-primary" title="Kembali ke atas" aria-label="Kembali ke bagian atas halaman"><i class="fa fa-angle-double-up" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </footer>
    @php
        $whatsappValue = trim((string) ($websiteSetting?->whatsapp ?? ''));
        $whatsappIsFormattedNumber = $whatsappValue !== '' && ! preg_match('/[^\d\s+().-]/', $whatsappValue);
        $whatsappRaw = $whatsappIsFormattedNumber ? $whatsappValue : (string) ($websiteSetting?->telepon ?? '');
        $whatsappNumber = preg_replace('/\D+/', '', $whatsappRaw);

        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        } elseif (str_starts_with($whatsappNumber, '8')) {
            $whatsappNumber = '62' . $whatsappNumber;
        }
    @endphp
    @if($whatsappNumber)
        <aside class="whatsapp-widget" id="whatsapp-widget" data-whatsapp-number="{{ $whatsappNumber }}" aria-label="Chat WhatsApp">
            <div class="whatsapp-widget-panel" id="whatsapp-widget-panel" aria-hidden="true" inert>
                <div class="whatsapp-widget-header">
                    <span class="whatsapp-widget-avatar"><i class="fab fa-whatsapp" aria-hidden="true"></i></span>
                    <div class="whatsapp-widget-brand">
                        <strong>PT. Bina Persada JS</strong>
                        <span>Online</span>
                    </div>
                    <button class="whatsapp-widget-close" type="button" id="whatsapp-widget-close" aria-label="Tutup chat">&times;</button>
                </div>
                <div class="whatsapp-widget-body">
                    <p class="whatsapp-widget-greeting">Halo, ada yang bisa kami bantu?</p>
                    <button type="button" class="whatsapp-widget-quick" data-message="Saya ingin konsultasi terkait layanan perusahaan.">Saya ingin konsultasi layanan</button>
                    <button type="button" class="whatsapp-widget-quick" data-message="Saya ingin meminta penawaran untuk kebutuhan pekerjaan kami.">Saya ingin meminta penawaran</button>
                    <button type="button" class="whatsapp-widget-quick" data-message="Saya ingin bertanya tentang project perusahaan.">Saya ingin bertanya tentang project</button>
                    <button type="button" class="whatsapp-widget-quick" data-message="Saya ingin menghubungi admin.">Saya ingin menghubungi admin</button>
                    <label class="sr-only" for="whatsapp-widget-message">Pesan WhatsApp</label>
                    <textarea class="whatsapp-widget-input" id="whatsapp-widget-message" placeholder="Tulis pesan Anda..."></textarea>
                    <a class="whatsapp-widget-send" id="whatsapp-widget-send" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" aria-label="Kirim pesan ke WhatsApp PT. Bina Persada JS">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i> Kirim ke WhatsApp
                    </a>
                </div>
            </div>
            <button class="whatsapp-widget-toggle" type="button" id="whatsapp-widget-toggle" aria-controls="whatsapp-widget-panel" aria-expanded="false" aria-label="Buka chat WhatsApp">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
            </button>
        </aside>
    @endif
    <script src="{{ asset('web/plugins/jQuery/jquery.min.js') }}"></script>
    <script src="{{ asset('web/plugins/bootstrap/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('web/plugins/slick/slick.min.js') }}"></script>
    <script src="{{ asset('web/plugins/slick/slick-animation.min.js') }}"></script>
    <script src="{{ asset('web/plugins/colorbox/jquery.colorbox.js') }}"></script>
    <script src="{{ asset('web/plugins/shuffle/shuffle.min.js') }}" defer></script>
    @if(config('services.google_maps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async" async defer></script>
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
    @if($whatsappNumber)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var widget = document.getElementById('whatsapp-widget');
                var panel = document.getElementById('whatsapp-widget-panel');
                var toggle = document.getElementById('whatsapp-widget-toggle');
                var close = document.getElementById('whatsapp-widget-close');
                var input = document.getElementById('whatsapp-widget-message');
                var send = document.getElementById('whatsapp-widget-send');
                var defaultMessage = 'Halo PT. Bina Persada Jaya Sejahtera, saya ingin bertanya tentang layanan.';

	                function setOpen(open) {
	                    widget.classList.toggle('is-open', open);
	                    panel.setAttribute('aria-hidden', open ? 'false' : 'true');
	                    panel.toggleAttribute('inert', !open);
	                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
	                }

                toggle.addEventListener('click', function () {
                    setOpen(!widget.classList.contains('is-open'));
                });

                close.addEventListener('click', function () {
                    setOpen(false);
                });

                widget.querySelectorAll('.whatsapp-widget-quick').forEach(function (button) {
                    button.addEventListener('click', function () {
                        input.value = 'Halo PT. Bina Persada Jaya Sejahtera,\n' + button.dataset.message;
                        input.focus();
                    });
                });

                send.addEventListener('click', function (event) {
                    var message = input.value.trim() || defaultMessage;
                    send.href = 'https://wa.me/' + widget.dataset.whatsappNumber + '?text=' + encodeURIComponent(message);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setOpen(false);
                    }
                });
            });
        </script>
    @endif
    @stack('scripts')
</div>
</body>
</html>
