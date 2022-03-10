@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mx-auto">
                    <div class="card-header"><i class="fas fa-chart-pie"></i> 
                        Estadísticas de las páginas
                    </div>
                    <div class="card-body">
                        
                        <div class="form-row align-items-center">
                            <input id="user" type="hidden" value="{{ Auth::user()->id }}">
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

                            <div class="col-sm-4 my-1" >
                                <select id="pages" name="pages[]" class="form-control" multiple>
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
                                <i class="fas fa-chart-line"></i> Estadística Comentarios
                            </h4>
                    </div>
                    <div class="card-body">
                        <div id="chart" class="container">
                        </div>
                    </div>
                </div>
                {{--<div class="card mx-auto">--}}
                    {{--<div class="card-header">--}}
                        {{--<h4>--}}
                            {{--<i class="fas fa-chart-bar"></i> Estadística inbox--}}
                        {{--</h4>--}}
                    {{--</div>--}}
                    {{--<div class="card-body">--}}
                        {{--<div id="chartInbox" class="container">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div> 
@endsection
@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(getPages());

        $('#pages').select2({
			theme: "bootstrap",
            placeholder: "Seleccione una página",
            theme: "classic"
		});

		$('#multiple-states').select2({
			theme: "bootstrap"
        }); 

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn();
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            getPages();
        }
        
        /******************************************************* Page *************************************************************************/
        function getPages() {
            let datos = {};
                pageLists = "";
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                user_id=document.getElementById('user').value;
            datos = {user_id};
            axios.post('{{ route('ClassifyFeeling.page',$company) }}', datos).then(response => {
                let resultado = response.data;
                resultado.forEach( function(element, index) {
                    pageLists += "<option value='"+element.page_id+"||##||"+element.page_name+"'>"+element.page_name+"</option>";
                    //pageLists += '<option value='+element.page_id+'>'+element.page_name+'</option>';
                });
                $('#pages').html(pageLists);
            })
        }


        /******************************************************* Optiene *************************************************************************/
        function buscar() {
            let start = document.getElementById('start').value,
                end = document.getElementById('end').value,
                interval = document.getElementById('interval').value,
                page_id = Array.from(document.getElementById('pages').selectedOptions).map(el=>el.value),
                datos = {start, end, interval, page_id};

            axios.post('{{ route('Statistics.StatisticsPage') }}', datos).then(response => {
                var resultado = response.data;
                //(response.data).length>0
                if( resultado.status == "success") {
//                    chartInbox(response.data);
                    chartComment(response.data);
                    // chartComment2(response.data);
                    // chartSubCategoria(response.data);
                } else {
                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
                }
            });
        }
  
        /******************************************************* Estadisticas *************************************************************************/
        function chartComment2(data) {
            var pages = data.pages;
            for (var key in pages) {
                var page = pages[key];

                Highcharts.chart('chart', {
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text: 'Comentarios'
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
                    series: page.comment,

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
            // var commet1 = data.pages;
            // var commet2 = data.pages;
            
        }   

        function chartComment(data) {
            var comment = data.Comment;
            Highcharts.chart('chart', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Comment'
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
                series: comment,

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

        function chartInbox2(data) {
            let inbox = data.Inbox;
                audiencia = data.Audience;
                Admin = data.Admin;
                comment = data.Comment;
                i = 0;
                Highcharts.chart('chartInbox', {
                chart: {
                    marginLeft: 40, // Keep all charts left aligned
                    spacingTop: 20,
                    spacingBottom: 20
                },
                title: {
                    text: 'Inbox'
                },
                xAxis: {
                    categories: data.fechas
                },
                yAxis: {
                    title: {
                        text: 'Total'
                    },
                },
                plotOptions: {
                    area: {
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 3,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                series: [{
                    data: data.Inbox[i]
                    }]
                               
            });
        
        }

        function chartInbox(data) {
            let inbox = data.Inbox;
                audiencia = data.Audience;
                Admin = data.Admin;
                comment = data.Comment;

            Highcharts.chart('chartInbox', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Inbox'
                },
                xAxis: {
                    categories: data.fechas
                },
                yAxis: {
                    title: {
                        text: 'Total'
                    },
                },
                plotOptions: {
                    area: {
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 3,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Todo',
                    data: data.Inbox[0].data
                    },
                    {
                    name: 'Administrador',
                    data: data.Audience[0].data
                    },
                    {
                    name: ' Audiencia',
                    data: data.Admin[0].data
                    }]
                               
            });
        }

    </script>
@endsection
