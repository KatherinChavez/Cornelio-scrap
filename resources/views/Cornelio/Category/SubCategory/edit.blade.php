@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Editar subcategorías</h4></div>
                <div class="card-body">
                    {!! Form::model($subcategorias, ['route' => ['subcategory.update',$company,$subcategorias->id],
                    'method' => 'PUT']) !!}
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
        document.getElementById('nav-subcategorias').className+=' active';
    </script>
@endsection
