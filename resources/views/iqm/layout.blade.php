<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'IQM Portal') - PT Bina Persada JS</title>
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
    .iqm-logo { max-height: 48px; object-fit: contain; }
    .iqm-shell { min-height: 100vh; }
    .table th { font-size: 12px; text-transform: uppercase; color: #64748b; white-space: nowrap; }
    .table td { font-size: 13px; vertical-align: middle; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg iqm-navbar navbar-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('iqm.landing') }}">
        <img src="{{ ($websiteSetting ?? null)?->logoUrl() ?? asset('web/images/logo.png') }}" class="iqm-logo" alt="Bina Persada JS">
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
</body>
</html>
