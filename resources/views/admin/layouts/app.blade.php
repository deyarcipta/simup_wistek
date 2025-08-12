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

    <link rel="stylesheet" href="{{ asset('vendor/fonts/iconify-icons.css') }}" />
  
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('js/config.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

</head>
<body>
  <!-- Layout Wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      
      <!-- Sidebar -->
      @include('admin.layouts.sidebar')

      <!-- Main Content -->
      <div class="layout-page">
        @include('admin.layouts.navbar')
        
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
  @stack('scripts')
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
</body>
</html>
