@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mx-auto">
                    <div class="card-header"><i class="fas fa-chart-pie"></i> 
                        Estadísticas de los temas
                    </div>
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">

                    <div class="card-body">
                        
                        <div class="form-row align-items-center">
                        

                            <div class="col-sm-2 my-1">
                                <label class="sr-only" for="start">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-sm-2 my-1">
                                <label class="sr-only" for="end">Fecha Final</label>
                                <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-sm-3 my-1">
                                <label class="sr-only" for="interval">Intervalo</label>
                                <select class="custom-select" id="interval">
                                    <option value="Mensual">Mensual</option>
                                    <option value="Diario" selected >Diario</option>
                                    <option value="Hora">Hora</option>
                                </select>
                            </div>
                            {{--<div class="col-sm-3 my-1" >--}}
                                {{--<!-- {{ Form::label('id','Seleccione la subcategoria  *') }} -->--}}
                                {{--{{ Form::select('id',$subcategory,null,['class'=>'form-control','placeholder'=>'Seleccione la subcategoria','required']) }}--}}
                            {{--</div>--}}

                            <div class="col-sm-4 my-1" >
                                <select id="subcategoria" name="subcategoria[]" class="form-control"  multiple style="height: 11110%">
                                </select>
                            </div>

                            <div class="col-auto my-1">
                                <button type="button" class="btn btn-primary" onclick="buscar()">Filtrar</button>
                            </div>
                        </div>
                        

                    </div>
                </div>
                
                <div class="card mx-auto">
                    <div class="card-header">
                        <h4>
                            <i class="fas fa-chart-bar"></i> Estadística
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <div id="chart" class="container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endsection
@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(getSubcategoria());

        $('#subcategoria').select2({
            theme: "bootstrap",
            placeholder: "Seleccione un tema",
            theme: "classic"
        });


        function statusChangeCallback(response) {
            isLogedIn();
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            getSubcategoria();

        }

        /******************************************************* Subcategoria *************************************************************************/
        function getSubcategoria() {
            let datos = {};
            pageLists = "";
            user_id=document.getElementById('user').value;
            datos = {user_id};
            axios.post('{{ route('Statistics.getSubC',$company) }}', datos).then(response => {
                let resultado = response.data;
                resultado.forEach( function(element, index) {
                    pageLists += "<option value='"+element.id+"||##||"+element.name+"'>"+element.name+"</option>";
                    //pageLists += '<option value='+element.page_id+'>'+element.page_name+'</option>';
                });
                $('#subcategoria').html(pageLists);
            })
        }


        function buscar() {
            let start = document.getElementById('start').value,
                end = document.getElementById('end').value,
                interval = document.getElementById('interval').value,
                subcategoria = Array.from(document.getElementById('subcategoria').selectedOptions).map(el=>el.value),
                datos = {start, end, interval, subcategoria};
            axios.post('{{ route('Statistics.StatisticsSubC') }}', datos).then(response => {
                console.log(response.data);
                if (response.data) {
                    chartSubCategoria(response.data);
                } else {
                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
                }
            });
        }
  
        function chartSubCategoria(data) {
            var sub_Categoria = data.Subcategoria;
            console.log(data.Subcategoria);
            Highcharts.chart('chart', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Temas'
                },
                yAxis: {
                    title: {
                        text: 'Total'
                    },
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                xAxis: {
                    categories: data.fechas
                },
                series: sub_Categoria,
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }
            });
        }

    </script>
@endsection
