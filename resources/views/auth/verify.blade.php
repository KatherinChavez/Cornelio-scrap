@extends('layouts.auth')

@section('content')

<h3 class="text-center">{{ __('Verificar email') }}</h3>

@if (session('resent'))
    <div class="alert alert-success" role="alert">
        {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico..') }}
    </div>
@endif

{{ __('Antes de continuar, consulte su correo electrónico para ver si hay un enlace de verificación.') }}
{{ __('Si no recibió el correo electrónico') }},
<form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('haga clic aquí para solicitar otro') }}</button>.
</form>

@endsection
