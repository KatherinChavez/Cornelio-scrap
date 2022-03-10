@extends('layouts.app')
@section('content')

<div class="col-lg-12">
    <div class="justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h3 class="fw-bold">Crear nuevo contenido</h3></div>
{{--                <div class="card-body">--}}
{{--                    {!! Form::open() !!}--}}
{{--                        @include('Cornelio.Category.PageCategory.partials.form')--}}
{{--                    {!! Form::close() !!}--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-categorias').className+=' active';
    </script>
@endsection
