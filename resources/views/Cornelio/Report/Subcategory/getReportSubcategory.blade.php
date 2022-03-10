@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fa fa-area-chart"></i>
                    </h4>
                    @php
                        $start= base64_encode($fechas['start']);
                        $end=base64_encode($fechas['end']);
                        $sub_id = base64_encode($sub['id']);
                    @endphp
                    <a href="{{ url('ExportTopics/'.$sub_id.'/'.$start.'/'.$end.'/') }}" class="btn btn-sm btn-success pull-right ml-2 mb-1">
                        Exportar
                    </a>
                </div>

                <!---------------------------------------------------------- HIDDEN ------------------------------------------------>
                @if(isset($fechas))
                    <input type="hidden" name="start" id="start" value="{{$fechas['start']}}">
                    <input type="hidden" name="end" id="end" value="{{$fechas['end']}}">
                @else
                    <input type="hidden" name="start" id="start" value="">
                    <input type="hidden" name="end" id="end" value="">
                @endif
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <!---------------------------------------------------------------------------------------------------------->

                <div class="card-body table-responsive">
                    <!------------------------------------------------------ Si no encuentra datos ----------------------------------------------------->
                    @if(count($posts)==0)
                        <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                    @endif

                    <!------------------------------------------------------ Muestra los datos que se encuentra ----------------------------------------------------->
                    @if(count($posts)>0)
                        <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_analisis.jpg') }}"  alt="Reporte diario" title="Reporte diario" style="display:block; margin-left: auto; margin-right: auto;" >
                        <h2 style="color:#0f3760;">Impacto provocado sobre el tema {{$sub['name']}}</h2>

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th>Interacciones</th>
                                    <th>Publicaciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $contar=[];
                                @endphp

                                @foreach($posts as $post)
                                    @php
                                        $value=$post['page_name'];
                                        $reacciones=$post['comentarios']+$post['reacciones'];
                                    @endphp

                                    @if(isset($contar[$value]))
                                        @php
                                            $contar[$value]['interacciones']+=$reacciones;
                                            $contar[$value]['publicaciones']+=1;
                                            $contar[$value]['nombre']=$value;
                                        @endphp

                                     @else
                                        @php
                                            $contar[$value]['interacciones']=$reacciones;
                                            $contar[$value]['publicaciones']=1;
                                            $contar[$value]['nombre']=$value;
                                            @endphp
                                        @endif
                                    @endforeach

                                    @php
                                        arsort($contar);
                                    @endphp

                                    @foreach($contar as $medio)
                                        <tr>
                                            <td>{{$medio['nombre']}}</td>
                                            <td>{{$medio['interacciones']}}</td>
                                            <td>{{$medio['publicaciones']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    <!-------------------------------------------------- Estadistica --------------------------------------------------------->
                        <br><br>
                        <div class="card mx-auto">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-bar"></i> Estadística</h4>
                            </div>

                            <div class="card-body">
                                <div id="chart" class="container"></div>
                            </div>
                        </div>

                    <!-------------------------------------------------- Publicaciones --------------------------------------------------------->

                    <div class="row">
                        <div class="col-sm-12" id="{{$sub['id']}}">
                            <button onclick="getDatos({{$sub['id']}})" class="btn btn-primary btn-lg btn-block">Ver nube de palabras</button>
                        </div>

                        <br>
                        <div class="col-sm-12 " id="cloud-{{$sub['id']}}"></div>
                        <div class="w-100"></div>
                        </br></br>
                        <div class="col-sm-12">
                            <h2>Publicaciones del tema: {{$sub['name']}}</h2>
                        </div>

                        @foreach($posts as $post)
                            <br>
                            <div class="col-sm-4" style="overflow: auto">
                                <div class="card mb-3" style=" height: 700px;overflow: auto">

                                    @if($post['picture']!=null)
                                        <img src="{{$post['picture']}}" alt="" class="card-img-top img-fluid w-100">
                                    @endif

                                    @if($post['video']!=null)
                                        <video width="100%" height="auto" controls>
                                        <source src="{{$post['video']}}" type="video/mp4"></video>
                                    @endif

                                        <h6 class="card-title mb-1" style="color:#0f3760;">{{$post['page_name']}}</h6>
                                        <p class="card-text small">{{$post['content']}}
                                            @if($post['url']!=null)
                                                <br>
                                                <a href="{{$post['url']}}" target="_blank">{{$post['title']}}</a>
                                            @endif
                                        </p>

                                    <hr class="my-0">
                                    <div class="card-footer small text-muted">{{$post['created_time']}}</div>
                                    <div class="card-body" id="comments-{{$post['post_id']}}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/wordcloud.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script>

        $(document).ready(getRandom(),DatosChart());
        let bandera=0;

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function getDatos(id){
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={sub:id,start:start,end:end};
            }else{
                datos={sub:id}
            }

            showProcessing();
            axios.post('{{ route('Report.Comment',$company) }}', datos).then(response => {
                console.log(response);
                if((response.data).length>0){
                    procesarComentarios(response.data,id);
                }else{
                    $('#cloud-'+id).html('<h3>No hay suficientes datos!</h3>');
                    $('#'+id).remove();
                    hideProcessing();
                }
            });
        }

        function procesarComentarios2(data,id) {
            let texto = data,
                comentarios;

            texto.forEach(function (posts, index) {
                comentarios += " " + posts;
            });
            lines = comentarios.split(/[,\. ]+/g),
                datos = Highcharts.reduce(lines, function (arr, word) {
                    var obj = Highcharts.find(arr, function (obj) {
                        return obj.name === word;
                    });
                    if (obj) {
                        obj.weight += 1;
                    } else {
                        obj = {
                            name: word,
                            weight: 1
                        };
                        arr.push(obj);
                    }
                    return arr;
                }, []);

            Highcharts.chart('cloud-'+id, {
                series: [{
                    type: 'wordcloud',
                    data: datos,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }
            });
            $('#'+id).remove();
            hideProcessing();
        }

        function procesarComentarios(data,id) {
            var comentario="";
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
                ' el ',' por ','Por ',' como ',' cómo ','Y ',' y ',' un ',' una ',' uno ',' mas ', ' más ',
                ' se ',' no ','No ',' si ','Si ', 'A ',' a ',' en ',' es ','está ',' eso ',' esos ',' pero ',
                'Image/Emoji',' para ',' las ',' su ',' sus ',' esa ','!',' ser ',' sin ',' ya ',' los ',' te ',
                ' me ','Me ',' ja ',' jaja ',' je ',' jeje ',' les ',' la ',' le ',' son ','DE ','QUE ','LA ',' con ',
                'Pero ',' este ',' esta ',' hace ',' poco ',' toda ','Toda ','Todo ',' todo ',' bien ','Bien ',' estos ',
                ' estas ','Estos ','Estas ','Está ',' esto ',' solo ',' cada ',' todos ','Todos ',' nada ',' ellos ',
                ' Costa ',' Rica ', ' Diario ', ' Extra '
            ];
            var i;
            var countId=0;
            var filtrado=[];
            data.forEach(function (post, indexsub) {

            var texto=post.comment;
                palabras.forEach(function (palabra, index2) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ")
                    }
                });
                comentario+=" "+texto;
            });
            var text = comentario;
            var lines = text.split(/[,\. ]+/g),
                dataP = Highcharts.reduce(lines, function (arr, word) {
                    var obj = Highcharts.find(arr, function (obj) {
                        return obj.name === word;
                    });
                    if (obj) {
                        obj.weight += 1;
                    } else {
                        obj = {
                            name: word,
                            weight: 1
                        };
                        arr.push(obj);
                    }
                    return arr;
                }, []);
                dataP=dataP.sort(function (a, b){
                    return (b.weight - a.weight)
                });
                var temporal;
                var hasta=dataP.length;
                if(hasta>60){
                    hasta=60;
                }
                for(i=0;i<hasta;i++){
                    var x= dataP[i].name;
                    if(x.length>3){
                        temporal=dataP[i];
                        filtrado.push(temporal);
                    }
                }
            Highcharts.chart('cloud-'+id, {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }
            });
            $('#'+id).remove();
            hideProcessing();
        }

        function getRandom() {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={subcategoria_id:'{{$sub['id']}}',start:start,end:end};
            }else{
                datos={subcategoria_id:'{{$sub['id']}}'};
            }
            axios.post('{{ route('Report.MessageRandom',$company) }}', datos).then(response => {
                comentarios(response.data);
            });
        }

        function comentarios(data) {
            var longitud=data.length;
            var i;
            for(i=0; i<longitud; i++ ){
                var com=data[i].comentarios,post_id=data[i].post, commList='';
                com.forEach(function (comentario,index) {
                    commList+='<div class="received_withd_msg"><p class="card-text small ">'+comentario.comment+'</p>' +
                        '</div><p class="small text-muted">'+comentario.created_time+'</p>';
                });
                $('#comments-'+post_id).html(commList);
            }
        }

        function DatosChart() {
            let start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={subcategoria_id:'{{$sub['id']}}',start:start,end:end};
            }else{
                datos={subcategoria_id:'{{$sub['id']}}'};
            }

            axios.post('{{ route('Report.chartReporteInteraction',$company) }}', datos).then(response => {

                chart(response.data);
                //chart2(response.data);
                hideProcessing();
            });
        }

        function chart(data) {
            Highcharts.chart('chart', {

                title: {
                    text: 'Interacción de los temas'
                },

                yAxis: {
                    title: {
                        text: 'Interacciones'
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },

                xAxis: {
                    categories: data.fechas
                },

                series: data.series,

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

        function chart2(data) {
            Highcharts.chart('chart', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Interacciones de las Subcategorias'
                },

                yAxis: {
                    title: {
                        text: 'Interacciones'
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },

                xAxis: {
                    categories: data.fechas
                },

                series: [{
                        name: 'Comentario',
                        color: '#f7a35c' ,
                        data: data.series[1].data
                        },{
                        name: 'Reacción',
                        color: '#f3545d' ,
                        data: data.SeriesR[1].data
                }]

            });
        }

        function DatosChartPost() {

            let start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;

            if(start!=='' && end!==''){
                datos={subcategoria_id:'{{$sub['id']}}',start:start,end:end};
            }else{
                datos={subcategoria_id:'{{$sub['id']}}'};
            }

            {{--if(start!=='' && end!==''){--}}
                {{--datos={'megacategoria_id':'{{$mega}}',start:start,end:end};--}}
            {{--}else{--}}
                {{--datos={'megacategoria_id':'{{$mega}}'};--}}
            {{--}--}}
        {{----}}
            axios.post('{{ route('Report.chartReportePost',$company) }}', datos).then(response => {
                Highcharts.chart('chart-post', {
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text: 'Estadisticas de las publicaciones'
                    },
                    yAxis: {
                        title: {
                            text: 'Publicaciones'
                        },
                    },
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    },

                    xAxis: {
                        categories: response.data.fechas
                    },

                    series: response.data.series,

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
                bandera=bandera+1;
                if(bandera===3){
                    hideProcessing();
                }


                ;
            });
        }

        function showProcessing() {
            $("body").append(
                '<div id="overlay-processing" style="background: #F0F0F0; height: 100%; width: 100%; opacity: .9; padding-top: 10%; position: fixed; text-align: center; top: 0;z-index: 2147483647;">' +
                '<h2 style="color: #333333" id="estado">Leyendo comentarios</h2>' +
                '<i class="fa fa-cog fa-spin fa-3x fa-fw" aria-hidden="true"></i>' +
                '<span class="sr-only">Processing</span></div>'
            );
        }

        function hideProcessing() {
            $('body').find('#overlay-processing').remove();
        }
    </script>
@endsection
