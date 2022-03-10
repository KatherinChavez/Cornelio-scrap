{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--<div class="container">--}}
    {{--<div class="row justify-content-center">--}}
        {{--<div class="col-md-8">--}}
            {{--<div class="card">--}}
                {{--<div class="card-header"><h4>Detalle del usuario</h4></div>--}}
                {{--<div class="card-body">--}}
                    {{--<p><strong>Nombre: </strong>{{ $user->name }} {{ $user->last_name }} {{ $user->sec_last_name }}</p>--}}
                    {{--<p><strong>Email: </strong>{{ $user->email }}</p>--}}
                    {{--<p><strong>Fecha creación: </strong>{{ $user->created_at }}</p>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}
{{--@section('script')--}}
    {{--<script>--}}
        {{--document.getElementById('nav-agencia').className+=' active';--}}
    {{--</script>--}}
{{--@endsection--}}


@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4>Detalle del usuario</h4></div>
                    <div class="card-body">
                        <p><strong>Nombre: </strong>{{ $user->name }} {{ $user->last_name }} {{ $user->sec_last_name }}</p>
                        <p><strong>Email: </strong>{{ $user->email }}</p>
                        <p><strong>Fecha creación: </strong>{{ $user->created_at }}</p>

                        @forelse($user->companies as $c)
                            <p><strong>Compañia: </strong>{{ $c->nombre }}</p>
                        @empty
                            <p><b>Solicitar acceso a Compañia</b></p>

                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-agencia').className+=' active';
    </script>
@endsection
