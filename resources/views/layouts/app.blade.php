<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/lightbox/css/lightbox.min.css') }}" rel="stylesheet">

    <!-- Tour page -->
    <link href="{{ asset('css/ptjs.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/classic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/personal.css') }}" rel="stylesheet">

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

        .loading{
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url({{asset('img/loader.gif')}}) 50% 50% no-repeat rgb(249,249,249);
            opacity: .8;
        }
    </style>
    @yield('styles')
</head>
<body>

<div id="app" class="wrapper">
    <div hidden>
        {{$company}}
    </div>
    <div id="loading" class="loading" hidden></div>
    @include('includes.navbar')
    <main class="">
        <div class="main-panel">
            <div class="content">
                <!-- Panel con gradient  -->
                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner py-5">
                    </div>
                </div>
                <!-- mt con -- margen negativo -->
                <div class="page-inner mt--5">
                    <div class="row mt--2" id="mensaje-alerta">
                        @if (session('info'))
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="alert alert-success">
                                            {{ session('info') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
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


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
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
<script src="{{ asset('assets/lightbox/js/lightbox.min.js')}}"></script>

<!-- Page Tour -->
{{--<script src="https://sucursal.correos.go.cr/js/ptjs.js"></script>--}}
<script src="{{ asset('/js/ptjs.min.js')}}"></script>
<script src="{{ asset('/js/ptjs.js')}}"></script>

<!-- Encriptar y desencriptar PHP base64 -->
<script src="https://cdn.jsdelivr.net/npm/js-base64@2.5.2/base64.min.js"></script>


<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '{{ config('services.facebook.app_id') }}',
            cookie     : true,
            xfbml      : true,
            version    : '{{ config('services.facebook.version') }}'
        });

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
        FB.AppEvents.logPageView();

    };
    function statusChangeCallback(response) {
        if (response.status === 'connected') {

        } else if (response.status === 'not_authorized') {
            window.location = "{{ route('facebook.index') }}";
        } else {
            window.location = "{{ route('facebook.index') }}";
        }
    }

    function checkLoginState() {

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    function validate(data) {
        let val=true;
        let keys=Object.entries(data);
        keys.forEach(e=>{
            if(!e[1]){
                let element= '<p class="text-danger" id="error'+e[0]+' " name="error">Digite una valor</p>';
                let bad=document.getElementsByName(e[0])[0];
                (!document.getElementById("error"+e[0])) ? $(bad).after(element):"";
                val=false;
            }
        });
        (!val) ? swal('Opss','Valores incompletos','error' ):"";
        return val;
    }

    function loadingPanel() {
        let loading =document.getElementById('loading');
        (loading.hasAttribute('hidden'))?loading.removeAttribute('hidden') :loading.setAttribute('hidden','');
    }

    function infoV() {
        let info=document.getElementById('info');
        (info.hasAttribute('hidden'))? info.removeAttribute('hidden'):info.setAttribute('hidden','');
    }

</script>


<script type="text/javaScript">
    if (screen.width < 1280){
        $(function () {
            $('#run.my-first-tour').on('click', function () {
                $.ptJs({
                    autoStart: true,
                    templateData: {
                        title: 'Cornelio'
                    },
                    steps: [
                        {
                            el: document,
                            modal: true,
                            templateData: {
                                content: 'El Tour es para brindarte una explicaci??n breve de las funcionalidades que pod??s realizar en ??sta' +
                                    ' nueva actualizaci??n. Desde el celular deb??s desplegar el men?? de la izquierda para poder ver los m??dulos.',
                                title: '??Bienvenido(a) al Tour de Cornelio!'
                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "P??ginas" aparecen todas las p??ginas que se encuentran como administrador en Facebook, la cual '+
                                         ' contar?? con la opci??n de crear publicaciones, observar todas las publicaciones, inscribir y desuscribir '+
                                         ' la p??gina.'

                                         /* 'En el m??dulo de "P??ginas" aparecen todas las p??ginas que se encuentra como administrador en Facebook y adem??s pod??s ' +
                                         ' crear publicaciones y observar todas las publicaciones que cuenta.'  */
                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Categor??a" pod??s administrar tipos de categor??as para todas tus diferentes empresas.'

                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Scrap" puedes obtener informaci??n de cualquier tipo de p??gina al ingresar el alias '+
                                         ' o el id de la p??gina que se encuentra en Facebook.'

                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Clasificaci??n" pod??s administrar todas las publicaciones, comentarios y conversaciones de una '+
                                         ' categor??a o ya sea de un contenido en espec??fico. '
                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Estadistica" puedes observar la interacci??n de una empresa, categor??a o contenido en un rango '+
                                         ' de fecha en espec??fico.'

                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Reporte" pod??s observar informe de categor??as, contenidos y temas. '
                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo de "Notificaci??n" pod??s gestionar las notificaciones que desees recibir.'
                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En esta zona pod??s administrar las actividades que cuenta cada usuario.'

                            }
                        },
                        {
                            el: document,
                            templateData: {
                                content: 'En el m??dulo donde se muestra la imagen del perfil, pod??s administrar tu perfil.'
                            }
                        }
                    ]
                });
            });
        });

    }else {
        $(function () {
            $('#run.my-first-tour').on('click', function () {
                $.ptJs({
                    autoStart: true,
                    templateData: {
                        title: 'Cornelio'
                    },
                    steps: [
                        {
                            el: document,
                            modal: true,
                            templateData: {
                                content: 'El Tour es para brindarte una explicaci??n breve de las funcionalidades que pod??s realizar en ??sta' +
                                    ' nueva actualizaci??n.',
                                title: '??Bienvenido(a) al Tour de Cornelio!'
                            }
                        },
                        {
                            el: '#element2.my-first-tour',
                            templateData: {
                                content: 'En esta zona aparecen todas las p??ginas que se encuentran como administrador en Facebook, la cual '+
                                         ' contar?? con la opci??n de crear publicaciones, observar todas las publicaciones, inscribir y desuscribir '+
                                         ' la p??gina.??'

                                        /* 'En esta zona aparecen todas las p??ginas que se encuentra como administrador en Facebook, la cual se contara con la opci??n de y adem??s pod??s '+
                                        ' crear publicaciones, observar todas las publicaciones de una p??gina en especifico, subcribir y desuscribir la p??gina.' */

                            }
                        },
                        {
                            el: '#element3.my-first-tour',
                            templateData: {
                                content: 'En esta zona pod??s administrar tipos de categor??as para todas tus diferentes empresas.'
                                         /* 'En esta zona pod??s encontrar todas las categor??as que se han creado, adem??s se contara con la '+'
                                         'opci??n de crear para una empresa exitente o nueva empresa una categor??a y su contenido en especifico.  */

                            }
                        },
                        {
                            el: '#element4.my-first-tour',
                            templateData: {
                                content: 'En esta zona puedes obtener informaci??n de cualquier tipo de p??gina al ingresar el alias '+
                                         ' o el id de la p??gina que se encuentra en Facebook.'
                                        /*administrar todas tus direcciones para entregas a domicilio.'*/
                            }
                        },
                        {
                            el: '#element5.my-first-tour',
                            templateData: {
                                content: 'En esta zona pod??s administrar todas las publicaciones, comentarios y conversaciones de una '+
                                         ' categor??a o ya sea de una p??gina en espec??fico. '

                            }
                        },
                        {
                            el: '#element6.my-first-tour',
                            templateData: {
                                content: 'En esta zona puedes observar la interacci??n de una empresa, categor??a o contenido en un rango '+
                                         ' de fecha en espec??fico.'
                            }
                        },
                        {
                            el: '#element7.my-first-tour',
                            templateData: {
                                content: 'En esta zona pod??s observar informe de categor??as, contenidos y temas. '
                                        /* 'En esta zona pod??s crear una gu??a para llegar con toda la documentaci??n ' +
                                        'lista a la sucursal, para que s??lo entregu??s el paquete y pagu??s el importe por el flete.' */
                            }
                        },
                        {
                            el: '#element8.my-first-tour',
                            templateData: {
                                content: 'En el m??dulo de "Notificaci??n" pod??s gestionar las notificaciones que desees recibir.'
                            }
                        },
                        {
                            el: '#element9.my-first-tour',
                            templateData: {
                                content: 'En esta zona pod??s administrar las actividades que cuenta cada usuario.'
                            }
                        },
                        {
                            el: '#element10.my-first-tour',
                            templateData: {
                                content: 'En esta zona pod??s administrar tu perfil.'
                            }
                        }
                    ]
                });
            });
        });
    }
</script>

@yield('script')
</body>
</html>
