<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0c1e35">
  <title>@yield('title', 'IQM Portal') - PT Bina Persada JS</title>
  @php
    $websiteSetting = $websiteSetting ?? \App\Models\WebsiteSetting::first();
    $iqmFaviconUrl = $websiteSetting?->faviconUrl() ?? asset('icons/favicon-32x32.png');
    $iqmAppleTouchIconUrl = $websiteSetting?->appleTouchIconUrl() ?? asset('icons/apple-touch-icon.png');
  @endphp
  <link rel="manifest" href="/manifest.json?v=20260604">
  <link rel="icon" type="image/png" href="{{ $iqmFaviconUrl }}?v={{ time() }}">
  <link rel="apple-touch-icon" href="{{ $iqmAppleTouchIconUrl }}?v={{ time() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body { font-family: Inter, system-ui, sans-serif; background: #f5f7fb; color: #172033; font-size: 14px; }
    .iqm-navbar { background: #0f2742; box-shadow: 0 10px 24px rgba(15,39,66,.14); }
    .iqm-brand-logo { display: block; width: auto; max-width: 210px; max-height: 48px; object-fit: contain; background: transparent; border: none; box-shadow: none; padding: 0; }
    .iqm-nav-link { color: rgba(255,255,255,.78) !important; border-radius: 8px; padding: .55rem .75rem !important; font-size: 13px; font-weight: 600; }
    .iqm-nav-link.active, .iqm-nav-link:hover { color: #fff !important; background: rgba(255,255,255,.12); }
    .iqm-shell { min-height: calc(100vh - 190px); }
    .iqm-container { max-width: 1140px; }
    .iqm-card { border: 1px solid #e8edf5; border-radius: 10px; box-shadow: 0 10px 28px rgba(15,39,66,.07); }
    .iqm-card-header { border-bottom: 1px solid #eef2f7; padding-bottom: .85rem; margin-bottom: 1rem; }
    .iqm-pill { border-radius: 999px; font-size: 11px; padding: .35rem .55rem; }
    .iqm-table th { font-size: 11px; letter-spacing: .02em; text-transform: uppercase; color: #64748b; white-space: nowrap; background: #f8fafc; }
    .iqm-table td { font-size: 13px; vertical-align: middle; }
    .iqm-summary-icon { width: 42px; height: 42px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; }
    .iqm-progress { height: 8px; background: #e8edf5; border-radius: 999px; overflow: hidden; }
    .iqm-progress-bar { height: 100%; border-radius: inherit; background: #2563eb; }
    .iqm-progress-lg { height: 14px; }
    .iqm-overview-metric { border: 1px solid #eef2f7; border-radius: 10px; padding: .85rem; height: 100%; background: #fbfdff; }
    .iqm-project-timeline { display: grid; grid-template-columns: repeat(5, minmax(82px, 1fr)); gap: 18px 10px; padding-bottom: 4px; }
    .iqm-project-step { position: relative; min-width: 0; text-align: center; }
    .iqm-project-step-marker { width: 32px; height: 32px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; border: 2px solid #cbd5e1; color: #64748b; background: #fff; font-size: 12px; font-weight: 700; }
    .iqm-project-step.completed .iqm-project-step-marker { border-color: #16a34a; background: #16a34a; color: #fff; }
    .iqm-project-step.active .iqm-project-step-marker { border-color: #f59e0b; background: #f59e0b; color: #172033; }
    .iqm-project-step-label { display: block; margin-top: .45rem; color: #64748b; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .iqm-project-step.completed .iqm-project-step-label, .iqm-project-step.active .iqm-project-step-label { color: #172033; }
    .iqm-timeline { position: relative; padding-left: 34px; }
    .iqm-timeline::before { content: ""; position: absolute; left: 13px; top: 8px; bottom: 8px; width: 2px; background: #e8edf5; }
    .iqm-timeline-item { position: relative; padding-bottom: 1rem; }
    .iqm-timeline-item:last-child { padding-bottom: 0; }
    .iqm-timeline-icon { position: absolute; left: -34px; top: 0; width: 28px; height: 28px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; box-shadow: 0 0 0 4px #fff; }
    .iqm-footer { background: #0f2742; color: rgba(255,255,255,.78); }
    .iqm-footer strong { color: #fff; }
    .bg-gradient-primary { background: #2563eb; color: #fff; }
    .bg-gradient-info { background: #0891b2; color: #fff; }
    .bg-gradient-warning { background: #f59e0b; color: #172033; }
    .bg-gradient-success { background: #16a34a; color: #fff; }
    .bg-gradient-danger { background: #dc2626; color: #fff; }
    .bg-gradient-secondary { background: #64748b; color: #fff; }
    .bg-gradient-dark { background: #111827; color: #fff; }
    .bg-gradient-light { background: #e2e8f0; color: #172033; }
    @media (max-width: 991.98px) {
      .iqm-navbar .navbar-nav { gap: 4px; padding-top: 14px; }
      .iqm-navbar form { padding-top: 10px; }
    }
    @media (max-width: 575.98px) {
      .iqm-container { padding-left: 14px; padding-right: 14px; }
      .iqm-table th, .iqm-table td { font-size: 12px; }
      .iqm-summary-icon { width: 36px; height: 36px; }
      .iqm-project-timeline { grid-template-columns: 1fr; overflow-x: visible; gap: 12px; }
      .iqm-project-step { display: flex; align-items: center; gap: 10px; text-align: left; min-width: 0; }
      .iqm-project-step-label { margin-top: 0; white-space: normal; }
    }
  </style>
  @stack('styles')
</head>
<body>
  @php
    $iqmDefaultLogoPath = public_path('web/images/logo.png');
    $iqmHasPrimaryLogo = $websiteSetting?->hasPrimaryLogo() ?? false;
    $iqmLogoUrl = $websiteSetting?->logoUrl() ?? asset('web/images/logo.png');
    $iqmLogoVersion = $iqmHasPrimaryLogo ? $websiteSetting->assetVersion() : (is_file($iqmDefaultLogoPath) ? filemtime($iqmDefaultLogoPath) : time());
    $iqmLogoAlt = $websiteSetting?->nama_perusahaan ?? $websiteSetting?->company_name ?? 'PT Bina Persada Jaya Sejahtera';
    $iqmNav = [
      ['label' => 'Dashboard', 'url' => route('iqm.dashboard'), 'pattern' => 'iqm/dashboard', 'icon' => 'fa-chart-line'],
      ['label' => 'Inquiry & Quotation', 'url' => route('iqm.inquiries.index'), 'pattern' => 'iqm/inquiries*', 'icon' => 'fa-file-lines'],
      ['label' => 'Project Report', 'url' => route('iqm.project-reports.index'), 'pattern' => 'iqm/project-reports*', 'icon' => 'fa-clipboard-list'],
      ['label' => 'Invoice Report', 'url' => route('iqm.invoice-reports.index'), 'pattern' => 'iqm/invoice-reports*', 'icon' => 'fa-file-invoice-dollar'],
      ['label' => 'Website Perusahaan', 'url' => url('/'), 'pattern' => null, 'icon' => 'fa-globe', 'target' => '_blank'],
      ['label' => 'Profile', 'url' => route('iqm.profile'), 'pattern' => 'iqm/profile', 'icon' => 'fa-user'],
    ];
  @endphp
  <nav class="navbar navbar-expand-lg navbar-dark iqm-navbar sticky-top">
    <div class="container iqm-container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('iqm.landing') }}">
        <img src="{{ $iqmLogoUrl }}?v={{ $iqmLogoVersion }}" class="iqm-brand-logo" alt="{{ $iqmLogoAlt }}" decoding="async">
        <span>IQM Portal</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#iqmNavbar" aria-controls="iqmNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="iqmNavbar">
        @auth('iqm')
          <ul class="navbar-nav ms-lg-auto me-lg-3 gap-lg-1">
            @foreach($iqmNav as $item)
              <li class="nav-item">
                <a
                  class="nav-link iqm-nav-link {{ ($item['pattern'] && request()->is($item['pattern'])) || (request()->is('iqm') && $item['label'] === 'Dashboard') ? 'active' : '' }}"
                  href="{{ $item['url'] }}"
                  @if(($item['target'] ?? null) === '_blank') target="_blank" rel="noopener" @endif
                >
                  <i class="fas {{ $item['icon'] }} me-1"></i>{{ $item['label'] }}
                </a>
              </li>
            @endforeach
          </ul>
          <form method="POST" action="{{ route('iqm.logout') }}" class="d-flex">
            @csrf
            <button class="btn btn-warning btn-sm fw-semibold"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
          </form>
        @else
          <ul class="navbar-nav ms-lg-auto gap-lg-1 pt-3 pt-lg-0 align-items-lg-center">
            <li class="nav-item">
              <a class="nav-link iqm-nav-link" href="{{ url('/') }}">Website</a>
            </li>
            <li class="nav-item">
              <a class="nav-link iqm-nav-link" href="{{ url('/contact') }}">Contact</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('iqm.login') }}" class="btn btn-warning btn-sm fw-semibold ms-lg-2 {{ request()->routeIs('iqm.login') ? 'active' : '' }}">Login</a>
            </li>
          </ul>
        @endauth
      </div>
    </div>
  </nav>

  <main class="iqm-shell">
    @yield('content')
  </main>

  <footer class="iqm-footer mt-5">
    <div class="container iqm-container py-4">
      <div class="row g-3 align-items-center">
        <div class="col-lg-6">
          <strong>© 2026 PT. Bina Persada Jaya Sejahtera.</strong>
          <span> All Rights Reserved.</span>
        </div>
        <div class="col-lg-6">
          <div class="d-flex flex-column flex-md-row gap-2 gap-md-3 justify-content-lg-end small">
            <span><i class="fas fa-envelope me-1"></i> info@binapersadajs.co.id</span>
            <span><i class="fas fa-phone me-1"></i> 021-2283-7556</span>
            <span><i class="fas fa-location-dot me-1"></i> Jakarta, Indonesia</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function () {
      if (!('serviceWorker' in navigator)) {
        return;
      }

      var reloadKey = 'binapersadajs-sw-v5-reloaded';
      var refreshing = false;

      navigator.serviceWorker.addEventListener('controllerchange', function () {
        if (refreshing || sessionStorage.getItem(reloadKey) === '1') {
          return;
        }

        refreshing = true;
        sessionStorage.setItem(reloadKey, '1');
        window.location.reload();
      });

      window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js?v=20260604').then(function (registration) {
          if (registration.waiting) {
            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
          }

          registration.addEventListener('updatefound', function () {
            var worker = registration.installing;

            if (!worker) {
              return;
            }

            worker.addEventListener('statechange', function () {
              if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                worker.postMessage({ type: 'SKIP_WAITING' });
              }
            });
          });
        });
      });
    })();
  </script>
  @stack('scripts')
</body>
</html>
