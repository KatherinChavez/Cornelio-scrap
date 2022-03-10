@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            Clasificar comentarios de las publicaciones de Twitter
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione la página que deseas obtener las publicaciones de twitter para clasificar los comentarios">
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
                        <div class="card-body" id="porPublicacion">
                            <div class="form-group">
                                {{ Form::label('page_id','Seleccione la página  *') }}
                                {{ Form::select('page_id',$page,null,['class'=>'form-control col-12','placeholder'=>'Seleccione la página','required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-block btn-primary btn-round" id="seleccionar" type="button"
                                        onclick="setData()">Obtener publicaciones
                                </button>
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

        function setData() {
            let start_time= document.getElementById("start").value,
                end_time  = document.getElementById("end").value,
                pagina_id = document.getElementById("page_id").value;
            if(pagina_id === ''){
                swal('Opss','por favor seleccione una página','warning');
                return false;
            }
            window.location="{{ route('classificarionTwitter.getComment') }}?id="+Base64.encode(pagina_id)+"&inicio="+Base64.encode(start_time)+"&final="+Base64.encode(end_time)+" ";
        }

    </script>
@endsection
