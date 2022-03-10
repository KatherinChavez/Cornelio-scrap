@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Crear una nueva empresa</h4></div>
                <div class="card-body">
                    {!! Form::open(['route' => 'companies.store']) !!}
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

        function guardar() {
            swal.fire({
                icon: 'success',
                text: 'Por favor proceda a agregar usuarios a la compañia',
                footer: '<a href>Why do I have this issue?</a>'
            })

        }
    </script>
@endsection
