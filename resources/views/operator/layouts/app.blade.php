<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon / Logo --}}
    @if(!empty($logoAplikasi))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logoAplikasi) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('default-logo.png') }}">
    @endif

    <title id="dynamicTitle">{{ $namaAplikasi }}@hasSection('title') | @yield('title')@endif | </title>
    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let titleElement = document.getElementById("dynamicTitle");
        let originalTitle = titleElement.innerText;
        let space = "   "; // jarak antar loop
        let index = 0;

        setInterval(function () {
            // Geser teks
            let displayed = originalTitle.substring(index) + space + originalTitle.substring(0, index);
            titleElement.innerText = displayed;

            index++;
            if (index > originalTitle.length) index = 0;
        }, 250); // Kecepatan scroll (ms)
    });
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Boxicons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />

    <link rel="stylesheet" href="{{ asset('vendor/fonts/iconify-icons.css') }}" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/modern-admin.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('js/config.js') }}"></script>
</head>
<body>
  <!-- Layout Wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      
      <!-- Sidebar -->
      @include('operator.layouts.sidebar')

      <!-- Main Content -->
      <div class="layout-page">
        @include('operator.layouts.navbar')
        
        <!-- Page Content -->
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>

    </div>
  </div>
<!-- Core JS -->

    <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>

    <script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->

    <script src="{{ asset('js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('js/dashboards-analytics.js') }}"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false,
        didOpen: () => {
            const swalContainer = document.querySelector('.swal2-container');
            if(swalContainer) swalContainer.style.zIndex = '20000';
        }
    })
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        didOpen: () => {
            const swalContainer = document.querySelector('.swal2-container');
            if(swalContainer) swalContainer.style.zIndex = '20000';
        }
    })
</script>
@endif

@stack('scripts')
</body>
</html>
