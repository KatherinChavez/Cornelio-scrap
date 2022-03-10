@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fa fa-area-chart"></i>
                        @if(isset($fechas))
                            <strong style="color:#0f3760;" hidden> Reporte desde {{$fechas['start']}} a {{$fechas['end']}} </strong>
                        @else
                            <strong style="color:#0f3760;"> Reporte de la fecha {{date('d/m/Y')}}</strong>
                        @endif
                    </h4>
                </div>

                <!---------------------------------------------------------- HIDDEN ------------------------------------------------>
                
                @if(isset($fechas))
                    <input type="hidden" name="start" id="start" value="{{$fechas['start']}}">
                    <input type="hidden" name="end" id="end" value="{{$fechas['end']}}">
                @else
                    <input type="hidden" name="start" id="start" value="{{date('d/m/Y')}}">
                    <input type="hidden" name="end" id="end" value="{{date('d/m/Y')}}">
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
                        <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_analisis.jpg') }}"  alt="Reporte diario" title="Reporte diario" style="vertical-align: middle; width:100%">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tema</th>
                                    <th>Página</th>
                                    <th>Publicación</th>
                                    <th>Comentarios</th>
                                    <th>Reacciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                @php
                                //dd($post);
                                @endphp
                                <tr>
                                    <th>
                                        @if(isset($post['sub']))
                                        {{$post['sub']}}
                                        @endif
                                    </th>
                                    <td>{{$post['page_name']}}</td>
                                    <td>{{substr($post['content'], 0, 40).'...'}}</td>
                                    <td>{{$post['comentarios']}}</td>
                                    <td>{{$post['reacciones']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        </br></br></br>

                        <h2 style="color:#0f3760;">Impacto provocado en Medios Digitales sobre los diversos temas monitoreados</h2>
                        <table class="table table-striped table-sm table-responsive-sm">
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
                        </br></br>

                        <!-------------------------------------------------- Estadistica --------------------------------------------------------->
                        {{--<div class="card mx-auto">--}}
                            {{--<div class="card-header">--}}
                                {{--<h4><i class="fas fa-chart-bar"></i> Estadística</h4>--}}
                            {{--</div>--}}

                            {{--<div class="card-body">--}}
                                {{--<div id="chart" class="container">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <!-------------------------------------------------- Publicaciones --------------------------------------------------------->
                        <div class="row">
                                @foreach($posts as $post)
                                    @if(isset($post['sub']))
                                        <br>
                                        <div class="w-100"></div>
                                        <h2 style="color:#0f3760;">Publicaciones del tema: {{$post['sub']}}</h2>
                                        <div class="w-100"></div>

                                        <br>
                                        <div class="col-sm-12" id="{{$post['id']}}">
                                            <button onclick="getDatos({{$post['id']}})" class="btn btn-outline-primary btn-lg btn-block">Ver nube de palabras</button>
                                        </div>

                                        <br>
                                        <div class="w-100"></div>
                                        <div class="col-sm-12" id="{{$post['id']}}">
                                            <button onclick="getRandom({{$post['id']}})" class="btn btn-outline-info btn-lg btn-block">Ver comentarios</button>
                                        </div>

                                        <br>
                                        <div class="col-sm-12" id="cloud-{{$post['id']}}"></div>
                                        <div class="col-sm-12 mb-3" id="im-{{$post['id']}}">
                                            <button onclick="getImpacto({{$post['id']}})" class="btn btn-outline-success btn-lg btn-block">Ver impacto</button>
                                        </div>
                                        <div id="pagina-{{$post['id']}}" class="col-sm-12"></div>
                                    @endif

                                    <div class="col-sm-6">
                                        <div class="card mb-3">
                                            @if($post['picture']!=null)
                                                <img src="{{$post['picture']}}" alt="" class="card-img-top img-fluid w-100">
                                            @endif

                                            @if($post['video']!=null)
                                                <video width="100%" height="auto" controls>
                                                    <source src="{{$post['video']}}" type="video/mp4"></video>
                                            @endif

                                            <div class="card-body">
                                                <h6 class="card-title mb-1" style="color:#0f3760;">{{$post['page_name']}}</h6>
                                                <p class="card-text small">{{$post['content']}}
                                                    @if($post['url']!=null)
                                                        <br><a href="{{$post['url']}}" target="_blank">{{$post['title']}}</a>
                                                    @endif
                                                </p>
                                            </div>

                                            <hr class="my-0">
                                            <div class="card-body py-2 small">
                                                <div class="mr-3 d-inline-block">
                                                    <img src="{{ asset('reacciones/like.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="like" style=" vertical-align: middle">{{$post['reacciones']}}</label>
                                                </div>

                                                <div class="mr-3 d-inline-block">
                                                    <img src="{{ asset('reacciones/comment.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="comentarios" style=" vertical-align: middle">{{$post['comentarios']}}</label>
                                                </div>

                                                <div class="mr-2 d-inline-block pull-right">
                                                    <a class="btn btn-success btn-small" title="Compartir" href=" https://wa.me/?text=Hola! te comparto esta nota, relacionada con: {{$post['page_name']}}, acá te dejo el link para que la veas: https://www.facebook.com/{{$post['post_id']}}" target="_blank">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>

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
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        //$(document).ready(DatosChart());

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
            axios.post('{{ route('Review.ReportWordCloud') }}', datos).then(response => {
                if((response.data).length>5){
                    procesarComentarios(response.data,id);
                }else{
                    $('#cloud-'+id).html('<h3>No hay suficientes datos!</h3>');
                    $('#'+id).remove();
                    hideProcessing();
                }
            });        
        }

        function procesarComentarios(data,id) {
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


        function getRandom(id) {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;

            if(start!=='' && end!==''){
                datos={subcategoria_id:id,start:start,end:end};
            }else{
                datos={subcategoria_id:id}
            }

            axios.post('{{ route('Review.commentsRandom') }}', datos).then(response => {
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


        function getImpacto(id) {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={sub:id,start:start,end:end};
            }else{
                datos={sub:id}
            }
            //showProcessing();
            axios.post('{{ route('Review.ReportImpactPost',$company) }}', datos).then(response => {
                response.data.sort(function (a, b){
                    return (b.interacciones - a.interacciones)
                });
                tablaImpacto(response.data,id);
            }); 
        }

        function tablaImpacto(data,id) {
            var tablaList='<h2 style="color:#0f3760;">Medios de más impacto</h2>'+
                '<div class="" >'+
                '<table class="table table-striped table-sm table-responsive-sm">'+
                '<thead>'+
                '<tr>'+
                '<th>Página</th>'+
                '<th>Interacciones</th>'+
                '<th>Publicaciones</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>';
            data.forEach(function (medio,index) {
                tablaList+='<tr>'+
                    '<td>'+medio.nombre+'</td>'+
                    '<td>'+medio.interacciones+'</td>'+
                    '<td>'+medio.publicaciones+'</td>'+
                    '</tr>'

            });
            tablaList+='</tbody>'+
                '</table>'+
                '</div>';
            $('#pagina-'+id).html(tablaList);
            $('#im-'+id).remove();
            hideProcessing();
        }

        function DatosChart() {
            let start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={'megacategoria_id':'{{$megacategoria_id}}',start:start,end:end};
            }else{
                datos={'megacategoria_id':'{{$megacategoria_id}}'};
            }
            axios.post('{{ route('Report.chartReporteInteraction',$company) }}', datos).then(response => {
                chart(response.data);
                hideProcessing();
            });
        }

        function chart(data) {
            Highcharts.chart('chart', {

                title: {
                    text: 'Interacciones de los temas '
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