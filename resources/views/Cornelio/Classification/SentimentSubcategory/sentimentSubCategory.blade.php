@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{--Se obtiene toda la informacion de las subcategoria, pero ahora sera conocida como etiqueta--}}
                    <h4>
                        Sentimiento de los temas
                        <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione una etiqueta, permitiendo obtener una informacción de todas las publicaciones relaccionada">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h4>
                </div>
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body table-responsive">
                    <div class="form-group">
                        {{ Form::label('subcategory_id','Seleccione un tema *') }}
                        {{ Form::select('subcategory_id',$subcategory,null,['class'=>'form-control','placeholder'=>'Seleccione el tema','required'=>'required']) }}
                        <p class="text-danger">{{ $errors->first('description')}}</p>
                    </div> 
                    <div class="form-group">
                        {{--<button type="button" id="obtener" class="btn btn-sm btn-outline-primary" onclick="seleccionar()">Obtener tema</button>--}}
                        <button class="btn btn-sm btn-block btn-primary btn-round" id="obtener" type="button"
                                onclick="seleccionar()">Obtener tema
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
            //isLogedIn();
        } else if (response.status === 'not_authorized') {
             window.location = "{{ route('facebook.index',$company) }}";
        } else {
            window.location = "{{ route('facebook.index',$company) }}";
        }
    }
    function seleccionar() {
        var subcategoria = document.getElementById("subcategory_id").value;
        if(subcategoria){
            window.location = "{{ route('ClassifyCategory.CategorySentiment') }}?categoria="+subcategoria+" " ;
        }
        else{
            swal('Opss ! ', 'Por favor seleccione un tema', 'warning');
        }
        //window.location = "{{ route('ClassifyCategory.CategorySentiment') }}?categoria="+subcategoria+" " ;
    }
</script>
@endsection