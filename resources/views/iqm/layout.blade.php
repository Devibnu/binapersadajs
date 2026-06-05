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
    body { font-family: Inter, system-ui, sans-serif; background: #f6f8fb; color: #172033; }
    .iqm-navbar { background: #0f2742; }
    .iqm-card { border: 0; border-radius: 16px; box-shadow: 0 14px 40px rgba(15, 39, 66, .08); }
    .iqm-pill { border-radius: 999px; font-size: 12px; padding: .4rem .65rem; }
    .iqm-logo { display: block; max-height: 48px; max-width: 210px; object-fit: contain; width: auto; }
    .iqm-shell { min-height: 100vh; }
    .table th { font-size: 12px; text-transform: uppercase; color: #64748b; white-space: nowrap; }
    .table td { font-size: 13px; vertical-align: middle; }
  </style>
</head>
<body>
  @php
    $iqmDefaultLogoPath = public_path('web/images/logo.png');
    $iqmHasPrimaryLogo = $websiteSetting?->hasPrimaryLogo() ?? false;
    $iqmLogoUrl = $websiteSetting?->logoUrl() ?? asset('web/images/logo.png');
    $iqmLogoVersion = $iqmHasPrimaryLogo ? $websiteSetting->assetVersion() : (is_file($iqmDefaultLogoPath) ? filemtime($iqmDefaultLogoPath) : time());
    $iqmLogoAlt = $websiteSetting?->nama_perusahaan ?? 'PT Bina Persada JS';
  @endphp
  <nav class="navbar navbar-expand-lg iqm-navbar navbar-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('iqm.landing') }}">
        <img src="{{ $iqmLogoUrl }}?v={{ $iqmLogoVersion }}" class="iqm-logo" alt="{{ $iqmLogoAlt }}" decoding="async">
        <span>IQM Portal</span>
      </a>
      <div class="ms-auto d-flex gap-2">
        @auth('iqm')
          <a href="{{ route('iqm.dashboard') }}" class="btn btn-sm btn-outline-light">Dashboard</a>
          <form method="POST" action="{{ route('iqm.logout') }}">@csrf <button class="btn btn-sm btn-warning">Logout</button></form>
        @else
          <a href="{{ route('iqm.login') }}" class="btn btn-sm btn-warning">Login</a>
        @endauth
      </div>
    </div>
  </nav>
  <main class="iqm-shell">@yield('content')</main>
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
</body>
</html>
