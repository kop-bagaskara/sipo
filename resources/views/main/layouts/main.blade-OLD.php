<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | KOP - PLAN PRODUCTION </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="shortcut icon" href="{{ asset('assets/images/ficon.png') }}">

    @include('main.layouts.css')

</head>

@yield('body')

<div id="layout-wrapper">

    <input type="text" name="csrf_tokens" id="csrf_tokens" value="{{ csrf_token() }}" hidden>

    <div class="main-content">

        @include('main.layouts.topbar')

        @include('main.layouts.topbar-nav')

        <br>
        <div class="page-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('main.layouts.footer')

    </div>
</div>

@include('main.layouts.vendor-script')

</body>

</html>
