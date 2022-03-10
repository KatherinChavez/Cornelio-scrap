@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Crear subcategorías</h4></div>
                <div class="card-body">
                    {!! Form::open(['route' => ['subcategory.store',$company]]) !!}
                        @include('Cornelio.Category.Subcategory.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-sideCategorias').className+=' active';
    </script>
@endsection
