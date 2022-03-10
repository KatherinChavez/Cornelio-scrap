<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>



    <!-- Fonts and icons -->
    <script src="{{ asset('/assets/js/plugin/webfont/webfont.min.js')}}"></script>
    <script>
        WebFont.load({
            google: {"families":["Lato:300,400,700,900"]},
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset("/assets/css/fonts.min.css")}}']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/atlantis.css')}}">


</head>
<body class="login">
<div id="app" class="wrapper wrapper-login wrapper-login-full p-0">
    <div class="login-aside w-50 d-flex flex-column align-items-center justify-content-center text-center bg-primary-gradient">
        <img class="mb-5" width="250px" src="{{ asset('img/logo.png')}}">
        <h1 class="title fw-bold text-white mb-3">Cornel.io</h1>
        <p class="subtitle text-white op-7">Accede a la herramienta Cornel.io</p>
    </div>
    <div class="login-aside w-50 d-flex align-items-center justify-content-center bg-white">
        <div class="container container-login container-transparent animated fadeIn">
            @yield('content')
        </div>
    </div>
</div>
@yield('script')
<!--   Core JS Files   -->
<script src="{{ asset('/assets/js/core/popper.min.js')}}"></script>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
<script src="{{ asset('/assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js')}}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js')}}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('/assets/js/plugin/sweetalert/sweetalert.min.js')}}"></script>

<!-- Atlantis JS -->
<script src="{{ asset('/assets/js/atlantis.min.js')}}"></script>

<script src="{{ asset('/js/jquery.mask.js')}}"></script>

</body>
</html>
