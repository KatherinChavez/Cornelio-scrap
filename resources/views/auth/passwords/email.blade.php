@extends('layouts.auth')

@section('content')
    <h3 class="text-center">{{ __('Restablezca su contraseña') }}</h3>


    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="placeholder">{{ __('Correo electrónico') }}</label>

            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>
        <div class="row form-action">
            <div class="col-md-6">
                <a href="{{ route( 'login')}}" id="show-signin" class="btn btn-danger btn-link w-100 fw-bold">{{ __('Cancelar') }}</a>
            </div>
            <div class="col-md-6" align="right">
                <button type="submit" class="btn btn-primary w-100 fw-bold" >
                    {{ __('Enviar') }}
                </button>
            </div>
        </div>
    </form>
@endsection
