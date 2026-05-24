<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="theme-color" content="#0c1e35">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="BPJS Admin">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" sizes="192x192" href="/pwa/icons/icon-192x192.png">
  @if(!empty($websiteSetting?->favicon))
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $websiteSetting->favicon) }}">
  @else
    <link rel="icon" type="image/png" href="{{ asset('web/images/favicon.png') }}">
  @endif
  <title>{{ $websiteSetting?->nama_perusahaan ?? 'Bina Persada JS' }} - Panel Admin</title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="{{ asset('web/plugins/fontawesome/css/all.min.css') }}">
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css') }}?v=1.0.3" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

    <!--   Core JS Files   -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  @stack('rtl')
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

	  <!-- Github buttons -->
	  <script async defer src="https://buttons.github.io/buttons.js"></script>
	  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
	  <script src="{{ asset('assets/js/soft-ui-dashboard.min.js') }}?v=1.0.3"></script>
	  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	  @if(session('success'))
	    <script>
	      document.addEventListener('DOMContentLoaded', function () {
	        Swal.fire({
	          toast: true,
	          position: 'top-end',
	          icon: 'success',
	          title: @json(session('success')),
	          showConfirmButton: false,
	          timer: 2500,
	          timerProgressBar: true
	        });
	      });
	    </script>
	  @endif
	  @if(session('error'))
	    <script>
	      document.addEventListener('DOMContentLoaded', function () {
	        Swal.fire({
	          toast: true,
	          position: 'top-end',
	          icon: 'error',
	          title: @json(session('error')),
	          showConfirmButton: false,
	          timer: 3000,
	          timerProgressBar: true
	        });
	      });
	    </script>
	  @endif
	  @if(session('warning'))
	    <script>
	      document.addEventListener('DOMContentLoaded', function () {
	        Swal.fire({
	          toast: true,
	          position: 'top-end',
	          icon: 'warning',
	          title: @json(session('warning')),
	          showConfirmButton: false,
	          timer: 3000,
	          timerProgressBar: true
	        });
	      });
	    </script>
	  @endif
	  @if($errors->any())
	    <script>
	      document.addEventListener('DOMContentLoaded', function () {
	        Swal.fire({
	          toast: true,
	          position: 'top-end',
	          icon: 'error',
	          title: @json($errors->first()),
	          showConfirmButton: false,
	          timer: 3000,
	          timerProgressBar: true
	        });
	      });
	    </script>
	  @endif
	  <script>
	    document.addEventListener('DOMContentLoaded', function () {
	      document.querySelectorAll('.js-confirm-submit').forEach(function (form) {
	        form.addEventListener('submit', function (event) {
	          event.preventDefault();

	          Swal.fire({
	            title: 'Simpan perubahan?',
	            text: 'Pastikan data yang dimasukkan sudah benar.',
	            icon: 'question',
	            showCancelButton: true,
	            confirmButtonText: 'Ya, Simpan',
	            cancelButtonText: 'Batal',
	            reverseButtons: true
	          }).then(function (result) {
	            if (result.isConfirmed) {
	              form.submit();
	            }
	          });
	        });
	      });

	      document.querySelectorAll('.js-confirm-delete').forEach(function (form) {
	        form.addEventListener('submit', function (event) {
	          event.preventDefault();

	          Swal.fire({
	            title: 'Hapus data?',
	            text: 'Data yang dihapus tidak dapat dikembalikan.',
	            icon: 'warning',
	            showCancelButton: true,
	            confirmButtonText: 'Ya, Hapus',
	            cancelButtonText: 'Batal',
	            reverseButtons: true
	          }).then(function (result) {
	            if (result.isConfirmed) {
	              form.submit();
	            }
	          });
	        });
	      });
	    });
	  </script>
	  <script>
	    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js');
      });
    }
  </script>
</body>

</html>
