@extends('layouts.app')

@section('content')
<div class="col-lg-12">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h3 class="fw-bold">Reporte de los temas</h3></div>
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body table-responsive">
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
                    {{--<div class="form-group">--}}
                        {{--{{ Form::label('id','Seleccione la categoría *') }}--}}
                        {{--<select class="form-control" id="comboMega">--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        {{ Form::label('id','Seleccione el tema *') }}
                        <select class="form-control" id="comboSub">
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" id="obtener" class="btn btn-sm btn-primary btn-round btn-block" onclick="seleccionar()">Obtener información</button>
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
        //$(document).ready(getMega());
        $(document).ready(getSub());
        function getMega() {
            let datos = {};
                user_id=document.getElementById("user").value,
                datos = {user_id};
                comboList='<option value="0" selected>Seleccione la categoría</option>';

            axios.post('{{ route('Report.ItemMegacategory',$company) }}', datos).then(response => {
                console.log(response.data);
                var consulta = response.data;
                consulta.forEach(function (mega, index) {
                    comboList+='<option value="'+mega.id+'">'+mega.name+'</option>';
                });
                $('#comboMega').html(comboList);
            });

        }
        $('#comboMega').change(function () {
            getSub();
        });
        function getSub() {

            let datos = {},
                user_id=document.getElementById("user").value,
                comboList='<option value="0" selected>Seleccione la tema</option>';

            datos = {user_id};
            axios.post('{{ route('Report.ItemSubcategory',$company) }}', datos).then(response => {
                console.log(response.data);
                let long=response.data.length;
                for (let i=0;i<long;i++){
                    comboList+='<option value="'+response.data[i]['id']+'">'+response.data[i]['name']+'</option>';
                }
                $('#comboSub').html(comboList);
            });
        }
        function seleccionar() {
            var sub=document.getElementById('comboSub').value,
                start=document.getElementById('start').value,
                end=document.getElementById('end').value;
            //if(sub!=0 && mega!=0){
            if(sub!=0){
                let subE=btoa(sub),
                    startE=btoa(start),
                    endE=btoa(end);
                //window.location.href = 'SubcategoriaReporte/'+megaE+'/'+subE+'/'+startE+'/'+endE;
                window.location.href = 'SubcategoriaReporte/'+subE+'/'+startE+'/'+endE;
            }else{
                swal('Ops ! ','Por favor seleccione un tema','');
                alert('Por favor complete los campos');
            }
        }
    </script>
@endsection
