@extends('layouts.auth')

@section('content')

    <h3 class="text-center">{{ __('Restablezca su contraseña') }}</h3>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email" class="placeholder">{{ __('Correo electrónico *') }}</label>

            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="form-group">
            <label for="password" class="placeholder">{{ __('Contraseña *') }}</label>

            <div class="position-relative">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
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

        <div class="form-group">
            <label for="password-confirm" class="placeholdert">{{ __('Confirmar contraseña *') }}</label>

            <div class="position-relative">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                <div class="show-password">
                    <i class="icon-eye"></i>
                </div>
            </div>
        </div>

        <div class="row form-action">
            <div class="col-md-6">
                <a href="{{ route( 'login')}}" id="show-signin" class="btn btn-danger btn-link w-100 fw-bold">{{ __('Cancelar') }}</a>
            </div>
            <div class="col-md-6" align="right">
                <button type="submit" class="btn btn-success w-100 fw-bold" >
                    {{ __('Actualizar contraseña') }}
                </button>
            </div>
        </div>
    </form>
@endsection
