@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Editar palabra</h4></div>
                <div class="card-body">
                    {!! Form::model($words, ['route' => ['words.update',$company,$words->id],
                    'method' => 'PUT']) !!}
                        @include('Cornelio.Category.Words.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-words').className+=' active';
    </script>
@endsection