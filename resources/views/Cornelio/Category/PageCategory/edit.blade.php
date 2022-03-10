@extends('layouts.app')

@section('content')

<div class="col-lg-12">
    <div class="justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4>Editar Categoría</h4></div>
                <input type="text" value="{{$categorias->id}}" id="idcategoria" name="idcategoria" hidden>
                <div class="card-body">
                    {!! Form::model($categorias, ['route' => ['Category.update',$company,$categorias->id],
                    'method' => 'PUT']) !!}
                    @include('Cornelio.Category.PageCategory.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-categorias').className+=' active';

        function statusChangeCallback(response) {
            if (response.status === 'connected') {

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }
    </script>
@endsection
