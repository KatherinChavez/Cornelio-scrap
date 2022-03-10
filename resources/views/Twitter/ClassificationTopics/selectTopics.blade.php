@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{--Se obtiene toda la informacion de las subcategoria, pero ahora sera conocida como tema--}}
                        <h4>
                            Clasificación de los temas
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione una etiqueta, permitiendo obtener una informacción de todas las publicaciones relaccionada">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h4>
                    </div>
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">

                    <div class="card-body table-responsive">
                        <div class="form-row align-items-center">
                            <div class="col-sm-6">
                                <label class="sr-only" for="start">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start" value="{{\Carbon\Carbon::yesterday()->format('Y-m-d')}}">
                            </div>
                            <div class="col-sm-6">
                                <label class="sr-only" for="end">Fecha Final</label>
                                <input type="date" class="form-control" id="end" value="{{\Carbon\Carbon::tomorrow()->format('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                {{ Form::label('subcategory_id','Seleccione un tema *') }}
                                {{ Form::select('subcategory_id',$topics,null,['class'=>'form-control','placeholder'=>'Seleccione el tema','required'=>'required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-block btn-primary btn-round" id="seleccionar" type="button" onclick="loadTopics()">Obtener publicaciones</button>
                            </div>
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
                //isLogedIn();
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }
        function loadTopics() {
            let start_time = document.getElementById("start").value,
                end_time   = document.getElementById("end").value,
                subcategoria = document.getElementById("subcategory_id").value;
            if(subcategoria){
                window.location = "{{ route('classificarionTwitter.getTopics') }}?topics_id="+Base64.encode(subcategoria)+"&inicio="+Base64.encode(start_time)+"&final="+Base64.encode(end_time)+" ";
            }
            else{
                swal('Opss ! ', 'Por favor seleccione un tema', 'warning');
            }
        }
    </script>
@endsection