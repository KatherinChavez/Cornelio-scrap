@extends('layouts.auth')
@section('content')
    <div class="container container-login container-transparent animated fadeIn">
        <h3 class="text-center">{{ __('Regístrate') }}</h3>
        <form class="login-form"  method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name" class="placeholder"><b>{{ __('Nombre *') }}</b></label>
                <input id="name" type="text" maxlength="20" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus tabindex=2>
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="last_name" class="placeholder"><b>{{ __('Primer apellido *') }}</b></label>
                <input id="last_name" type="text" maxlength="20" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" required autofocus tabindex=3>
                @if ($errors->has('last_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="sec_last_name" class="placeholder"><b>{{ __('Segundo apellido') }}</b></label>
                <input id="sec_last_name" type="text" maxlength="20" class="form-control{{ $errors->has('sec_last_name') ? ' is-invalid' : '' }}" name="sec_last_name" value="{{ old('sec_last_name') }}" autofocus tabindex=4>
                @if ($errors->has('sec_last_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sec_last_name') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="email" class="placeholder"><b>{{ __('Correo electrónico *') }}</b></label>
                <input id="email" type="email" maxlength="30" placeholder="correos@ejemplo.com" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required tabindex=5>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
{{--            <div class="form-group">--}}
{{--                <label for="phone" class="placeholder"><b>{{ __('Telefono Movil *') }}</b></label>--}}
{{--                <input id="phone" type="text" maxlength="11" placeholder="87654321" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" required tabindex=6>--}}
{{--                @if ($errors->has('phone'))--}}
{{--                    <span class="invalid-feedback" role="alert">--}}
{{--                        <strong>{{ $errors->first('phone') }}</strong>--}}
{{--                    </span>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <label for="telegram" class="placeholder"><b>{{ __('Telegram Username') }}</b></label>--}}
{{--                <input id="telegram" type="text" maxlength="12" placeholder="UserName" class="form-control{{ $errors->has('telegram') ? ' is-invalid' : '' }}" name="telegram" value="{{ old('telegram') }}" tabindex=7>--}}
{{--                @if ($errors->has('telegram'))--}}
{{--                    <span class="invalid-feedback" role="alert">--}}
{{--                        <strong>{{ $errors->first('telegram') }}</strong>--}}
{{--                    </span>--}}
{{--                @endif--}}
{{--            </div>--}}
            <div class="form-group">
                <label for="password" class="placeholder"><b>{{ __('Contraseña *') }}</b></label>
                <div class="position-relative">
                    <input id="password" type="password" maxlength="100" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}" tabindex=8  required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <div class="show-password">
                        <i class="icon-eye"></i>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="placeholder"><b>{{ __('Confirmar contraseña *') }}</b></label>
                <div class="position-relative">
                    <input id="password_confirmation" type="password" maxlength="100" class="form-control" name="password_confirmation" onpaste="return false" tabindex=9 required>
                    <div class="show-password">
                        <i class="icon-eye"></i>
                    </div>
                </div>
            </div>
            <div class="login-account">
                <span class="msg">{{ __('¿Ya tienes una cuenta?') }}</span>
                <a href="{{ route('login') }}" class="link">{{ __('Inicia sesión') }}</a>
            </div>
            <div class="row form-action">
                <div class="col-md-6">
                    <a href="{{ route( 'login')}}" id="show-signin" class="btn btn-danger btn-link w-100 fw-bold">{{ __('Cancelar') }}</a>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success w-100 fw-bold" tabindex=10>{{ __('Registrarse') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $("#name").on('keypress', function(e) {
                var regex = new RegExp("^[a-zA-Z ]*$");
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        });

    </script>
@endsection

