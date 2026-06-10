<!-- Navbar -->
<style>
    .admin-notification-menu { width: min(360px, calc(100vw - 28px)); max-height: 430px; overflow-y: auto; }
    .admin-notification-trigger { position: relative; line-height: 1; }
    .admin-notification-badge { position: absolute; top: -7px; right: -9px; min-width: 18px; height: 18px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; padding: 0 5px; }
    .admin-notification-icon { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 34px; }
    .admin-notification-text { min-width: 0; }
    .admin-notification-description { white-space: normal; overflow-wrap: anywhere; }
</style>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('paneladmin.dashboard') }}">Panel Admin</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">{{ str_replace('-', ' ', trim(Request::path(), '/')) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', last(explode('/', trim(Request::path(), '/')))) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <div class="ms-md-3 pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Cari...">
                </div>
            </div>
            <ul class="navbar-nav justify-content-end align-items-center">
                @php($adminNotifications = $adminNotifications ?? ['totalUnread' => 0, 'latestNotifications' => collect()])
                <li class="nav-item dropdown d-flex align-items-center me-3">
                    <a href="#" class="nav-link text-body p-0 admin-notification-trigger" id="adminNotificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi admin">
                        <i class="fa fa-bell"></i>
                        @if(($adminNotifications['totalUnread'] ?? 0) > 0)
                            <span class="admin-notification-badge bg-gradient-danger text-white">
                                {{ ($adminNotifications['totalUnread'] ?? 0) > 99 ? '99+' : $adminNotifications['totalUnread'] }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0 admin-notification-menu" aria-labelledby="adminNotificationDropdown">
                        <div class="px-3 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <h6 class="mb-0">Notifikasi</h6>
                                <span class="badge bg-gradient-danger">{{ $adminNotifications['totalUnread'] ?? 0 }} baru</span>
                            </div>
                        </div>
                        @forelse(($adminNotifications['latestNotifications'] ?? collect()) as $notification)
                            <a href="{{ $notification['url'] }}" class="dropdown-item border-bottom py-3">
                                <div class="d-flex gap-3">
                                    <span class="admin-notification-icon {{ $notification['badge_color'] }}">
                                        <i class="fas {{ $notification['icon'] }} text-white text-sm"></i>
                                    </span>
                                    <span class="admin-notification-text">
                                        <span class="d-block text-sm font-weight-bold text-dark">{{ $notification['title'] }}</span>
                                        <span class="d-block text-xs text-secondary admin-notification-description">{{ $notification['description'] }}</span>
                                        <span class="d-block text-xxs text-muted mt-1">{{ optional($notification['time'])->diffForHumans() }}</span>
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-3 py-4 text-center text-secondary text-sm">Tidak ada notifikasi baru.</div>
                        @endforelse
                    </div>
                </li>
                <li class="nav-item d-flex align-items-center me-3">
                    <a href="{{ route('paneladmin.profile') }}" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user-circle me-sm-1"></i>
                        <span class="d-sm-inline d-none">{{ auth()->user()->name }}</span>
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <form method="POST" action="{{ route('paneladmin.logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="nav-link text-body font-weight-bold px-0 bg-transparent border-0">
                            <i class="fa fa-sign-out-alt me-sm-1"></i>
                            <span class="d-sm-inline d-none">Keluar</span>
                        </button>
                    </form>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
