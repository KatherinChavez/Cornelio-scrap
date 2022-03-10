@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        Clasificación de categoría
                        <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione el tipo de categoría que desea obtener en un rango de fecha">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h4>
                </div>

                <div class="card-body">
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                    <div class="form-row align-items-center">
                        <div class="col-sm-6">
                            <label class="sr-only" for="start">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="col-sm-6">
                            <label class="sr-only" for="end">Fecha Final</label>
                            <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        </br></br>
                        {{ Form::label('categoria_id','Seleccione la categoría *') }}
                        {{ Form::select('categoria_id',$categories,null,['class'=>'form-control','placeholder'=>'Seleccione la categoría','required']) }}
                        <p class="text-danger">{{ $errors->first('description')}}</p>
                    </div>

                    <div class="form-group">
                        <button type="button" id="seleccionar" class="btn btn-sm btn-outline-primary" onclick="cargarPost()">Obtener categoría</button>
                    </div>
                    <div id="section-posts" class="row justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
    <script>
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function cargarPost() {
            let datos = {},
                categoria=document.getElementById("categoria_id").value;
                user=document.getElementById("user").value;
                inicio=document.getElementById("start").value;
                final=document.getElementById("end").value;
            datos = {categoria,user,inicio,final};

            window.location="{{ route('ClassifyCategory.PostCategory') }}?id="+categoria+"&inicio="+inicio+"&final="+final+" ";

            {{--axios.post('{{ route('ClassifyCategory.SelectCategory') }}', datos).then(response => {--}}
                {{--window.location="{{ route('ClassifyCategory.PostCategory') }}?id="+categoria+"&inicio="+inicio+"&final="+final+" ";--}}
            {{--});--}}
        }
    </script>

@endsection