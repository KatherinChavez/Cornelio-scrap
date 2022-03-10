@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        Clasificar Publicaciones
                        <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione la página que desea obtener las publicaciones">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h4>
                </div>
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body" >
                    <h3>Obtener publicaciones por:</h3>
                    <div class="form-control">

                    <input type="radio" id="Pagina" name="por" value="Pagina" class="form-radio-input" checked>
                    <label for="Pagina">Página</label><br>
                    <input type="radio" id="Contenido" name="por" value="Contenido" class="form-radio">
                    <label for="Contenido">Contenido</label><br>
                    </div>

                </div>
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
                            {{--<button type="button" id="seleccionar" class="btn btn-sm btn-outline-primary" onclick="setData()">Obtener publicaciones</button>--}}

                            <button class="btn btn-sm btn-block btn-primary btn-round" id="seleccionar" type="button"
                                    onclick="setData()">Obtener publicaciones
                                    {{--onclick="cargarPost()">Obtener contenidos--}}
                            </button>

                        </div>
                    </div>
                    <div class="card-body" id="porContenido" hidden>
                        <div class="form-group">
                            {{ Form::label('categoria_id','Seleccione el contenido *') }}
                            {{ Form::select('categoria_id',$categories,null,['class'=>'form-control col-12','placeholder'=>'Seleccione el contenido de la categoria','required']) }}
                            <p class="text-danger">{{ $errors->first('description')}}</p>
                        </div>
                        <div class="form-group">
                            {{--<button type="button" id="seleccionar" class="btn btn-sm btn-outline-success" onclick="cargarPost()">Obtener contenidos</button>--}}
                            <button class="btn btn-sm btn-block btn-primary btn-round" id="seleccionar" type="button"
                                    onclick="cargarPost()">Obtener contenidos
                            </button>

                        </div>
                        <div id="section-posts" class="row justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function()
    {
        $("input[name=por]").click(function () {

            if($(this).val()=='Pagina'){
                document.getElementById("porContenido").hidden=true;
                document.getElementById("porPublicacion").hidden=false;
            }else{
                document.getElementById("porContenido").hidden=false;
                document.getElementById("porPublicacion").hidden=true;
            }
//        alert("La edad seleccionada es: " + $('input:radio[name=por]:checked').val());
//        alert("La edad seleccionada es: " + $(this).val());
    });
    });

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
        let start_time=document.getElementById("start").value,
            end_time=document.getElementById("end").value,
            user_id=document.getElementById("user").value,
            pagina_id=document.getElementById("page_id").value;
        if(pagina_id===''){
            swal('Opss','por favor seleccione una página','warning');
            return false;
        }
        window.location="{{ route('InfoPage.InfoPage') }}?id="+pagina_id+"&inicio="+start_time+"&final="+end_time+" ";
    }

    function cargarPost() {
        let datos = {},
            categoria=document.getElementById("categoria_id").value;
        user=document.getElementById("user").value;
        inicio=document.getElementById("start").value;
        final=document.getElementById("end").value;
        datos = {categoria,user,inicio,final};
        if (datos.categoria===''){
            swal('Opss','por favor seleccione un contenido','warning');
            return false;
        }

        window.location="{{ route('ClassifyCategory.PostCategory') }}?id="+categoria+"&inicio="+inicio+"&final="+final+" ";

    }
</script>
@endsection
