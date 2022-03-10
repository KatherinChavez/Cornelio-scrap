@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="alert alert-success invisible" id="alerta" role="alert">
                        <p id="status"></p>
                    </div>

                    <div class="card-header" align="center">
                        <h1> Facebook</h1>
                    </div>

                    <div class="card-body ">
                        <div class="row justify-content-center">
                            <h4 class="justify-content-center">Por favor, iniciar sesión con Facebook para administrar
                                tu página.</h4>
                        </div>

                        <div class="row justify-content-center">
                            {{--<fb:login-button--}}
                                    {{--scope="public_profile,pages_messaging,pages_show_list,email"--}}
                                    {{--onlogin="checkLoginState();">Iniciar Sesión--}}
                            {{--</fb:login-button>--}}

                            <fb:login-button scope="public_profile,pages_messaging,pages_show_list,email" onlogin="checkLoginState();">Iniciar sesion
                            </fb:login-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function statusChangeCallback(response) {
//            alert('redireccion 1');
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                $('#alerta').removeClass('invisible').addClass('visible');
                document.getElementById('status').innerHTML = 'Inicio de sesión exitoso.';
                window.location = "{{ route('companies') }}";
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                document.getElementById('status').innerHTML = 'Por favor registrese en la aplicacion ';
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                document.getElementById('status').innerHTML = 'Login with Facebook.';
            }
        }
    </script>
@endsection
