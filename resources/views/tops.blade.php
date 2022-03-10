@extends('layouts.app')

@section('styles')
    <style>
        /*.cortar{*/
        /*width:200px;*/
        /*padding:20px;*/
        /*text-overflow:ellipsis;*/
        /*white-space:nowrap;*/
        /*overflow:hidden;*/
        /*transition: all 1s;*/
        /*}*/
        /*.cortar:hover {*/
        /*height: 100%;*/
        /*white-space: initial;*/
        /*overflow:visible;*/
        /*cursor: pointer;*/
        /*}*/
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="container col-lg-12">
            <div class="card" style="">
                <div class="card-body">
                    <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                        <div class="tab-pane show active" id="pills-home" role="tabpanel"
                             aria-labelledby="pills-home-tab">
                            <div class="container-home" id="main-content">
                                <div class="col-lg-12">
                                    <div class="justify-content-center">
                                        <div class="row-card-no-pd " id="title">
                                            <div class="card-body text-center" id="home-title">
                                                <h1 class="pb-2 fw-bold">¡Bienvenido a Cornel.io!</h1>
                                                <img src="https://cornelio.network/assets/img/lookingup.png" alt="navbar brand" class="navbar-brand"
                                                     style="max-width: 20vw">
                                                <h4 class="pb-2 fw-bold">¡Comencemos! vamos  ver qué dicen las Redes Sociales...</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="chart-container">
                                    <div class="row">

                                        {{----------- Top 10 de páginas con más publicaciones en las últimas 24 horas --------------}}
                                        <div class="col-md-4">
                                            <div class="card card-primary bg-primary-gradient">
                                                <div class="card-body">
                                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold" align="center">
                                                        Top 10 de páginas con más publicaciones
                                                    </h5>

                                                    <div class="table-responsive">
                                                        <table class="table table-head-bg-primary mb-3" style="color: white;">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">Posición</th>
                                                                <th scope="col">Páginas de Facebook</th>
                                                                <th scope="col">Cant. Publicaciones</th>
                                                                <th scope="col">Nube palabra</th>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($pages as $page)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{$page->page_name}}</td>
                                                                    <td>{{ $page->count }}</td>
                                                                    <td onclick="getDatosPage({{$page->page_id}})"><i class="fas fa-cloud"></i></td>

                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <p style="text-align: center;"> Datos: Últimas <span>24 horas.</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{----------------------------- Total de interacciones -------------------------------------}}
                                        <div class="col-md-4">
                                            <div id="top-marks" class="card card-light bg-light-gradient">
                                                <div class="card-body">
                                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold" align="center">Total de Publicaciones</h5>
                                                    <h1 class="mb-4 fw-bold" align="center">{{ $temasCount }}</h1>
                                                    <h5 class="mt-3 b-b1 pb-2 mb-5 fw-bold" align="center">Generadas por mis temas</h5>
                                                    <hr>
                                                    <h5 class="pb-2 fw-bold" align="center">Temas</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-head-bg-primary mb-3">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">Top 10</th>
                                                                <th scope="col">Tema</th>
                                                                <th scope="col">Cant. Publicaciones</th>
                                                                <th scope="col">Nube palabra</th>

                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($temas as $tema)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $tema->subcategory->name }}</td>
                                                                    <td>{{ $tema->count }}</td>
                                                                    <td onclick="getDatos({{$tema->subcategoria_id}})"><i class="fas fa-cloud"></i></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <p style="text-align: center;">Datos: Últimas <b>72 horas.</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{------------------------- Top 10 de palabras más populares del día -----------------------}}
                                        <div class="col-md-4">
                                            <div class="card card-primary bg-primary-gradient">
                                                <div class="card-body">
                                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold" align="center">
                                                        Top 10 de palabras más populares del día
                                                    </h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-head-bg-primary mb-3" style="color: white;">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col"> Posición </th>
                                                                <th scope="col"> Tema </th>
                                                                <th scope="col"> Cant. publicaciones </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($responseTemas as $temas)
                                                                @foreach($temas as $tema)
                                                                    @foreach($tema['datos'] as $datosTemas)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td><div class="cortar">{{ $datosTemas['word'] }}</div></td>
                                                                            <td>{{ $datosTemas['count'] }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                            </tbody>

                                                        </table>
                                                        <p align="center"> Datos: Palabras del día <b><?php echo date("j/n/Y");?></b>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--------------- Top 10 de los temas más populares del día de los contenidos --------------}}

                                        <div class="col-md-12 row justify-content-center">
                                            <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                                Top 10 de los temas más populares del día de los contenidos

                                            </h5>
                                        </div>

                                        {{---------------------------- Burbuja de las palabras de los contenidos ------------------}}
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" id="get_bubbles" onclick="getBubbles()">Burbujas de palabras</button>
                                        <div id="chart" class="container"></div>

                                        {{---------------------- Top 1o de palabras más populares de las categorias ------------------}}
                                        @foreach($response as $contenido)
                                            @foreach($contenido as $contenidos)
                                                <div class="col-md-6">
                                                    <div class="card card-light bg-light-gradient">
                                                        <div class="card-body">
                                                            <h5 class="pb-2 fw-bold" align="center">Categoría - {{ $contenidos['name'] }}</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-head-bg-primary mb-3" >
                                                                    <thead>
                                                                    <tr>
                                                                        <th scope="col"> Posición </th>
                                                                        <th scope="col"> Tema </th>
                                                                        <th scope="col"> Cant. publicaciones </th>
                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                    @foreach($contenidos['datos'] as $datos)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td><div class="cortar">{{ $datos['word'] }}</div></td>
                                                                            <td>{{ $datos['count'] }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                                <p style="text-align: center;">Palabras del día <b><?php echo date("j/n/Y");?></b> para la categoría {{ $contenidos['name'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach

                                        {{-------------------- Top 10 de las publicaciones con mayor interaccion ------------------}}
                                        <div class="col-md-12">
                                            <br><br>
                                            <div class="card card-primary bg-primary-gradient">
                                                <div class="card-body">
                                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold row justify-content-center" align="center">
                                                        Top 10 de publicaciones con más interacciones
                                                    </h5>
                                                    <div class="table-responsive" style="overflow-x:auto;">
                                                        <table class="table table-head-bg-primary mb-3" style="color: white;">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col"> Fecha </th>
                                                                <th scope="col"> Página </th>
                                                                <th scope="col"> Contenido </th>
                                                                <th scope="col"> <img name="like" src="{{ asset('reacciones/like.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="love" src="{{ asset('reacciones/love.png') }}"  alt="Love" title="Love" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="Hahaha" src="{{ asset('reacciones/hahaha.png') }}" alt="Hahaha" title="Hahaha" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="Wow" src="{{ asset('reacciones/wow.png') }}" alt="Wow" title="Wow" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="Sad" src="{{ asset('reacciones/sad.png') }}" alt="Sad" title="Sad" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="Angry" src="{{ asset('reacciones/angry.png') }}" alt="Angry" title="Angry" style="width: 18px; vertical-align: middle"> </th>
                                                                <th scope="col"> <img name="comentarios" src="{{ asset('reacciones/shared.png') }}" alt="comentarios" title="Comentarios" style="width: 18px; vertical-align: middle"> </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($topData as $data)
                                                                <tr>
                                                                    <td>{{ $data['date'] }}</td>
                                                                    <td>{{$data['name'] }}</td>
                                                                    <td><div class="cortar">{{$data['content'] }}</div></td>
                                                                    <td>{{$data['reaction']->likes }}</td>
                                                                    <td>{{ $data['reaction']->love }}</td>
                                                                    <td>{{ $data['reaction']->haha }}</td>
                                                                    <td>{{ $data['reaction']->wow }}</td>
                                                                    <td>{{ $data['reaction']->sad }}</td>
                                                                    <td>{{ $data['reaction']->angry }}</td>
                                                                    <td>{{ $data['reaction']->shared }}</td>
                                                                </tr>

                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        {{--style="text-align: center;"--}}
                                                        <p style="text-align: center;"> Datos: Últimas <b>24 horas</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        {{---------------------------------- Graficas de los temas --------------------------------}}
                                        <div class="col-md-12 row justify-content-center">
                                            <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                                <i class="fas fa-chart-bar"></i> Sentimiento de Conversaciones
                                            </h5>
                                            <button type="button" class="btn btn-outline-warning btn-lg btn-block" id="get_tema" onclick="getTema()">Sentimientos</button>

                                            <div class="col-md-12 row justify-content-center" id="estadistica"></div>
                                            <p style="text-align: center;">Datos: Últimas <b>24 horas</b></p>
                                        </div>


                                        {{---------------------------- Red de los temas ------------------}}
                                        <button type="button" class="btn btn-outline-success btn-lg btn-block"   id="get_network" onclick="Network()">Árbol de datos</button>
                                        <div id="network" class="col-md-12"></div>

                                        <div id="NetworkDetail" class="col-md-12"></div>


                                        {{------------------------------------ Nube de temas --------------------------------------}}
                                        <div id="modalCloud" data-content='' class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal content-->
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Nube de Palabras</h3>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                    </div>
                                                    <div class="modal-body">

                                                        <div id="prueba">
                                                            <div id="cloud"></div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{------------------------------------ Nube de pagina --------------------------------------}}
                                        <div id="modalPage" data-content='' class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal content-->
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Nube de Palabras</h3>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                    </div>
                                                    <div class="modal-body">

                                                        {{--<div id="prueba">--}}
                                                        <div id="cloudPage"></div>
                                                        {{--</div>--}}

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/wordcloud.js"></script>
    <script src="https://code.highcharts.com/modules/networkgraph.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    {{--<script src="https://code.highcharts.com/modules/heatmap.js"></script>--}}
    {{--<script src="https://code.highcharts.com/9.1/highcharts-more.js"></script>--}}
    {{--<script src="https://code.highcharts.com/modules/exporting.js"></script>--}}
    <script>
        //$(document).ready(getTema(), getBubbles());
        //$(document).ready(getTema(), getBubbles(), getNetwork(), getNetworkDetail() );
        //$(document).ready(getTema());
        document.getElementById("chart").hidden = true;

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        /********************************** Nube de palabra de los temas ***********************************************/
        function getDatos(tema) {
            let datos = {subcategoria_id:tema};
            $('#modalCloud').modal();
            axios.post('{{ route('get.cloudWord') }}', datos).then(response => {
                if(response.data.length>0){
                    $('#cloud').html('En un momento');
                    //$('#cloud').append('');
                    procesarComentarios(response.data);
                }else{
                    $('#cloud').html('<h3>No hay suficientes datos!</h3>');
                }

            });

        }

        function procesarComentarios(data) {
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas ', '.', ' .com ', '/',
                '?', ' ref ', '=', ' share ', '¡', '🤦🏻‍♀', '🥱','🤬', '👏', '✌', '😱','😱', '😡', '💩','🖤','❤','😂','💪', '🤣',
                '🏆','🤫','😁','⚫','🔴','😭','🙄', '🤷🏼‍♀', '🤷‍♂', '🤦🏻‍♂', '🤔','🤡', '"', '&quot', ' com ', 'https:', 'www'

            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                var texto=posts.comment;
                palabras.forEach(function (palabra, index3) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ");
                        // texto ++;
                    }
                });
                comentarios += texto;
                //comentarios+=" "+texto;
                //console.log(comentarios);

            });
            wordCloudComment(comentarios)
        }

        function wordCloudComment(comentario) {
            var text = comentario;
            var lines = text.split(/[,\. ]+/g),
                data = Highcharts.reduce(lines, function (arr, word) {
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

            data=data.sort(function (a, b){
                return (b.weight - a.weight)
            });
            //console.log(data);

            var filtrado=[];
            var temporal;
            var hasta=data.length;
            if(hasta>60){
                hasta=60;
            }
            for(i=0;i<hasta;i++){
                var x= data[i].name;
                if(x.length>3){
                    temporal=data[i];
                    filtrado.push(temporal);
                }
            }

            Highcharts.chart('cloud', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
        }

        /********************************** Nube de palabra de las paginas *********************************************/
        function getDatosPage(page) {
            let datos = {page_id:page};
            $('#modalPage').modal();
            axios.post('{{ route('get.cloudWord') }}', datos).then(response => {
                if(response.data.length>0){
                    $('#cloudPage').html('En un momento');
                    //$('#cloud').append('');
                    procesarComentariosPage(response.data);
                }else{
                    $('#cloudPage').html('<h3>No hay suficientes datos!</h3>');
                }

            });

        }

        function procesarComentariosPage(data) {
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas ', '.', ' .com ', '/',
                '?', ' ref ', '=', ' share ', '¡', '🤦🏻‍♀', '🥱','🤬', '👏', '✌', '😱','😱', '😡', '💩','🖤','❤','😂','💪', '🤣',
                '🏆','🤫','😁','⚫','🔴','😭','🙄', '🤷🏼‍♀', '🤷‍♂', '🤦🏻‍♂', '🤔','🤡', '"', '&quot', ' com ', 'https:', 'www'

            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                var texto=posts.comment;
                palabras.forEach(function (palabra, index3) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ");
                    }
                });
                comentarios += texto;

            });
            wordCloudCommentPage(comentarios)
        }

        function wordCloudCommentPage(comentario) {
            var text = comentario;
            var lines = text.split(/[,\. ]+/g),
                data = Highcharts.reduce(lines, function (arr, word) {
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

            data=data.sort(function (a, b){
                return (b.weight - a.weight)
            });
            //console.log(data);

            var filtrado=[];
            var temporal;
            var hasta=data.length;
            if(hasta>60){
                hasta=60;
            }
            for(i=0;i<hasta;i++){
                var x= data[i].name;
                if(x.length>3){
                    temporal=data[i];
                    filtrado.push(temporal);
                }
            }

            Highcharts.chart('cloudPage', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
        }

        /********************************** Sentimientos de los tema ***************************************************/
        function getTema() {
            showProcessing();
            document.getElementById("get_tema").hidden = true;
            axios.post('{{ route('get.FeelingComments') }}').then(response => {
                if (response.data) {
                    chartTema(response.data);
                    hideProcessing();
                } else {
                    $('#estadistica').append('<h1><b>No se encuentran datos a mostrar<b></h1>');
                    hideProcessing();
                }
            });
        }

        function chartTema(data) {
            var reacciones = data.Tema;
            reacciones.forEach(function (reaccion, index) {
                $('#estadistica').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="'+ reaccion.Tema+'"></div>');
                Highcharts.chart(reaccion.Tema, {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },

                    title: {
                        text: 'Sentimiento de conversación <br>del tema  <b>'+ reaccion.Tema + '</b> <br>en las Últimas <b>24 horas</b>',

                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }

                    },

                    colors: ['#317f43', '#CB3234', '#ffff00'],
                    series: [{
                        type: 'pie',
                        name: 'Porcentaje de Reacciones',
                        innerSize: '50%',
                        data: [
                            ['Positivos ' , reaccion.interation.positivo],
                            ['Negativos ' , reaccion.interation.negativo],
                            ['Neutrales ' , reaccion.interation.neutral],
                        ]
                    }]



                });
            });

        }

        /*********************************** Burbuja de palabras de los contenidos *************************************/
        function getBubbles() {
            showProcessing();
            document.getElementById("get_bubbles").hidden = true;
            axios.post('{{ route('get.wordContent') }}').then(response => {
                if(response.data.length>0){
                    document.getElementById("chart").hidden = false;
                    chartBubbles(response.data);
                    hideProcessing();
                } else {
                    hideProcessing();
                    document.getElementById("chart").hidden = true;
                }
            });
        }

        function chartBubbles(data) {
            Highcharts.chart('chart', {
                chart: {
                    type: 'packedbubble',
                    height: '270%',
                },
                title: {
                    text: 'Burbuja de temas generales'
                },
                tooltip: {
                    useHTML: true,
                    pointFormat: '<b>{point.name}:</b> {point.value}'
                },
                plotOptions: {
                    packedbubble: {
                        minSize: '20%',
                        maxSize: '100%',
                        zMin: 0,
                        zMax: 1000,
                        layoutAlgorithm: {
                            gravitationalConstant: 0.05,
                            splitSeries: true,
                            seriesInteraction: false,
                            dragBetweenSeries: true,
                            parentNodeLimit: true
                        },
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}',
                            filter: {
                                property: 'y',
                                operator: '>',
                                value: 250
                            },
                            style: {
                                color: 'black',
                                textOutline: 'none',
                                fontWeight: 'normal'
                            }
                        }
                    }
                },
                series: data
            });
        }

        /*********************************** gráfico de red de los tenas *************************************/
        function Network(){
            document.getElementById("get_network").hidden = true;
            showProcessing();
            getNetwork();
            getNetworkDetail()
        }

        function getNetwork() {
            axios.post('{{ route('get.NetworkTopics') }}').then(response => {
                if (response.data) {
                    networkgraph(response.data);
                } else {
                    return 'No se puede mostrar los datos';
                }
            });
        }

        function networkgraph(data){
            //TEMA  -  PALABRA  - PAGINA (LAS PAGINAS QUE SE ENCUENTRA LAS PALABRAS) - CANTIDAD DE PUBLICACIONES DE LAS PAGINAS
            let datos = data.chart;
            let name = data.name;

            Highcharts.addEvent(
                Highcharts.Series,
                'afterSetOptions',
                function (e) {
                    var colors = Highcharts.getOptions().colors,
                        i = 0,
                        nodes = {};

                    if (
                        this instanceof Highcharts.seriesTypes.networkgraph &&
                        e.options.id === 'lang-tree'
                    ) {
                        e.options.data.forEach(function (link) {

                            if (link[0] === name) {
                                nodes[name] = {
                                    id: name,
                                    marker: {
                                        radius: 50
                                    }
                                };
                                nodes[link[1]] = {
                                    id: link[1],
                                    marker: {
                                        radius: 30
                                    },
                                    color: colors[i++]
                                };
                            } else if (nodes[link[0]] && nodes[link[0]].color) {
                                nodes[link[1]] = {
                                    id: link[1],
                                    color: nodes[link[0]].color
                                };
                            }
                        });

                        e.options.nodes = Object.keys(nodes).map(function (id) {
                            return nodes[id];
                        });
                    }
                }
            );

            Highcharts.chart('network', {
                chart: {
                    type: 'networkgraph',
                    height: '130%'
                },
                title: {
                    text: 'Árbol de datos que contiene la compañía'
                },
                subtitle: {
                    text: 'Datos generados en las últimas 72 horas'
                },
                plotOptions: {
                    networkgraph: {
                        keys: ['from', 'to'],
                        layoutAlgorithm: {
                            enableSimulation: true,
                            friction: -0.09
                        }
                    }
                },
                series: [{
                    dataLabels: {
                        enabled: true,
                        linkFormat: ''
                    },
                    id: 'lang-tree',
                    data: datos
                }]
            });
        }


        /*********************************** gráfico de red de los tenas *************************************/

        function getNetworkDetail() {
            axios.post('{{ route('get.NetworkDetail') }}').then(response => {
                hideProcessing();
                if (response.data) {
                    NetworkDetail(response.data.chart);
                } else {
                    return 'No se puede mostrar los datos';
                }
            });
        }

        function NetworkDetail(data) {
            let tema = data.tema;
            tema.forEach(function (temaC, index){
                let datos = temaC.chart;
                let name = temaC.name;

                $('#NetworkDetail').append('<div class="col-md-10 mt-5 b-b1 pb-2 mb-4" id="Tema'+name+'"></div>');

                Highcharts.addEvent(
                    Highcharts.Series,
                    'afterSetOptions',
                    function (e) {
                        var colors = Highcharts.getOptions().colors,
                            i = 0,
                            nodes = {};

                        if (
                            this instanceof Highcharts.seriesTypes.networkgraph &&
                            e.options.id === 'lang-tree'
                        ) {
                            e.options.data.forEach(function (link) {

                                if (link[0] === name) {
                                    nodes[name] = {
                                        id: name,
                                        marker: {
                                            radius: 50
                                        }
                                    };
                                    nodes[link[1]] = {
                                        id: link[1],
                                        marker: {
                                            radius: 30
                                        },
                                        color: colors[i++]
                                    };
                                } else if (nodes[link[0]] && nodes[link[0]].color) {
                                    nodes[link[1]] = {
                                        id: link[1],
                                        color: nodes[link[0]].color
                                    };
                                }
                            });

                            e.options.nodes = Object.keys(nodes).map(function (id) {
                                return nodes[id];
                            });
                        }
                    }
                );

                Highcharts.chart('Tema'+name, {
                    chart: {
                        type: 'networkgraph',
                        height: '170%'
                    },
                    title: {
                        text: 'Árbol de datos que contiene el tema '+ name+''
                    },
                    subtitle: {
                     text: 'Datos generados en las últimas 72 horas'
                     },
                    plotOptions: {
                        networkgraph: {
                            keys: ['from', 'to'],
                            layoutAlgorithm: {
                                enableSimulation: true,
                                friction: -0.09
                            }
                        }
                    },
                    series: [{
                        dataLabels: {
                            enabled: true,
                            linkFormat: ''
                        },
                        id: 'lang-tree',
                        data:  datos
                    }]
                });
            });
        }

        function showProcessing() {
            $("body").append(
                '<div id="overlay-processing" style="background: #F0F0F0; height: 100%; width: 100%; opacity: .9; padding-top: 10%; position: fixed; text-align: center; top: 0;z-index: 2147483647;">' +
                '<h2 style="color: #333333" id="estado">Leyendo datos</h2>' +
                '<i class="fa fa-cog fa-spin fa-3x fa-fw" aria-hidden="true"></i>' +
                '<span class="sr-only">En proceso</span></div>'
            );
        }

        function hideProcessing() {
            $('body').find('#overlay-processing').remove();
        }
    </script>
@endsection