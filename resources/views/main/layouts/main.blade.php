<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('sipo_krisan/public/assets/images/ficon.png') }}">
    <title>SiPO - Krisanthium</title>

    @include('main.layouts.css')
</head>

<body class="fix-header fix-sidebar card-no-border logo-center">

    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                stroke-miterlimit="10" />
        </svg>
    </div>
    <div id="main-wrapper">

        @include('main.layouts.topbar')
        @include('main.layouts.topbar-nav')

        <div class="page-wrapper">

            <div class="container-fluid">
                @yield('content')
            </div>

            <footer class="footer">
                © 2024 - EDP Krisanthium
            </footer>

        </div>

    </div>

    @include('main.layouts.vendor-script')

    <!-- Bootstrap 4 JS (load once) -->
    <script src="{{ asset('sipo_krisan/public/news/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('sipo_krisan/public/news/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/waves.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/custom.min.js') }}"></script>

    <!-- Charts and Plugins -->
    <script src="{{ asset('sipo_krisan/public/news/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/raphael/raphael-min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/morrisjs/morris.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>

    {{-- @yield('scripts') --}}

</body>

</html>
