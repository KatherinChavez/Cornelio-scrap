@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
{{--                <div class="card-header">Permiso</div>--}}
                <div class="card-body">
                    {!! Form::model($companies, ['route' => ['companies.update',$companies->id],
                    'method' => 'PUT']) !!}
                        @include('admin.companies.partials.form')
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
        document.getElementById('nombre').addEventListener('keyup', () =>{
            let name=document.getElementById('nombre').value;
            document.getElementById('slug').value=slugify(name);
        });
    </script>
@endsection
