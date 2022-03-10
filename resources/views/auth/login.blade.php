@extends('layouts.auth')

@section('content')
    <h3 class="text-center">{{ __('Iniciar sesión') }}</h3>
    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf
        <div class="form-group">
            <label for="email" class="placeholder"><b>{{ __('Correo electrónico') }}</b></label>
            <input id="email" name="email" type="text" placeholder="correos@ejemplo.com" class="form-control @error ('email') is-invalid @enderror" maxlength="100" value="{{ old('email') }}" tabindex=1 required>
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password" class="placeholder"><b>{{ __('Contraseña') }}</b></label>
            @if (Route::has('password.request'))
                <a class="link float-right" href="{{ route('password.request') }}">
                    {{ __('¿Has olvidado tú contraseña?') }}
                </a>
            @endif
            <div class="position-relative">
                <input id="password" name="password" type="password" class="form-control" maxlength="100" tabindex=2 required >
                <div class="show-password">
                    <i class="icon-eye"></i>
                </div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group form-action-d-flex mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rememberme" name="remember"  {{ old('remember') ? 'checked' : '' }} tabindex=3>
                <label class="custom-control-label m-0" for="rememberme">{{ __('Recuérdame') }}</label>
            </div>
            <button type="submit" class="btn btn-primary col-md-5 float-right mt-3 mt-sm-0 fw-bold">{{ __('Ingresar') }}</button>
        </div>
        <div class="login-account">
            <span class="msg">{{ __('¿No tienes una cuenta aún?') }}</span>
            <a href="{{ route('register') }}" class="link" tabindex=4>{{ __('Regístrate') }}</a>
        </div>
    </form>
@endsection
