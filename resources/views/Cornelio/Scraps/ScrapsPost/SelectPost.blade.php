@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Scrap Post</h4></div>
                <div class="card-body table-responsive">
                <div class="form-group">
                        {{ Form::label('page_id','Buscar id de página *') }}
                        {{ Form::text('page_id',null,['class' => 'form-control', 'maxlength' => '80']) }}
                        <p class="text-danger">{{ $errors->first('page_id')}}</p>
                    </div>
                    <div class="form-group">
                        {{ Form::label('post','Post de la página') }}
                        {{ Form::text('post',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('page_name','Nombre de página') }}
                        {{ Form::text('page_name',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                    </div>
                    
                    <div class="form-group">
                        {{ Form::label('categoria_id','Seleccione la categoría *') }}
                        {{ Form::select('categoria_id',$categories,null,['class'=>'form-control','placeholder'=>'Seleccione la categoría','required']) }}
                        <p class="text-danger">{{ $errors->first('description')}}</p>
                    </div>
                    <div class="form-group">
                        <button type="button" id="obtener" class="btn btn-sm btn-outline-primary" onclick="ObtenerId()">Obtener nombre</button>
                        <div id="boton"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-scraps').className+=' active';
        var idP="";
        function statusChangeCallback(response) {
            if (response.status === 'connected') {

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function ObtenerId() {
            let page=document.getElementById("page_id").value;
            ObtenerNombre(page);
        }

        function ObtenerNombre(page) {
            FB.api(
                '/'+page+'',
                'GET',
                {"fields":"name,category"},
                function(response) {
                    let nombre=response.name,
                        idP=response.id,
                        post=response.post,
                        categoria=document.getElementById("categoria_id").value,
                        boton='';
                        data={};
                    data={categoria,nombre,idP,post};
                    if(!response || response.error){
                        swal('Intentá de nuevo', 'El id de la página está incorrecto', 'error');
                        return false;
                    }
                    axios.post('{{ route('scrapsPage.saveScrap',$company) }}',data).then(response => {
                        document.getElementById("page_name").value=nombre;
                        document.getElementById("page_id").value=idP;
                        document.getElementById("post").value=idP+"_";

                        boton = `<a class="btn btn-sm btn-success" href="{{ route('scrapComments.index') }}?page=`+idP+'&page_name='+nombre+`">Comments</a>`;
                        document.getElementById('boton').innerHTML = boton;
                        document.getElementById("obtener").hidden=true;
                        guardarScrap();
                    });
                }
            );
        }

        function guardarScrap(){
            let datos = {},
                idP=document.getElementById("page_id").value;
                nombre=document.getElementById("page_name").value;
                post =document.getElementById("post").value;
                categoria=document.getElementById("categoria_id").value;
                
            datos = {idP,post,nombre,categoria},
            axios.post('{{ route('scrapsPage.saveScrap',$company) }}', datos).then(response => {
                
            });
        }

        function guardarPagina() {
            let datos = {}, 
                idP=document.getElementById("page_id").value;
                nombre=document.getElementById("page_name").value;

            datos = {idP, nombre};
            axios.post('{{ route('scrapsPage.saveScrap') }}', datos).then(response => {
            });
        }

        function stopRKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
        }
        document.onkeypress = stopRKey;
        </script>
@endsection