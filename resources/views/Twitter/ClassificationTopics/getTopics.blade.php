@extends('layouts.app')

@section('styles')
    @include('Cornelio.Classification.Sentiment.Style')
@endsection

@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Clasificación de los temas de {{$topics->name}}
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Se cuenta con la opción de clasificar los comentarios y ver información de más que cuenta un tweet en específico o los tweets en general que se encuentra en el tema seleccionado.">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opción
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" onclick="getInformation()">Reporte</a>
                                    <a class="dropdown-item" onclick="getDataTwitter()"  data-toggle="modal" data-target="">Páginas</a>
                                    <a class="dropdown-item" onclick="getStatusTopics()" data-toggle="modal" data-target="" data-target="#pieCloud">Estatus</a>
                                    <a class="dropdown-item" onclick="getCloudTwitter()" data-toggle="modal" data-target="" data-target="#modalCloud">Palabras</a>
                                </div>
                            </div>
                        </div>

                        <br><br>
                        <div class="row justify-content-center">

                            @foreach($tweets as $tweet)
                                <div class="col-md-4" id="tweet-{{$tweet->id_tweet}}">
                                    <div class="post-wrapper">
                                        <p class="post-meta">
                                            <button type="button" id="actualizar-{{$tweet->id_tweet}}" class="badge badge-primary"  onclick="updateTweet(this.id);" >Actualizar</button>
                                            <button type="button" id="sta-{{$tweet->id_tweet}}"        class="badge badge-warning " onclick="getStatus(this.id);"  data-toggle="modal" data-target="pie">Estatus</button>
                                            <button type="button" id="cloud-{{$tweet->id_tweet}}"      class="badge badge-info"     onclick="getCloud(this.id);"   data-toggle="modal" data-target="pie">Palabras</button>
                                            <button type="button" id="des-{{$tweet->id_tweet}}"        class="badge badge-danger"   onclick="desclacificar(this);" title="Eliminar">Desclasificar</button>
                                        </p>
                                    </div>
                                    {{--style=" height: 250px;overflow: auto"--}}
                                    <div class="card mb-4" style=" height: 550px;overflow: auto">
                                        <div class="card-body">
                                            <h3 style="color: #0A5A97">{{$tweet->tweet->name}}</h3><br>
                                            @if(isset($tweet->attachment))
                                                @if($tweet->attachment->picture != null)
                                                    <img class="card-img-top img-fluid w-100" src="{{$tweet->attachment->picture}}" alt="">
                                                @endif
                                            @endif

                                            @if( isset($tweet->tweet->created_time))
                                                <p class="card-title mb-1 small">{{$tweet->tweet->created_time}}</p>
                                            @endif
                                            @if(isset($tweet->tweet->content))
                                                <p class="card-text small">{{$tweet->tweet->content}} </p>
                                            @endif
                                            @if(isset($tweet->tweet->expanded_url) && $tweet->tweet->expanded_url != null )
                                                <br><b><a href="{{$tweet->tweet->expanded_url}}" target="_blank">Ver más</a></b>
                                            @endif

                                            <hr class="my-0">
                                            <div class="" id="reactions-{{$tweet->id_tweet}}">
                                                @if($tweet->reactions)
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Like" src="{{asset("/reacciones/like_twitter.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                        <label id="like-{{$tweet->id_tweet}}" for="Like">{{$tweet->reactions['favorite_count']}}</label>
                                                    </div>
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="retweet" src="{{asset("/reacciones/retweet.png")}}" alt="retweet" title="retweet" style="width: 20px; vertical-align: middle">
                                                        <label id="retweet-{{$tweet->id_tweet}}" for="retweet">{{$tweet->reactions['retweet_count']}}</label>
                                                    </div>
                                                @else
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Like" src="{{asset("/reacciones/like_twitter.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                        <label id="like-{{$tweet->id_tweet}}" for="Like">0</label>
                                                    </div>
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Retweet" src="{{asset("/reacciones/retweet.png")}}" alt="Retweet" title="Retweet" style="width: 20px; vertical-align: middle">
                                                        <label id="retweet-{{$tweet->id_tweet}}" for="Retweet">0</label>
                                                    </div>
                                                @endif()
                                            </div>
                                            <hr class="my-0">
                                            {{--<div class="card-body" style=" height: 250px;overflow: auto" id="comments-{{$post->post_id}}" >--}}
                                            <div class="card-body" style="" id="comments-{{$tweet->id_tweet}}">
                                                @foreach($tweet->comments as $comment)
                                                    <div class="received_withd_msg">
                                                        <div style="padding: 2px" class="rounded" >
                                                            <p class="card-text small">
                                                                <b><a href="https://twitter.com/{{$comment->username}}/status/{{$comment->comment_id}}" target="_blank"><strong>{{ $comment->name}}</strong></a></b>
                                                                <br>
                                                                {{ $comment->content }}
                                                            </p>
                                                            {{$comment->created_time}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row justify-content-center pt-2"  >
                                                        <label style="margin-left: 10px;">
                                                            @php
                                                                $status = \App\Models\Twitter\TwitterSentiment::where('comment_id', $comment->comment_id)->first();
                                                            @endphp
                                                            @if(isset($status) && $status != null)
                                                                <input id="checkbox-{{$comment->comment_id}}" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)" {{ ($status['status'] == '1') ? 'checked=checked' : '' }}> Verificado
                                                            @else
                                                                <input id="checkbox-{{$comment->comment_id}}" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
                                                            @endif
                                                        </label>
                                                        <button style="margin-left: 10px;" id="{{$comment->comment_id}}"  data-comment="" data-comm-id="{{$comment->comment_id}}" onclick="generateSentiment(this)" class="btn btn-xs btn-info">Clasificar</button>
                                                    </div>
                                                    <div id="sen-{{$comment->comment_id}}"></div>
                                                    <div id="persen-{{$comment->comment_id}}" style="margin-top: 5px"></div>
                                                    <hr class="my-0"><br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row justify-content-center">
                            {{$tweets->appends(request()->except('page'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REPORTE-->
    <div id="reporte" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h3 class="modal-title">Reporte</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="reporteContenido"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NUBE DE PALABRAS DE TODAS LAS PUBLICACIONES-->
    <div id="modalCloud" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h3 class="modal-title">Nube de Palabras</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div id="cloud"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NUBE DE PALABRAS DE SOLO UN COMENTARIO-->
    <div id="modalCloudComment" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h3 class="modal-title">Nube de Palabras</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div id="cloudComment"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ANALISIS DE PUBLICACION-->
    <div id="modalPie" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h3 class="modal-title">Análisis de publicación</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div id="pieIntroduccion"></div>
                    <div id="pieChart"></div>
                    <div id="pieTabla"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGINAS-->
    <div id="modalPage" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h4 class="modal-title">Páginas</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div id="tablaPage"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CHART-->
    <div id="modalChart" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Chart</h4>
                </div>
                <div class="modal-body">
                    <canvas id="canvas"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SENTIMIENTO-->
    <div id="modalAuto" data-content='' class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h3 class="modal-title">Clasificar sentimento</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <label for="tipo">Tipo de Clasificación</label>
                    <br><br>
                    <select id="tipo" class='form-control'>
                        <option value="Positivo">Positivo</option>
                        <option value="Negativo">Negativo</option>
                    </select>
                    <input type="hidden" id="posteoAuto" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="procesarSentiment()" class="btn btn-outline-success"
                            data-dismiss="modal">Procesar
                    </button>
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
        var name = '';

        $(document).ready(getSentiment());

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        /*----------------------------------- MUESTRA LA CLASIFICACION DE LOS SENTIEMIENTOS DE LOS COMENTARIOS ---------*/

        function getSentiment() {
            let datos = {"topics_id":"<?php echo $_GET['topics_id'] ?>"};
            axios.post('{{ route('classificarionTwitter.getSentiment') }}', datos).then(response => {
                resultado = (response.data);
                resultado.forEach(function (comment, index) {
                    if(comment != null){
                        selectSentiment(comment);
                    }
                });
            });
        }

        function selectSentiment(data) {
            let elemento = document.getElementById(data.comment_id);
            if(elemento){
                if (data.sentiment == 'Neutral') {
                    elemento.setAttribute("data-comment", "Neutral");
                    $('#'+data.comment_id).addClass('neutral');
                }

                if (data.sentiment == 'Negativo') {
                    elemento.setAttribute("data-comment", "Negativo");
                    $('#'+data.comment_id).addClass('negativo');
                }

                if (data.sentiment == 'Positivo') {
                    elemento.setAttribute("data-comment", "Positivo");
                    $('#'+data.comment_id).addClass('positivo');
                }

                if (data.sentiment != 'Negativo' && data.sentiment != 'Positivo' && data.sentiment != 'Neutral') {
                    if (elemento == null) {
                        return false;
                    }
                    elemento.setAttribute("data-comment", "" + data.sentiment + "");
                    $('#' + data.comment_id).addClass('perso');
                }
            }
        }

        /*----------------------------------- MUESTRA LOS SENTIMIENTOS  ------------------------------------------------*/

        function generateSentiment(commentario) {
            let comment    = commentario.id;
            let sent       = commentario.getAttribute('data-comment');
            let sentiments = `<div class="sentimientos">
                                    <img class="img" id="pos-`+ comment +`" onclick="actualizar(this.id)" name="Positive" src="{{asset("/reacciones/positive.png")}}" alt="Positivo" title="Positivo" style=" width:40px; vertical-align: middle" class="sentimiento badge badge-count" >
                                    <img class="img" id="neu-`+ comment +`" onclick="actualizar(this.id)" name="Neutral"  src="{{asset("/reacciones/neutral.png")}}"  alt="Neutral"  title="Neutral"  style=" width:40px; vertical-align: middle" class="sentimiento" >
                                    <img class="img" id="neg-`+ comment +`" onclick="actualizar(this.id)" name="Negativo" src="{{asset("/reacciones/negative.png")}}" alt="Negativo" title="Negativo" style=" width:40px; vertical-align: middle" class="sentimiento" >
                              </div>`;
            $(".sentimientos").remove();
            $("#sen-"+comment).html(sentiments);
            if(sent=='Neutral'){
                $('#neu-'+comment).addClass('sel');
            }
            if(sent=='Positivo'){
                $('#pos-'+comment).addClass('sel');
            }
            if(sent=='Negativo'){
                $('#neg-'+comment).addClass('sel');
            }
        }

        /*----------------------------------- CLASIFICA LOS SENTIMIENTOS DE LOS COMENTARIOS ----------------------------*/

        function actualizar(id) {
            let comment   = id;
            sentiment = "";

            if((comment.indexOf("pos") > -1)==true){
                comment=comment.replace('pos-','');
                sentiment="Positivo";
                $('#'+id).addClass('sel');
                $('#neg-'+comment).removeClass('sel');
                $('#neu-'+comment).removeClass('sel');
                var elemento = document.getElementById(comment);
                elemento.setAttribute("data-comment", "Positivo");
                $('#'+comment).addClass('positivo');
                $('#'+comment).removeClass('neutral');
                $('#'+comment).removeClass('negativo');
                $('#'+comment).removeClass('perso');

            }
            if((comment.indexOf("neg") > -1)==true){
                comment= comment.replace('neg-','');
                sentiment="Negativo";
                $('#'+id).addClass('sel');
                $('#pos-'+comment).removeClass('sel');
                $('#neu-'+comment).removeClass('sel');
                var elemento = document.getElementById(comment);
                elemento.setAttribute("data-comment", "Negativo");
                $('#'+comment).addClass('negativo');
                $('#'+comment).removeClass('positivo');
                $('#'+comment).removeClass('neutral');
                $('#'+comment).removeClass('perso');

            }
            if((comment.indexOf("neu") > -1)==true){
                comment= comment.replace('neu-','');
                sentiment="Neutral";
                $('#'+id).addClass('sel');
                $('#neg-'+comment).removeClass('sel');
                $('#pos-'+comment).removeClass('sel');
                var elemento = document.getElementById(comment);
                elemento.setAttribute("data-comment", "Neutral");
                $('#'+comment).addClass('neutral');
                $('#'+comment).removeClass('positivo');
                $('#'+comment).removeClass('negativo');
                $('#'+comment).removeClass('perso');

            }
            $('.selPerso').removeClass('selPerso');
            datos = {comment, sentiment};
            axios.post('{{ route('classificarionTwitter.sentiment',$company) }}', datos);
        }

        /*----------------------------------- VERIFICA LOS SENTIMIENTOS DE LOS COMENTARIOS -----------------------------*/
        function comprobarCheck(id) {
            var estado="";
            if ($('#'+id).is(':checked') ) {
                estado=1;
            } else {
                estado=0;
            }
            actualizarVerificar(id,estado);
        }

        function actualizarVerificar(id,estado) {
            let comment_id= id.replace('checkbox-','');
            datos = {comment_id,estado};
            axios.post('{{ route('classificarionTwitter.status',$company) }}', datos);
        }


        /***************************************************************************************************************
                                                OPCIONES DE CADA UNA DE LAS PUBLICACIONES
         ***************************************************************************************************************/

        function updateTweet(tweetId) {
            loadingPanel();
            let id_tweet = tweetId.replace('actualizar-', '');
            updateReaction(id_tweet);
            updateComment(id_tweet);
            setTimeout(()=>{loadingPanel();},5000)
        }

        function updateReaction(id_tweet) {
            let datos = {"id_tweet":id_tweet};
            axios.post('{{ route('classificarionTwitter.updateReaction') }}', datos).then(response => {
                document.getElementById('like-'+id_tweet).innerHTML = response.data.favorite_count;
                document.getElementById('retweet-'+id_tweet).innerHTML = response.data.retweet_count;
            });
        }

        function updateComment(id_tweet) {
            let datos = {"id_tweet":id_tweet};
            axios.post('{{ route('classificarionTwitter.updateComment') }}', datos).then(response => {
                if(response.data.length > 0){
                    $('#comments-' + id_tweet).html('');
                    generateComments(response.data, id_tweet);
                }
            });
        }

        function generateComments(result, id_tweet) {
            let commentsList = '';
            result.forEach(function (comment, index) {
                commentsList += `<div class="received_withd_msg">
                                    <div style="padding: 2px" class="rounded" >
                                        <p class="card-text small">
                                            <b><a href="https://twitter.com/`+comment.username+`/status/`+comment.comment_id+`" target="_blank"><strong>`+comment.name+`</strong></a></b>
                                            <br>
                                            `+comment.content+`
                                        </p>
                                        `+comment.created_time+`
                                    </div>
                                </div>

                                <div class="row justify-content-center pt-2"  >
                                    <label style="margin-left: 10px;">
                                        <input id="checkbox-`+comment.comment_id+`" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
                                    </label>
                                    <button style="margin-left: 10px;" id="`+comment.comment_id+`"  data-comment="" data-comm-id="`+comment.comment_id+`" onclick="generateSentiment(this)" class="btn btn-xs btn-info">Clasificar</button>
                                </div>
                                 <div id="sen-`+comment.comment_id+`"></div>
                                 <div id="persen-`+comment.comment_id+`" style="margin-top: 5px"></div>
                                 <hr class="my-0"><br>`;
                $('#comments-' + id_tweet).html('');
                $('#comments-' + id_tweet).append(commentsList);
            });
        }

        /*----------------------------------- MUESTRA EL ESTATUS DE LOS SENTIMIENTOS  ----------------------------------*/

        function getStatus(tweetId) {
            let id_tweet  = tweetId.replace('sta-', ''),
                topics_id = "<?php echo $_GET['topics_id'] ?>",
                datos     = {id_tweet, topics_id};
            axios.post('{{ route('classificarionTwitter.getSentimentTweet') }}', datos).then(response => {
                console.log(response);
                process_Statics(response.data.count, response.data.feeling, 0);
            });

            $('.highcharts-container ').remove();
            $('#pieProcesar').removeClass('procesar');
            $('#modaltablaReacciones').remove();
            $('#modalPie').modal();
        }

        function process_Statics(comments, sentiments, tipo) {
            var total      = comments,
                reacciones = sentiments,
                positivos  = 0,
                negativos  = 0,
                neutral    = 0,
                sin;
            reacciones.forEach(function (reaccion, index) {
                if (reaccion.sentiment == 'Positivo') {
                    positivos = positivos + 1
                }
                if (reaccion.sentiment == 'Negativo') {
                    negativos = negativos + 1
                }
                if (reaccion.sentiment == 'Neutral') {
                    neutral = neutral + 1
                }
            });
            sin = total - (positivos + negativos + neutral);
            pieChart(total, positivos, negativos, neutral, sin, tipo)
        }

        function pieChart(total, positivos, negativos, neutral, sin, tipo) {
            var porPositivos, porNegativos, porNeutral, porSin;
            porPositivos = (positivos / total) * 100;
            porNegativos = (negativos / total) * 100;
            porNeutral = (neutral / total) * 100;
            porSin = (sin / total) * 100;
            Highcharts.chart('pieChart', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: 'Sentimiento<br> de <br>conversación',
                    align: 'center',
                    verticalAlign: 'middle',
                    y: 40
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'white'
                            }
                        },
                        startAngle: -90,
                        endAngle: 90,
                        center: ['50%', '75%']
                    }
                },
                colors: ['#317f43', '#CB3234', '#ffff00', '#ff6347'],
                series: [{
                    type: 'pie',
                    name: 'Porcentaje de Reacciones',
                    innerSize: '50%',
                    data: [
                        ['Positivos ' + parseFloat(porPositivos).toFixed(0) + "%", porPositivos],
                        ['Negativos ' + parseFloat(porNegativos).toFixed(0) + "%", porNegativos],
                        ['Neutrales ' + parseFloat(porNeutral).toFixed(0) + "%", porNeutral],
                        ['Sin Clasificar ' + parseFloat(porSin).toFixed(0) + "%", porSin]
                    ]
                }]
            });
            $('#modaltablaReacciones').remove();
            if (tipo == 1) {
                var reaccionTable = '';
                reaccionTable += '<table id="modaltablaReacciones" class="table table-striped">';
                reaccionTable += '<thead><tr>';
                reaccionTable += '<th>Negativos:</th>';
                reaccionTable += ' <th>Positivos:</th>';
                reaccionTable += ' <th>Neutrales:</th>';
                reaccionTable += '</thead></tr><tbody>';

                reaccionTable += '<tr>';
                reaccionTable += '<td style="color:red; font-weight:bold;">' + negativos + '</td>';
                reaccionTable += ' <td style="color:green; font-weight:bold;">' + positivos + '</td>';
                reaccionTable += ' <td style="color:black; font-weight:bold;">' + neutral + '</td>';
                reaccionTable += '</tr>';

                reaccionTable += '</tbody></table>';
                $('#pieTabla').html(reaccionTable);

            }
            $('#pieProcesar').addClass('procesar');

        }

        /*----------------------------------- MUESTRA NUBE DE PALABRA DE UN TWEET  -------------------------------------*/

        function getCloud(tweetId) {
            loadingPanel();
            let id_tweet  = tweetId.replace('cloud-', ''),
                datos = {id_tweet};
            axios.post('{{ route('classificarionTwitter.tweetComment') }}', datos).then(response => {
                if(response.data.length > 0){
                    processCommentTweet(response.data);
                }
                else{
                    loadingPanel();
                    $('#cloudComment').html('');
                    $('#cloudComment').append('No cuenta con comentarios');

                }
            });
            $('.highcharts-container').remove();
            $('#cloudProcesar').removeClass('procesar');
            $('#modalCloudComment').modal();
        }

        function processCommentTweet(data) {
            var comentariosPost="";
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
                ' el ',' por ','Por ',' como ',' cómo ','Y ',' y ',' un ',' una ',' uno ',' mas ', ' más ',
                ' se ',' no ','No ',' si ','Si ', 'A ',' a ',' en ',' es ','está ',' eso ',' esos ',' pero ',
                'Image/Emoji',' para ',' las ',' su ',' sus ',' esa ','!',' ser ',' sin ',' ya ',' los ',' te ',
                ' me ','Me ',' ja ',' jaja ',' je ',' jeje ',' les ',' la ',' le ',' son ','DE ','QUE ','LA ',' con ',
                'Pero ',' este ',' esta ',' hace ',' poco ',' toda ','Toda ','Todo ',' todo ',' bien ','Bien ',' estos ',
                ' estas ','Estos ','Estas ','Está ',' esto ',' solo ',' cada ',' todos ','Todos ',' nada ',' ellos ',
                '?', 'https', ' http', ':', '/', '.', ',', '@', '...'
            ];
            var i;
            data.forEach( function(tweet, index) {
                var texto=tweet.content;
                palabras.forEach(function (palabra, index2) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ")
                    }
                });
                comentariosPost+=" "+texto;
            });
            wordCloudPost(comentariosPost);
        }

        function wordCloudPost(comentario) {
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

            Highcharts.chart('cloudComment', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
            loadingPanel();
            $('#reporteProcesando').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }

        /*----------------------------------- DESCLASIFICAR ------------------------------------------------------------*/

        function desclacificar(tweetId) {
            let tweet = tweetId.id,
                sub   = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                id    = tweet.replace('des-', '')
                datos = {id, sub};

            r = confirm('Esta seguro que desas desclasificar esta publicación?');
            if(r==true){
                axios.post('{{ route('classificationTweet.DeclassifyTweet',$company) }}', datos).then(response => {
                    if(response.data == 'eliminado'){
                        swal('Exito !', 'Se ha desclasificado', 'success');
                    }
                    else{
                        swal('Error !', 'La publicación ya se encuentra desclasificado', 'info');
                    }
                    $('#tweet-'+id).remove();
                });
            }
        }

        /***************************************************************************************************************
                                                     DROPDOWN DE OPCIONES
         ***************************************************************************************************************/

        function getInformation() {
            loadingPanel();
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                user   = document.getElementById('user').value;
            $('#reporteProcesando').removeClass('procesar');
            $('#reporteContenido').addClass('procesar');
            datos = {user, topics, start, end};
            axios.post('{{ route('classificarionTwitter.getInformation',$company) }}', datos).then(response => {
                if(response.data == 'Error'){
                    swal("Sin datos!", "No se ha almacenado los scrap de los ultmimos comentarios", "warning");
                }
                else{
                    getContent(response.data);
                    $('#reporte').modal();
                    setTimeout(()=>{loadingPanel();},5000)
                }
            });
        }

        function getContent(data) {
            var promedioPost = (data.publicaciones / data.paginas);
            var contenido = '';
            contenido += `<p>El tema ` + data.topics_name + ` ha generado ` + (data.publicaciones).toLocaleString() + ` publicaciones en ` + (data.paginas).toLocaleString() + ` página,
                            para un promedio de ` + parseFloat(promedioPost).toFixed(2) + ` publicaciones del tema por cada página.</p>
                          <p>Sobresale en cantidad de publicaciones la página ` + data.mayorPost.name + ` que realizó ` + (data.mayorPost.postCount).toLocaleString() + `
                             publicaciones. <br> En cantidad de comentarios la página ` + data.mayorComment.name + ` registra ` + (data.mayorComment.comments).toLocaleString() + ` comentarios.</p>
                          <div id="graficos">
                              <p>Las palabras que predominaron en la conversación son las siguientes:</p>
                              <div id="tablaCloud"></div><br>
                              <p>El sentimiento general del tema ha sido el siguiente:</p>
                              <div id="tablaSentimiento"></div>
                          </div>`;
            $('#reporteContenido').html(contenido);
            cloudReporte();
            getStatsSubcategoriaReporte();
        }

        function cloudReporte() {
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                datos  = {start,end, topics};
            axios.post('{{ route('classificarionTwitter.cloudInformation',$company) }}', datos).then(response => {
                procesarComentariosReporte(response.data);
            });
        }

        function procesarComentariosReporte(data) {
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ',
                '?', 'https', ' http', ':', '/', '.', ',', '@', '...'
            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                posts.forEach(function (post, index2) {
                    var texto=post.content;
                    palabras.forEach(function (palabra, index3) {
                        i=0;
                        for(;i!=-1;){
                            i=texto.indexOf(palabra);
                            texto=texto.replace(palabra, " ");

                        }
                    });
                    comentarios+=" "+texto;

                });
            });
            if(comentarios.length > 0){
                wordCloudPostReporte(comentarios)
            }
            else{
                $('#tablaCloud').append('No cuenta con comentarios');
            }
        }

        function wordCloudPostReporte(comentario) {
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

            Highcharts.chart('tablaCloud', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
            $('#cloudProcesar').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }

        function getStatsSubcategoriaReporte() {
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                datos  = {topics, start, end};
            axios.post('{{ route('classificarionTwitter.commentInformation',$company) }}', datos).then(response => {
                procesarStaticsReporte(response.data.count, response.data.comment);
            });
        }

        function procesarStaticsReporte(comments, sentiments) {
            let total      = comments,
                reacciones = sentiments,
                positivos  = 0,
                negativos  = 0,
                neutral    = 0,
                sin;
            reacciones.forEach(function (reaccion, index) {
                if (reaccion.sentiment == 'Positivo') {
                    positivos = positivos + 1
                }
                if (reaccion.sentiment == 'Negativo') {
                    negativos = negativos + 1
                }
                if (reaccion.sentiment == 'Neutral') {
                    neutral = neutral + 1
                }
            });
            sin = total - (positivos + negativos + neutral);
            pieChartReporte(total, positivos, negativos, neutral, sin)
        }

        function pieChartReporte(total, positivos, negativos, neutral, sin) {
            var porPositivos, porNegativos, porNeutral, porSin;
            porPositivos = (positivos / total) * 100;
            porNegativos = (negativos / total) * 100;
            porNeutral = (neutral / total) * 100;
            porSin = (sin / total) * 100;
            Highcharts.chart('tablaSentimiento', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: 'Sentimiento<br> de <br>conversación',
                    align: 'center',
                    verticalAlign: 'middle',
                    y: 40
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'white'
                            }
                        },
                        startAngle: -90,
                        endAngle: 90,
                        center: ['50%', '75%']
                    }
                },
                colors: ['#317f43', '#CB3234', '#ffff00', '#ff6347'],
                series: [{
                    type: 'pie',
                    name: 'Porcentaje de Reacciones',
                    innerSize: '50%',
                    data: [
                        ['Positivos ' + parseFloat(porPositivos).toFixed(0) + "%", porPositivos],
                        ['Negativos ' + parseFloat(porNegativos).toFixed(0) + "%", porNegativos],
                        ['Neutrales ' + parseFloat(porNeutral).toFixed(0) + "%", porNeutral],
                        ['Sin Clasificar ' + parseFloat(porSin).toFixed(0) + "%", porSin]
                    ]
                }]
            });
        }


        /*----------------------------------- MUESTRA INFO LAS PAGINAS QUE SE ENCUENTRAN -------------------------------*/
        function getDataTwitter() {
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                datos  = {topics, start, end};

            loadingPanel();
            axios.post('{{ route('classificarionTwitter.tweetClassification',$company) }}', datos).then(response => {
                processTwitter(response.data);
            });
            $('#pageProcesar').removeClass('procesar');
            $('#modalPage').modal();
            setTimeout(()=>{loadingPanel();},2000)
        }

        function processTwitter(data) {
            let posteos = [],
                longitud = data.length,
                peticiones = 0,
                array;

            data.forEach(function (post, index) {
                peticiones         = peticiones + 1;
                let _data          = {};
                _data.page_id      = post.author_id;
                _data.page_name    = post.page.name;
                _data.post_id      = post.id_tweet;
                _data.commentarios = parseInt(post.comments.length);
                posteos.push(_data);
                if (peticiones == longitud) {
                    array = countTweet(posteos);
                    var ordenado = array.sort(function (a, b) {
                        return (b.count - a.count)
                    });
                        tableTwitter(ordenado);
                }
            })
        }

        function countTweet(original) {
            let consume  = original,
                longitud = consume.length,
                temp     = [],
                produce  = [];
            for (i = 0; i < longitud; i++) {
                if (temp.indexOf(consume[i].page_id) == -1) {
                    temp.push(consume[i].page_id);
                    let _data          = {};
                    _data.id           = consume[i].page_id;
                    _data.name         = consume[i].page_name;
                    _data.commentarios = consume[i].commentarios;
                    _data.count        = 1;
                    produce.push(_data);
                }
                else {
                    for (var j = 0; j < produce.length; j++) {
                        if (produce[j].id === consume[i].page_id) {
                            var _x = parseInt(produce[j].count) + 1;
                            var _c = parseInt(produce[j].commentarios) + parseInt(consume[i].commentarios);
                            produce[j].count = _x;
                            produce[j].commentarios = _c;
                        }
                    }
                }
            }
            return produce;
        }

        function tableTwitter(data) {
            let longitud = data.length, hasta, comentslist = "";
            if (longitud < 16) {
                hasta = longitud;
            } else {
                hasta = 15;
            }
            comentslist += `<table id="tablapaginas" class="table table-striped">
                                <thead>
                                    <tr>
                                        <td>Twitter</td>
                                        <th>Tweets</th>
                                        <th>Comentarios</th>
                                    </tr>
                                </thead><tbody>`;

            for (i = 0; i < hasta; i++) {
                j = i + 1;
                comentslist += `<tr>
                                    <td><b>` + j + `. ` + data[i].name + `</b></td>
                                    <td>` + data[i].count + `</td>
                                    <td>` + data[i].commentarios + `</td>
                                </tr>`;
            }
            comentslist += '</tbody></table>';

            $('#pageProcesar').addClass('procesar');
            $("#tablaPage").html(comentslist);
            $('th').click(function () {
                var table = $(this).parents('table').eq(0)
                var rows  = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
                this.asc  = !this.asc
                if (!this.asc) {
                    rows = rows.reverse()
                }
                for (var i = 0; i < rows.length; i++) {
                    table.append(rows[i])
                }
                setIcon($(this), this.asc);
            })
        }

        /*--------------------------------------- MUESTRA SENTIMIENTO EN GENERAL----------------------------------------*/

        function getStatusTopics() {
            loadingPanel();
            $('#introduccionParrafo').remove();
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                datos  = {topics, start, end};
            axios.post('{{ route('classificarionTwitter.commentInformation',$company) }}', datos).then(response => {
                process_Statics(response.data.count, response.data.comment, 0);
            });
            $('.highcharts-container ').remove();
            $('#pieProcesar').removeClass('procesar');
            $('#modaltablaReacciones').remove();
            $('#modalPie').modal();
            setTimeout(()=>{loadingPanel();},2000)
        }

        /*--------------------------------------- NUBE DE PALABRA EN GENERAL -------------------------------------------*/

        function getCloudTwitter() {
            loadingPanel();
            let topics = Base64.decode("<?php echo $_GET['topics_id'] ?>"),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>"),
                datos  = {topics, start, end};
            axios.post('{{ route('classificarionTwitter.cloudInformation',$company) }}', datos).then(response => {
                procesarComentarios(response.data);
            });
            $('#modalCloud').modal();
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas ',
                '?', 'https', ' http', ':', '/', '.', ',', '@', '...'

            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                posts.forEach(function (post, index2) {
                    var texto=post.content;
                    palabras.forEach(function (palabra, index3) {
                        i=0;
                        for(;i!=-1;){
                            i=texto.indexOf(palabra);
                            texto=texto.replace(palabra, " ");

                        }
                    });
                    comentarios+=" "+texto;

                });
            });
            if(comentarios.length > 0){
                wordCloudComment(comentarios)
            }
            else{
                loadingPanel();
                $('#cloud').append('No cuenta con comentarios');
            }
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
            loadingPanel();
            $('#reporteProcesando').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }

    </script>
@endsection
