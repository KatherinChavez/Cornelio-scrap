@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Editar megacategoria</h4></div>
                <div class="card-body">
                    {!! Form::model($megacategorias, ['route' => ['megacategory.update',$company,$megacategorias->id],
                    'method' => 'PUT']) !!}
                        @include('Cornelio.Category.Megacategory.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-megacategorias').className+=' active';

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
