@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Autoclasificar sentimiento</h4></div>
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body table-responsive">
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
                        {{ Form::label('page_id','Seleccione la página  *') }}
                        {{ Form::select('page_id',$page,null,['class'=>'form-control','placeholder'=>'Seleccione la página','required']) }}
                        <p class="text-danger">{{ $errors->first('description')}}</p>
                    </div> 
                    <div class="form-group">
                        <label for="tipo">Seleccione el tipo</label>
                        <select id="tipo" name="tipo" class='form-control'>
                            <option value="3">Positivo</option>
                            <option value="1" selected>Negativo</option>
                        </select>
                    </div> 
                    
                    <div class="form-group">
                        <button type="button" id="seleccionar" class="btn btn-sm btn-outline-primary" onclick="setData()">Clasificar</button>
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
        let datos = {};
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            start_time=document.getElementById("start").value,
            end_time=document.getElementById("end").value,
            user_id=document.getElementById("user").value,
            pagina_id=document.getElementById("page_id").value,
            tipo=document.getElementById("tipo").value;
            datos = {start_time, end_time, user_id, pagina_id, tipo};        
            axios.post('{{ route('Classification.Sentiment',$company) }}', datos).then(response => {
               swal("Autoclasificación !", response.data, "success");
            });
    }
</script>
@endsection