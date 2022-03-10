@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Clasificación de Comentarios</h4>
                <p id="info" hidden>Clasifica las páginas según un criterio</p>
                </div>
                <div class="card-body table-responsive">
                    <input id="user"type="hidden" value="{{ Auth::user()->id }}">
                    <div class="card-body table-responsive">
                        <div class="form-group">

                            <div class="form-group">
                                {{ Form::label('page_id','Seleccione la página  *') }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Clasificar comentario" data-content="Seleccione la una página para clasificar los comentarios de las conversaciones">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                {{ Form::select('page_id',$page,null,['class'=>'form-control col-lg-12','placeholder'=>'Seleccione la página','required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>

                        </div>

                        <div class="form-group">
                            <button class="btn btn-sm btn-block btn-primary btn-round" id="seleccionar" type="button"
                                    onclick="setData()">Ver Comentarios
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
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function setData() {
            let pagina_id=document.getElementById("page_id").value;
            if(pagina_id!==''){
                window.location="{{ route('ClassifyFeeling.selectSentiment') }}?page_id="+pagina_id+" ";
            }else{
                swal('Opss','por favor seleccione una página','warning');
            }
        }



    </script>
@endsection
