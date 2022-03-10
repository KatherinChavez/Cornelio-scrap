@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="fw-bold">Reporte de los contenidos</h3></div>
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                    <div class="card-body">
                        <div class="form-row align-items-center">
                            <div class="col-sm-6 my-1">
                                <label class="sr-only" for="start">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-sm-6 my-1">
                                <label class="sr-only" for="end">Fecha Final</label>
                                <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('id','Seleccione el contenido *') }}
                            {{ Form::select('id',$contenido,null,['class'=>'form-control','placeholder'=>'Seleccione el contenido','required']) }}
                            {{--{{ Form::select('id',$Megacategoria,null,['class'=>'form-control','placeholder'=>'Seleccione la categoría','required']) }}--}}
                            <p class="text-danger">{{ $errors->first('description')}}</p>
                        </div>
                        <div class="form-group">
                            <button type="button" id="obtener" class="btn btn-sm btn-primary btn-round btn-block"
                                    onclick="seleccionar()">Obtener información
                            </button>
                        </div>
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

        function seleccionar() {
            let start_time = document.getElementById("start").value,
                end_time = document.getElementById("end").value,
                user_id = document.getElementById("user").value,
                contenido = document.getElementById("id").value,

                megaE = btoa(contenido),
                startE = btoa(start_time),
                endE = btoa(end_time);

            if(contenido){
                window.location.href = 'MegacategoriaReporte/' + megaE + '/' + startE + '/' + endE;
            }else{
                swal('Ops !', 'Por favor seleccione una categoría', 'warning');
            }
        }
    </script>
@endsection
