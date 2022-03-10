@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Crear un nuevo usuario para la empresa</h4></div>
                <div class="card-body">
                    {!! Form::open(['route' => 'users.store']) !!}
                        @include('admin.users.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-company').className+=' active';
    </script>
@endsection
