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
                        @php
                        //dd($subcategoria);
                        @endphp

                        <h4>Clasificación de los temas de {{$subcategoria->name}}
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Se cuenta con la opción de clasificar los comentarios y ver información de más">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            {{--<a onclick="infoV()" class="mx-2">--}}
                                {{--<i class="fas fa-info-circle"></i>--}}
                            {{--</a>--}}
                        </h4>

                        <p id="info" hidden> Clasificar las categorias como positivas o negativas</p>
                    </div>
                    <div class="card-body">
                        <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opción
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" onclick="reporte()">Reporte</a>
                                    <a class="dropdown-item" onclick="getDataPostComments()" data-toggle="modal"
                                       data-target="">Páginas</a>
                                    <a class="dropdown-item" onclick="getStatsSubcategoria()" data-toggle="modal"
                                       data-target="" data-target="#pieCloud">Estatus</a>
                                    <a class="dropdown-item" onclick="modalnuve()" data-toggle="modal" data-target=""
                                       data-target="#modalCloud">Palabras</a>
                                </div>
                            </div>
                        </div>

                        </br></br>
                        <div class="row justify-content-center" >

                            @foreach($posts as $post)

                                <div class="col-md-4">
                                    <div class="post-wrapper">
                                        <p class="post-meta">
                                            <button type="button" id="actualizar-{{$post->post_id}}" class="badge badge-primary" onclick="Actualizacion(this.id);" >Actualizar</button>
                                            <button type="button" id="sta-{{$post->post_id}}" class="badge badge-warning " onclick="getStats(this.id);" data-toggle="modal" data-target="pie">Estatus</button>
                                            <button type="button" id="cloud-{{$post->post_id}}" class="badge badge-info" onclick="getCloud(this.id);" data-toggle="modal" data-target="pie">Palabras</button>
                                            <button type="button" id="des-{{$post->post_id}}" class="badge badge-danger" onclick="desclacificar(this);" title="Eliminar">Desclasificar</button>
                                        </p>
                                    </div>
                                    {{--style=" height: 250px;overflow: auto"--}}
                                    <div class="card mb-4" style=" height: 550px;overflow: auto">
                                        @if(isset($post->attachment))
                                            @if($post->attachment->picture != null)
                                                <img class="card-img-top img-fluid w-100" src="{{$post->attachment->picture}}" alt="">
                                            @elseif($post->attachment->url != null)
                                                <img class="card-img-top img-fluid w-100" src="{{$post->attachment->url}}" alt="">
                                            @elseif($post->attachment->videos != null)
                                                <img class="card-img-top img-fluid w-100" src="{{$post->attachment->video}}" alt="">
                                            @endif
                                        @endif

                                        {{--<div class="card-body">--}}
                                        {{--<p class="card-title mb-1 small">{{\Carbon\Carbon::parse($post->created_time)->format('Y-m-d h:i:s')}}</p>--}}
                                            @if( isset($post->post->created_time))
                                                <p class="card-title mb-1 small">{{$post->post->created_time}}</p>
                                            @endif
                                        {{--<p class="card-title mb-1 small">{{$post->post->created_time}}</p>--}}
                                        <p class="card-text small">
                                            @if(isset($post->post->content))
                                                {{$post->post->content}}
                                            @endif
                                            @if(isset($post->attachment))
                                                <br><a href="{{$post->attachment->url}}" target="_blank">{{$post->attachment->tittle}}</a>
                                            @endif
                                        </p>
                                        {{--</div>--}}
                                        <hr class="my-0">
                                        <div class="" id="reactions-{{$post->post_id}}">

                                            @if($post->reactions)

                                                <div class="mr-2 d-inline-block">
                                                    <img name="Like" src="{{asset("/reacciones/like.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                    <label id="" for="Like">{{$post->reactions['likes']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Love" src="{{asset("/reacciones/love.png")}}" alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Love">{{$post->reactions['love']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Hahaha" src="{{asset("/reacciones/hahaha.png")}}" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Hahaha">{{$post->reactions['haha']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Wow" src="{{asset("/reacciones/wow.png")}}" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Wow">{{$post->reactions['wow']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Sad" src="{{asset("/reacciones/sad.png")}}" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Sad">{{$post->reactions['sad']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Angry" src="{{asset("/reacciones/angry.png")}}" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Angry">{{$post->reactions['angry']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Shared" src="{{asset("/reacciones/shared.png")}}" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="Shared">{{$post->reactions['shared']}}</label>
                                                </div>

                                            @else
                                                {{--<button class="btn btn-sm btn-primary" onclick="getReactions('{{$post->post_id}}')"> obtener</button>--}}
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Like" src="{{asset("/reacciones/like.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                    <label id="" for="Like">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Love" src="{{asset("/reacciones/love.png")}}" alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Love">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Hahaha" src="{{asset("/reacciones/hahaha.png")}}" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Hahaha">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Wow" src="{{asset("/reacciones/wow.png")}}" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Wow">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Sad" src="{{asset("/reacciones/sad.png")}}" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Sad">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Angry" src="{{asset("/reacciones/angry.png")}}" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Angry">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block">
                                                    <img name="Shared" src="{{asset("/reacciones/shared.png")}}" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="Shared">0</label>
                                                </div>
                                            @endif()
                                        </div>
                                        <hr class="my-0">
                                        {{--<div class="card-body" style=" height: 250px;overflow: auto" id="comments-{{$post->post_id}}" >--}}
                                        <div class="card-body" style=" " id="comments-{{$post->post_id}}" >
                                            @foreach($post->comments as $comment)
                                                <div class="received_withd_msg">
                                                    <div style="padding: 2px" class="rounded">
                                                        <p class="card-text small"><a href="https://business.facebook.com/{{$comment->comment_id}}" target="_blank"><strong>{{ $comment->commented_from="Sin"? "Usuario FB":$comment->commented_from }}</strong></a>
                                                            <br>
                                                            {{ $comment->comment }}
                                                        </p>
                                                    </div>
                                                </div>
                                                {{--<div class="row justify-content-center pt-2">{{\Carbon\Carbon::parse($comment->created_time)->format('Y-m-d h:i:s')}}</p>--}}
                                                <div class="row justify-content-center pt-2">{{$comment->created_time}}</p>
                                                    <label style="margin-left: 10px;">
                                                        <input id="checkbox-{{$comment->comment_id}}" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
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
                            @endforeach
                        </div>

                        <div class="row justify-content-center">
                            {{$posts->appends(request()->except('page'))->links()}}
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
                    </br></br>
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
        var postLimit = 10,
            commentLimit = 1,
            commentLimitSecondTimes = 200,
            pageAccessToken = "",
            loadMore = '',
            name = '',
            comentarios;
        $(document).ready(getSentiment());

        $(document).ready(
            axios.post('{{route('ClassifyFeeling.check')}}').then(response => {
                let result = response.data;
                result.forEach(status => {
                    let id = status.comment_id,
                        estado = status.estado;
                    let elemento = document.getElementById('checkbox-' + id);
                    if(elemento){
                        if (estado != 0) {
                            document.getElementById('checkbox-' + id).checked = true;
                        }
                    }

                });
            })
        );

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                isLogedIn(response);
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn(token) {
            FB.api(
                '/oauth/access_token',
                'GET',
                {"client_id":"{{env('APP_FB_ID_2')}}","client_secret":"{{env('APP_FB_SECRET_2')}}","grant_type":"client_credentials"},
                function (response) {
                    if (response.access_token) {
                        pageAccessToken = response.access_token;
                    } else {
                        FB.api(
                            '/me',
                            'GET',
                            {"fields": "accounts"},
                            function (response) {
                                pageAccessToken = response.access_token;
                                //count();
                            }
                        );

                    }
                }
            );
        }



        /*----------------------------------- CONSULTA DE LA BD ----------------------------------------------------------------*/
        //Scrap de los comentarios
        function getComments(post) {
            let datos = {"post_id":post}
            axios.post('{{ route('comment.selectDB') }}', datos).then(response => {
                generateComments(response.data, post);
            });
        }


        //Muestra los comentarios
        function generateComments(response, post_id) {
            var comments ,
                name_from='',
                id_from='',
                commentsList=''

            if(response.comments.data ){
                comments=response.comments.data;
            }else if(response.comments ){
                comments = response.comments;
            }
            if( comments ) {

                commentsList='';
                comments.forEach(function (comment, index) {
                    if(comment.commented_from){
                        name_from=comment.commented_from;
                        id_from=comment.comment_id;
                        if(comment.author_id != comment.page_id){
                            name_from="Usuario FB";
                        }
                    }
                    var created_time = moment(comment.created_time),
                        formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');


                    commentsList+=`<div class="received_withd_msg">
                                            <div style="background-color: ;padding: 2px" class="rounded">
                                            <p class="card-text small"><a href="https://business.facebook.com/` + id_from + `" target="_blank"><strong>`+name_from+`: </strong></a>
                                            <br>`+comment.comment+`
                                            </p>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center pt-2">
                                            <p class="small text-muted">`+formated_created_time.substring(0, 10) + ` ` + formated_created_time.substring(11, 16)+`</p>
                                            <label style="margin-left: 10px;">
                                                <input id="checkbox-`+ id_from +`" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
                                            </label>
                                            <button style="margin-left: 10px;" id="`+ comment.comment_id +`"  data-comment="" data-comm-id="` + comment.comment_id + `" onclick="generateSentiment(this)" class="btn btn-xs btn-info">Clasificar</button>
                                        </div>
                                        <div id="sen-`+id_from+`"></div>
                                        <div id="persen-`+id_from+`" style="margin-top: 5px"></div>
                                        <hr class="my-0"></br>`;


                });

                //showHidePaginationComments(response,post_id);
            }
            $('#comments-' + post_id).append(commentsList);
        }
        /*---------------------------------------------------------------------------------------------------*/

        //Clasificar los sentimiento de los cometarios
        function generateSentiment(commentario) {
            var comment = commentario.id;
            getSentimentPer(comment);
            var sent = commentario.getAttribute('data-comment');
            var sentimientos = '<div class="sentimientos">';
            sentimientos += '<img class="img" id="pos-' + comment + '" onclick="actualizar(this.id)" name="Positive" src="https://monitoreo.cornel.io/reacciones/positive.png" alt="Positivo" title="Positivo" class="sentimiento" style=" width:40px; vertical-align: middle">'
            sentimientos += '<img class="img"  id="neu-' + comment + '" onclick="actualizar(this.id)" name="Neutral" src="https://monitoreo.cornel.io/reacciones/neutral.png" alt="Neutral" title="Neutral" class="sentimiento" style=" width:40px; vertical-align: middle">'
            sentimientos += '<img class="img" id="neg-' + comment + '" onclick="actualizar(this.id)" name="Negativo" src="https://monitoreo.cornel.io/reacciones/negative.png" alt="Negativo" title="Negativo" class="sentimiento" style=" width:40px; vertical-align: middle">'
            sentimientos += '</div>';

            $(".sentimientos").remove();
            $("#sen-" + comment).html(sentimientos);
            if (sent == 'Neutral') {
                $('#neu-' + comment).addClass('sel');
            }
            if (sent == 'Positivo') {
                $('#pos-' + comment).addClass('sel');
            }
            if (sent == 'Negativo') {
                $('#neg-' + comment).addClass('sel');
            }
        }

        function getSentimentPer(comment) {
            $('.personalizado').remove();
            let datos = {};
            var page_id = "<?php echo $_GET['categoria'] ?>";
            user = document.getElementById("user").value;
            datos = {page_id, user};

            axios.post('{{ route('SentimentSub.personalizedFeelingSub',$company) }}', datos).then(response => {
                generateSentimentPer(response.data, comment);
            });
        }

        function generateSentimentPer(data, comment) {
            var SentimentPerList = "";
            var elemento = document.getElementById(comment);
            var sent = elemento.getAttribute("data-comment");
            data.forEach(function (sentiment, index) {
                if (sent == sentiment) {
                    SentimentPerList += `<div id="perso-` + comment + `" data-content="` + sentiment.sentiment + `" class="personalizado selPerso" onclick="actualizarPersonalizado(this)">
                    <spam>` + sentiment.sentiment + `</spam>
                    </div>`;
                }
                if (sent != sentiment) {
                    SentimentPerList += `<div id="perso-` + comment + `" data-content="` + sentiment.sentiment + `" class="personalizado" onclick="actualizarPersonalizado(this)">
                    <spam>` + sentiment.sentiment + `</spam>
                    </div>`;
                }
            });
            $("#persen-" + comment).html(SentimentPerList);

        }

        function actualizarPersonalizado(sent) {
            let datos = {};
            sen = sent.getAttribute('data-content');
            sentimiento = sent.id;
            sentimiento = sentimiento.replace('perso-', '');
            elemento = document.getElementById(sentimiento);
            elemento.setAttribute("data-comment", sen);
            user = document.getElementById('user').value;

            $('#' + sentimiento).removeClass('positivo');
            $('#' + sentimiento).removeClass('negativo');
            $('#' + sentimiento).removeClass('neutral');
            $('#' + sentimiento).addClass('perso');

            $('.img').removeClass('sel');
            document.getElementById(sentimiento).click();

            datos = {sentimiento, sen, user};
            axios.post('{{ route('ClassifyFeeling.updateSentiment',$company) }}', datos).then(response => {

                //swal('Exito', 'Se ha actualizado', 'success');
            });
        }

        function actualizar(id) {
            var sentimiento = id;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var sen = "";

            if ((sentimiento.indexOf("pos") > -1) == true) {
                sentimiento = sentimiento.replace('pos-', '');
                sen = "Positivo";
                $('#' + id).addClass('sel');
                $('#neg-' + sentimiento).removeClass('sel');
                $('#neu-' + sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Positivo");
                $('#' + sentimiento).addClass('positivo');
                $('#' + sentimiento).removeClass('neutral');
                $('#' + sentimiento).removeClass('negativo');
                $('#' + sentimiento).removeClass('perso');

            }
            if ((sentimiento.indexOf("neg") > -1) == true) {
                sentimiento = sentimiento.replace('neg-', '');
                sen = "Negativo";
                $('#' + id).addClass('sel');
                $('#pos-' + sentimiento).removeClass('sel');
                $('#neu-' + sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Negativo");
                $('#' + sentimiento).addClass('negativo');
                $('#' + sentimiento).removeClass('positivo');
                $('#' + sentimiento).removeClass('neutral');
                $('#' + sentimiento).removeClass('perso');

            }
            if ((sentimiento.indexOf("neu") > -1) == true) {
                sentimiento = sentimiento.replace('neu-', '');
                sen = "Neutral";
                $('#' + id).addClass('sel');
                $('#neg-' + sentimiento).removeClass('sel');
                $('#pos-' + sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Neutral");
                $('#' + sentimiento).addClass('neutral');
                $('#' + sentimiento).removeClass('positivo');
                $('#' + sentimiento).removeClass('negativo');
                $('#' + sentimiento).removeClass('perso');
            }
            $('.selPerso').removeClass('selPerso');

            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyFeeling.updateSentiment', $company); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&sentimiento=" + sentimiento +
                "&sen=" + sen +
                "&user=" + user,
                success: function (data) {
                    swal('Exito', 'Se ha actualizado', 'success');
                }
            })

        }

        //Muestra cuales han sido clasificado
        function getSentiment() {
            let datos = {"tema":{{ $_GET['categoria'] }}};
            axios.post('{{ route('ClassifyFeeling.Sentiment') }}', datos).then(response => {
                resultado = response.data;
                resultado.forEach(function (comment, index) {
                    if(comment.sentiment != null){
                        selectSentiment(comment.sentiment);
                    }
                });
            });
        }


        //Opciones que se encuentran arriba de cada post

        //Actualizar reacción
        function Actualizacion(postId, name) {
            var post = postId.replace('actualizar-', '');
            ActualizarReacciones(post, name);
            ActualizarComments(post, name);
            $("#comments-" + post).html(" ");

        }

        function ActualizarReacciones(post, name) {
            //var post = postId.replace('actualizar-', '');
            FB.api(
                '/' + post,
                'GET',
                {"fields": "reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares","access_token":pageAccessToken},

                function (response) {
                    var div = post,
                        like = response.like.summary.total_count,
                        love = response.love.summary.total_count,
                        wow = response.wow.summary.total_count,
                        haha = response.haha.summary.total_count,
                        sad = response.sad.summary.total_count,
                        angry = response.angry.summary.total_count,
                        shares = '',
                        reactionsList = '';

                    if (response.shares) {
                        shares = response.shares.count;
                    } else {
                        shares = 0;
                    }

                    reactionsList +=
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px; vertical-align: middle">' +
                        '<label id="" for="Like">' + like + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png" alt="Love" title="Love" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Love">' + love + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Hahaha">' + haha + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Wow">' + wow + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Sad">' + sad + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Angry">' + angry + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">' +
                        '<label id="" for="Shared">' + shares + '</label>' +
                        '</div>';
                    $("#reactions-" + div).html(reactionsList);
                }
            );

            ScrapDBScrap(post);
        }

        function ScrapDBScrap(post ) {
            let datos = {"post_id":post, "pageAccessToken" : pageAccessToken};
            axios.post('{{ route('scrapsPost.ScrapReaction') }}', datos).then(response => {
                //generateComments(response.data, post);
            });
        }

        function ActualizarComments(post) {
            let datos = {"post_id":post, "token" : pageAccessToken};
            axios.post('{{ route('scrapsPost.scrapsComments') }}', datos).then(response => {
                //generateComments(response.data, post);
                getComments(post);
                $("#comments-" + post).html(" ");
            });
        }


        function procesarSentiment() {
            let datos = {};
            sentimiento = document.getElementById('posteoAuto').value; //post_id
            sen = document.getElementById('tipo').value; //tipo de sentimiento
            user = document.getElementById('user').value;
            datos = {sentimiento, sen, user};
            {{--axios.post('{{ route('ClassifyFeeling.SentimentComment',$company) }}', datos).then(response => {--}}
            axios.post('{{ route('ClassifyFeeling.updateSentiment',$company) }}', datos).then(response => {
                getSentimentPost(sentimiento);
            });
        }

        function getSentimentPost(post_id) {
            let datos = {"user_id":document.getElementById('user').value,
                         "post_id":post_id};
            axios.post('{{ route('ClassifyCategory.SentimentPost',$company) }}', datos).then(response => {
                getSentimentP(response.data);

            });
        }


        function getSentimentP(data) {
            let datos = {};
            resultadoC = data;
            user = document.getElementById("user").value;
            resultadoC.forEach(function (comment, index) {
                selectSentiment(comment);
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

        //Status
        function getStats(post_id) {
            var subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            var postId = post_id.replace('sta-', '');
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var user = document.getElementById('user').value;
            var commnets, sentiments;

            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.countComment', $company); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&post_id=" + postId +
                "&user_id=" + user,
                success: function (data) {
                    commnets = data;
                    $.ajax({
                        method: "POST",
                        url: "<?php echo route('SentimentSub.reactionPost', $company); ?>",
                        data: "_token=" + CSRF_TOKEN +
                        "&post_id=" + postId +
                        "&user=" + user,
                        success: function (data) {
                            sentiments = data;
                            procesarStatics(commnets, sentiments, 1);

                        }
                    });
                }
            });

            $('.highcharts-container ').remove();
            $('#pieProcesar').removeClass('procesar');
            $('#modaltablaReacciones').remove();
            $('#modalPie').modal();
        }

        function procesarStatics(comments, sentiments, tipo) {
            var total = comments,
                reacciones = sentiments,
                positivos = 0,
                negativos = 0,
                neutral = 0,
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

        //Nube de palabra
        function getCloud(post_id) {
            let datos = {};
            postId = post_id.replace('cloud-', '');
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            user = document.getElementById('user').value;
            datos = {postId, user};
            axios.post('{{ route('ClassifyCategory.comment',$company) }}', datos).then(response => {
            //axios.post('{{ route('ClassifyCategory.WordComment') }}', datos).then(response => {
                //procesarComentariosPosts(response.data);
                procesarComentariosPost(response.data);
            });
            $('.highcharts-container').remove();
            $('#cloudProcesar').removeClass('procesar');
            //$('#modalCloud').modal();
            $('#modalCloudComment').modal();
        }

        function procesarComentariosPosts2(data) {
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

            Highcharts.chart('cloud', {
                series: [{
                    type: 'wordcloud',
                    data: datos,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });

            $('#cloudProcesar').addClass('procesar');
        }

        function procesarComentariosPost(data) {
            var comentariosPost="";
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
                ' el ',' por ','Por ',' como ',' cómo ','Y ',' y ',' un ',' una ',' uno ',' mas ', ' más ',
                ' se ',' no ','No ',' si ','Si ', 'A ',' a ',' en ',' es ','está ',' eso ',' esos ',' pero ',
                'Image/Emoji',' para ',' las ',' su ',' sus ',' esa ','!',' ser ',' sin ',' ya ',' los ',' te ',
                ' me ','Me ',' ja ',' jaja ',' je ',' jeje ',' les ',' la ',' le ',' son ','DE ','QUE ','LA ',' con ',
                'Pero ',' este ',' esta ',' hace ',' poco ',' toda ','Toda ','Todo ',' todo ',' bien ','Bien ',' estos ',
                ' estas ','Estos ','Estas ','Está ',' esto ',' solo ',' cada ',' todos ','Todos ',' nada ',' ellos '
            ];
            var i;
            data.forEach( function(post, index) {
                var texto=post.comment;
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
            $('#reporteProcesando').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }

        //Desaclificar
        function desclacificar(desId) {
            let datos = {};
            post_id = desId.id;
            user_id = document.getElementById("user").value
            post_id = post_id.replace('des-', '');
            i = post_id.indexOf("_");
            page_id = post_id.substring(0, i);
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $('#' + post_id + '').remove();
            r = confirm('Desea Desclasificar el Post?')
            if (r == true) {
                $('#row-' + post_id).remove();
                datos = {user_id, post_id};
                axios.post('{{ route('ClassifyCategory.DeclassifyCategory',$company) }}', datos).then(response => {
                    if(response.data == 'eliminado'){
                        swal('Exito !', 'Se ha desclasificado', 'success');
                    }
                    else{
                        swal('Error !', 'La publicación ya se encuentra desclasificado', 'info');
                    }

                });
            }
        }

        //Opciones generales
        //Reporte
        function reporte() {
            let datos = {};
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            user = document.getElementById('user').value;
            subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            $('#reporteProcesando').removeClass('procesar');
            $('#reporteContenido').addClass('procesar');
            datos = {user, subcategoria_id};
            axios.post('{{ route('ClassifyCategory.report',$company) }}', datos).then(response => {
                if(response.data == 'Error'){
                  swal("Sin datos!", "No se ha almacenado los scrap de los ultmimos comentarios", "warning");
                }
                else{
                  reporteContenido(response.data);
                  $('#reporte').modal();
                }
            });

        }

        function reporteContenido(data) {
            var promedioPost = ((data.publicaciones / data.paginas)).toLocaleString();
            var interacciones = (parseFloat(data.reacciones) + parseFloat(data.comentarios));
            var talking = parseFloat(data.talking);
            var proIneteracciones = parseFloat((interacciones / talking) * 100).toFixed(2);
            var contenido = '';
            contenido += '<p>El tema ' + data.sub + ' ha generado ' + (data.publicaciones).toLocaleString() + ' publicaciones en ' + (data.paginas).toLocaleString() + ' fanpages,' +
                ' para un promedio de ' + parseFloat(promedioPost).toFixed(2) + ' publicaciones del tema por cada fanpage.</p>';

            contenido += '<p>Sobresale en cantidad de publicaciones la página ' + data.mayorPost.name + ' que realizó ' + (data.mayorPost.postCount).toLocaleString() + ' ' +
                'publicaciones. <br> En cantidad de comentarios la página ' + data.mayorComment.name + ' registra ' + (data.mayorComment.comments).toLocaleString() + ' comentarios.</p>';

            contenido += '<p>La página que menos comentarios tuvo sobre el tema fue: ' + data.menorComment.name + ' con ' + (data.menorComment.comments).toLocaleString() + ' comentarios.</p>';

            contenido += '<div id="graficos">'
            contenido += '<p>Las palabras que predominaron en la conversación son las siguientes:</p>';
            contenido += '<div id="tablaCloud"></div>';
            contenido += '<p>El sentimiento general del tema ha sido el siguiente:</p>';
            contenido += '<div id="tablaSentimiento"></div>';
            contenido += '</div>';

            $('#reporteContenido').html(contenido);
            //countFansReporte();
            cloudReporte();
            getStatsSubcategoriaReporte();
        }

        function cloudReporte() {
            let datos = {};
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            user = document.getElementById('user').value;
            subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            datos = {subcategoria_id, user};
            axios.post('{{ route('ClassifyCategory.cloudReport',$company) }}', datos).then(response => {
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las '
            ];
            var i;
            data.forEach( function(posts, index) {
                posts.forEach(function (post, index2) {
                    var texto=post.comment;
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
            wordCloudPostReporte(comentarios)
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

        /********/
        function procesarComentariosReporte2(data) {
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

            Highcharts.chart('tablaCloud', {
                series: [{
                    type: 'wordcloud',
                    data: datos,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
            $('#cloud').addClass('procesar');
        }

        function getStatsSubcategoriaReporte() {
            var subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var user = document.getElementById('user').value;
            var commnets, sentiments;
            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.reactionCategoryCount', $company); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&subcategoria_id=" + subcategoria_id +
                "&user_id=" + user,
                success: function (data) {
                    commnets = data;
                    $.ajax({
                        method: "POST",
                        url: "<?php echo route('ClassifyCategory.reactionCategory', $company); ?>",
                        data: "_token=" + CSRF_TOKEN +
                        "&subcategoria_id=" + subcategoria_id +
                        "&user_id=" + user,
                        success: function (data) {
                            sentiments = data;
                            procesarStaticsReporte(commnets, sentiments);

                        }
                    });
                }
            });
        }

        function procesarStaticsReporte(comments, sentiments) {
            var total = comments,
                reacciones = sentiments,
                positivos = 0,
                negativos = 0,
                neutral = 0,
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

        //Paginas
        function getDataPostComments() {
            let datos = {};
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            user = document.getElementById('user').value;
            subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            datos = {user, subcategoria_id};
            axios.post('{{ route('ClassifyCategory.postPage',$company) }}', datos).then(response => {
                procesarPosteosyComentarios(response.data);
            });
            $('#pageProcesar').removeClass('procesar');
            $('#modalPage').modal();
        }

        function procesarPosteosyComentarios(data) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var posteos = [];
            var longitud = data.length;
            var peticiones = 0;
            var array;
            data.forEach(function (post, index) {
                $.ajax({
                    method: "POST",
                    url: "<?php echo route('ClassifyCategory.countComment', $company); ?>",
                    data: "_token=" + CSRF_TOKEN +
                    "&post_id=" + post.post_id,
                    success: function (response) {
                        peticiones = peticiones + 1;
                        var _data = {};
                        _data.page_id = post.page_id;
                        _data.page_name = post.page_name;
                        _data.post_id = post.post_id;
                        _data.commentarios = parseInt(response);
                        posteos.push(_data);
                        if (peticiones == longitud) {
                            array = countpages(posteos);
                            var ordenado = array.sort(function (a, b) {
                                return (b.count - a.count)
                            });
                            tablePage(ordenado);
                        }

                    }
                });
            })
        }

        function countpages(original) {
            var consume = original;
            var longitud = consume.length;
            var temp = [];

            var produce = [];

            for (var i = 0; i < longitud; i++) {
                if (temp.indexOf(consume[i].page_id) == -1) {
                    temp.push(consume[i].page_id);
                    var _data = {};
                    _data.id = consume[i].page_id;
                    _data.name = consume[i].page_name;
                    _data.commentarios = consume[i].commentarios;
                    _data.count = 1;

                    produce.push(_data);
                } else {
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

        function tablePage(data) {
            var longitud = data.length, hasta;
            if (longitud < 16) {
                hasta = longitud;
            } else {
                hasta = 15;
            }
            var comentslist = "";
            comentslist += '<table id="tablapaginas" class="table table-striped">';
            comentslist += '<thead><tr>';
            comentslist += '<td>Page</td>';
            comentslist += ' <th>Post</th>';
            comentslist += ' <th>Comments</th>';
            comentslist += '</thead></tr><tbody>';
            for (i = 0; i < hasta; i++) {
                j = i + 1;
                comentslist += '<tr>';
                comentslist += '<td><a target="_blank" href="https://www.facebook.com/' + data[i].id + '">' + j + '. ' + data[i].name + '</a></td>';
                comentslist += ' <td>' + data[i].count + '</td>';
                comentslist += ' <td>' + data[i].commentarios + '</td>';
                comentslist += '</tr>';
            }
            comentslist += '</tbody></table>';
            $('#pageProcesar').addClass('procesar');
            $("#tablaPage").html(comentslist);
            $('th').click(function () {
                var table = $(this).parents('table').eq(0)
                var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
                this.asc = !this.asc
                if (!this.asc) {
                    rows = rows.reverse()
                }
                for (var i = 0; i < rows.length; i++) {
                    table.append(rows[i])
                }
                setIcon($(this), this.asc);
            })
        }

        //Status general
        function getStatsSubcategoria() {
            $('#introduccionParrafo').remove();
            var subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var user = document.getElementById('user').value;
            var commnets, sentiments;
            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.reactionCategoryCount', $company); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&subcategoria_id=" + subcategoria_id +
                "&user_id=" + user,
                success: function (data) {
                    commnets = data;
                    $.ajax({
                        method: "POST",
                        url: "<?php echo route('ClassifyCategory.reactionCategory', $company); ?>",
                        data: "_token=" + CSRF_TOKEN +
                        "&subcategoria_id=" + subcategoria_id +
                        "&user_id=" + user,
                        success: function (data) {
                            sentiments = data;
                            procesarStatics(commnets, sentiments, 0);

                        }
                    });
                }
            });
            $('.highcharts-container ').remove();
            $('#pieProcesar').removeClass('procesar');
            $('#modaltablaReacciones').remove();
            $('#modalPie').modal();

        }

        //Nube de palabras
        function modalnuve() {
            cloud();
        }

        function cloud() {
            let datos = {};
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            user = document.getElementById('user').value;
            subcategoria_id = "<?php echo $_GET['categoria'] ?>";
            datos = {subcategoria_id, user};
            axios.post('{{ route('ClassifyCategory.cloudReport',$company) }}', datos).then(response => {
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas '
            ];
            var i;
            data.forEach( function(posts, index) {
                posts.forEach(function (post, index2) {
                    var texto=post.comment;
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
            $('#reporteProcesando').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }


        function procesarComentarios_2(data) {
            let texto = data,
                comentarios;
            texto.forEach(function (posts, index) {
                comentarios += " " + posts;
            });
            var lines = comentarios.split(/[,\. ]+/g),
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
            datos=datos.sort(function (a, b){
                return (b.weight - a.weight)
            });

            var filtrado=[];
            var temporal;
            var hasta=datos.length;
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
            alert('temporal', temporal);

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

            /*Highcharts.chart('cloud', {
                series: [{
                    type: 'wordcloud',
                    data: datos,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });*/

            //$('#cloudProcesar').addClass('procesar');
        }



        //Sentimiento
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

        function comprobarCheck(id) {
            var estado="";
                if ($('#'+id).is(':checked') ) {
                    //alert("Seleccionado");
                    estado=1;
                } else {
                    //alert("Checkbox Deseleccionado");
                    estado=0;
                }
            actualizarVerificar(id,estado);
        }

        function actualizarVerificar(id,estado) {
            let datos = {};
                id= id.replace('checkbox-','');
            datos = {id,estado,user};
            axios.post('{{ route('ClassifyFeeling.statusSentiment',$company) }}', datos).then(response => {
                //swal('Exito', 'Se ha actualizado la verficación', 'success');
            });
        }




    </script>
@endsection
