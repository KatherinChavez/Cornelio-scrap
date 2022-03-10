<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{--<link href="{{ asset('css/classic.min.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/ptjs.min.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/philately.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/lightbox/js/lightbox.min.js')}}"></script>

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
    <link rel="stylesheet" href="{{ asset('/assets/css/atlantis.css')}}">
    <style>
        .highcharts-credits{
            display: none;
        }
    </style>
    @yield('styles')
</head>
<body onload="mostrarSaludo()">
<div id="app" class="wrapper">
    <main class="">
        <div class="">
            <div class="content">
                <!-- mt con -- margen negativo -->
                <div class="page-inner">
                    <div class="row" id="mensaje-alerta">
                        @if (session('info'))
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="alert alert-success">
                                            {{ session('info') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
{{--<div id="overlay-processing" style="background: #F0F0F0; height: 100%; width: 100%; opacity: .7; padding-top: 10%; position: fixed; text-align: center; top: 0;z-index: 2147483647;" hidden>--}}
    {{--<br>--}}
    {{--<div class="spinner-border text-primary" role="status">--}}
        {{--<span class="sr-only">Procesando...</span>--}}
    {{--</div>--}}
{{--</div>--}}
<!--   Core JS Files   -->
{{--<script src="{{ asset('/assets/js/core/popper.min.js')}}"></script>--}}
<!-- jQuery UI -->
<script src="{{ asset('/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
<script src="{{ asset('/assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js')}}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>

<!-- Chart JS -->
<script src="{{ asset('/assets/js/plugin/chart.js/chart.min.js')}}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js')}}"></script>

<!-- Chart Circle -->
<script src="{{ asset('/assets/js/plugin/chart-circle/circles.min.js')}}"></script>

<!-- Datatables -->
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js')}}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js')}}"></script>

<!-- jQuery Vector Maps -->
{{--<script src="{{ asset('/assets/js/plugin/jqvmap/jquery.vmap.min.js')}}"></script>--}}
{{--<script src="{{ asset('/assets/js/plugin/jqvmap/maps/jquery.vmap.world.js')}}"></script>--}}

<!-- Sweet Alert -->
<script src="{{ asset('/assets/js/plugin/sweetalert/sweetalert.min.js')}}"></script>

<!-- Atlantis JS -->
<script src="{{ asset('/assets/js/atlantis.min.js')}}"></script>

<script src="{{ asset('/js/moment.js')}}"></script>

<script src="{{ asset('/js/ptjs.js')}}"></script>

<script src="{{ asset('/js/jquery.mask.js')}}"></script>

{{--<script type='text/javascript'>--}}
{{--document.oncontextmenu = function(){return false}--}}
{{--</script>--}}

@yield('script')
</body>
</html>
