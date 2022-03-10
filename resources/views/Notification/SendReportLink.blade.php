<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        .highcharts-credits{display: none;}
        .received_withd_msg p {
            background: #ebebeb none repeat scroll 0 0;
            border-radius: 4px;
            color: #000;
            margin: 0;
            padding: 5px 10px 5px 12px;
            width: 100%;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/8f70cb5d5a.js"></script>
    <title>Reporte diario</title>
</head>
{{--bg-dark--}}
<body style="background-color: #205796!important; margin: auto;">
{{--container--}}
<div class="container">
    {{--row justify-content-center--}}
    <div class="row justify-content-center" >
        {{--col-md-12--}}
        <div class="col-md-12">
            {{--card mx-auto mt-5--}}
            <div class="card mx-auto mt-5" >
                {{--card-header--}}
                <div class="card-header">
                    <i class="fa fa-area-chart"></i>
                    @if(isset($fechas))
                    <strong style="color:#0f3760;"> Reporte desde {{$fechas['start']}} a {{$fechas['end']}} </strong>
                    @else
                        <strong style="color:#0f3760;"> Reporte de la fecha {{date('d/m/Y')}}</strong>
                    @endif
                </div>

                @if(isset($companie))
                    <input type="hidden" name="company" id="company" value="{{$companie}}">
                @endif
                @if(isset($fechas))

                    <input type="hidden" name="start" id="start" value="{{$fechas['start']}}">
                    <input type="hidden" name="end" id="end" value="{{$fechas['end']}}">
                    @else
                    <input type="hidden" name="start" id="start" value="">
                    <input type="hidden" name="end" id="end" value="">
                    @endif
                @if(count($posts)==0)
                    <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                @endif
                @if(count($posts)>0)
                    <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_analisis.jpg') }}"  alt="Reporte diario" title="Reporte diario" style="vertical-align: middle">
                    {{--card-body--}}
                    <div class="card-body table-responsive">
                        {{--container-fluid--}}
                        <div class="container-fluid">
                            {{--row--}}
                            <div class="row">
                                {{--table table-striped table-sm--}}
                                <table class="table table-striped table-sm table-responsive-sm">
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
                                    {{--tabla--}}
                                    @foreach($posts as $post)
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
                            </div>
                            <div class="row">
                                <br><br>
                                <h2 style="color:#0f3760;">Impacto provocado en Medios Digitales sobre los diversos temas monitoreados</h2>
                                <div class="w-100" >
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
                                </div>
                            </div>
                            <br><br><br>
                            <div class="row">
                                <div class="w-100" id="chart"></div>
                                {{--<canvas class="hidden col-sm-12" id="canvas"></canvas>--}}
                            </div>
                            <div class="row">
                                {{--publicaciones--}}
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
                                        <div class="w-100"></div>
                                        <div class="col-sm-12" id="{{$post['id']}}">
                                            <button onclick="getRandom({{$post['id']}})" class="btn btn-outline-info btn-lg btn-block">Ver comentarios</button>
                                        </div>
                                        <br>
                                        <div class="col-sm-6 offset-md-3" id="cloud-{{$post['id']}}"></div>
                                        <div class="col-sm-12 mb-3" id="im-{{$post['id']}}">
                                            <button onclick="getImpacto({{$post['id']}})" class="btn btn-outline-success btn-lg btn-block">Ver impacto</button>
                                        </div>
                                        <div id="pagina-{{$post['id']}}" class="col-sm-12"></div>
                                    @endif
                                    {{--<div class="w-100"></div>--}}
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
                                            </div>
                                            </p>
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
                                                    <a class="btn btn-success btn-small" title="Compartir" href=" https://wa.me/?text=Hola! te comparto esta nota, relacionada con: {{$post['name']}}, acá te dejo el link para que la veas: https://www.facebook.com/{{$post['post_id']}}" target="_blank">
                                                        <i class="fa fa-whatsapp"></i>
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
                        </div>
                    </div>
                @endif
                <div class="card-footer" ><p style=" text-align: center; color:#0f3760;">Reporte Generado por <a href="https://goo.gl/4oMRBD" target="_blank">Agencia Digital de Costa Rica </a>- <a href="https://goo.gl/w2Stra" target="_blank">Cornel.io</a> Todos los Derechos Reservados</p></div>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>    
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

        $(document).ready(DatosChart());

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
            axios.post('{{ route('Report.CommentPost',$company) }}', datos).then(response => {
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
            var comentario="";
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
                ' el ',' por ','Por ',' como ',' cómo ','Y ',' y ',' un ',' una ',' uno ',' mas ', ' más ',
                ' se ',' no ','No ',' si ','Si ', 'A ',' a ',' en ',' es ','está ',' eso ',' esos ',' pero ',
                'Image/Emoji',' para ',' las ',' su ',' sus ',' esa ','!',' ser ',' sin ',' ya ',' los ',' te ',
                ' me ','Me ',' ja ',' jaja ',' je ',' jeje ',' les ',' la ',' le ',' son ','DE ','QUE ','LA ',' con ',
                'Pero ',' este ',' esta ',' hace ',' poco ',' toda ','Toda ','Todo ',' todo ',' bien ','Bien ',' estos ',
                ' estas ','Estos ','Estas ','Está ',' esto ',' solo ',' cada ',' todos ','Todos ',' nada ',' ellos ',
                ' deja ', ' Deja ' , ' aqui ', ' aquí ', ' mejor ', ' Mejor ', ' tambien ', ' también ', ' tiene ', ' tienen',
                ' algo ', ' Algo ', ' tener ', ' donde ', ' hasta ', ' Hasta ', ' otros ', ' hacer ', ' algo ', ' Algo ', ' pueden ',
                ' Pueden ', ' igual ', ' Igual ', ' quieren ', 'Quieren ', ' usted ', ' Usted ', ' quien ', ' Quien',
                ' otra ', ' puede ', ' pueden ', ' Puede ', ' mismo ', ' cuanta ', ' cuanto ', ' mismos ', ' estamos ',
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las '
            ];
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

        function getRandom(id) {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={subcategoria_id:id,start:start,end:end};
            }else{
                datos={subcategoria_id:id}
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

        function getImpacto(id) {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={sub:id,start:start,end:end};
            }else{
                datos={sub:id}
            }
            showProcessing();
            axios.post('{{ route('Report.Impact',$company) }}', datos).then(response => {
                consulta = response.data;
                consulta.sort(function (a, b){
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


        {{--function DatosChart1() {--}}
            {{--let start=document.getElementById('start').value,--}}
                {{--end=document.getElementById('end').value,--}}
                {{--datos;--}}
            {{--if(start!=='' && end!==''){--}}
                {{--datos={'company':'{{$companie_id}}',start:start,end:end};--}}
            {{--}else{--}}
                {{--datos={'company':'{{$companie_id}}'};--}}
            {{--}--}}
            {{--axios.post('{{ route('Report.Interaction') }}', datos).then(response => {--}}
                {{--chart(response.data);--}}
                {{--hideProcessing();--}}
            {{--});--}}
        {{--}--}}

        function DatosChart() {
            let start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                company = document.getElementById('company').value,
                datos;
            if(start!=='' && end!==''){
                datos={company,start:start,end:end};
            }else{
                datos={company};
            }
            console.log(datos);
            //axios.post('{{ route('Report.Interaction') }}', datos).then(response => {
            axios.post('{{ route('Report.chartReporteInteraction') }}', datos).then(response => {

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

        function chart1(data) {
            Highcharts.chart('chart', {

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
                        data: data.series[0].data
                        },{
                        name: 'Reacción',
                        color: '#f3545d' ,
                        data: data.SeriesR[0].data
                }]

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