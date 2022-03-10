@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Crear clasificación de palabra</h4></div>
                <div class="card-body">
                    {!! Form::open(['route' => ['ClassificationWord.store',$company]]) !!}
                        @include('Cornelio.Classification.Word.partials.form')
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
