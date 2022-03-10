@extends('layouts.auth')

@section('content')

<h3 class="text-center">{{ __('Confirmar contraseña') }}</h3>


{{ __('Confirme su contraseña antes de continuar.') }}

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group">
        <label for="password" class="placeholder">{{ __('Contraseña') }}</label>

        <div class="position-relative">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            <div class="show-password">
                <i class="icon-eye"></i>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row form-action">
        <div class="col-md-6">
            <a href="{{ route( 'login')}}" id="show-signin" class="btn btn-danger btn-link w-100 fw-bold">{{ __('Cancelar') }}</a>
        </div>
        <div class="col-md-6" align="right">
            <button type="submit" class="btn btn-primary">
                {{ __('Confirm Password') }}
            </button>
            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </div>
    </div>
</form>

@endsection
