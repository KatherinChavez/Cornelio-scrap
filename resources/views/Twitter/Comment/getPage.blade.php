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
                        <h4>
                            Clasificación de Comentarios
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Se cuenta con la opción de conocer y clasificar los sentimiento de los comentarios">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center" >
                            @foreach($tweets as $tweet)
                                @php
                                //dd($tweet);
                                @endphp
                                <div class="col-md-4" >
                                    <div class="card mb-4" style=" height: 600px;overflow: auto">
                                        <div class="card-body">
                                            <h3 style="color: #0A5A97">{{$tweet->name}}</h3><br>
                                            @if(isset($tweet->attachment))
                                                @if($tweet->attachment->picture != null)
                                                    <img class="card-img-top img-fluid w-100" src="{{$tweet->attachment->picture}}" alt="">
                                                @endif
                                            @endif

                                            <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($tweet->created_time)->format('Y-m-d h:i:s')}}</p>
                                            <p class="card-text small">
                                                @if($tweet->content)
                                                    {{$tweet->content}}
                                                @endif
                                                @if($tweet->expanded_url != '' || $tweet->expanded_url != null )
                                                    <br><a href="{{$tweet->expanded_url}}" target="_blank"> Ver más</a>
                                                @endif
                                            </p>

                                            <hr class="my-0">
                                            <div class="" id="reactions-{{$tweet->id_tweet}}">
                                                @if($tweet->reactions)
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Like" src="{{asset("/reacciones/like_twitter.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                        <label id="" for="Like">{{$tweet->reactions['favorite_count']}}</label>
                                                    </div>
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="retweet" src="{{asset("/reacciones/retweet.png")}}" alt="retweet" title="retweet" style="width: 20px; vertical-align: middle">
                                                        <label id="" for="retweet">{{$tweet->reactions['retweet_count']}}</label>
                                                    </div>
                                                @else
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Like" src="{{asset("/reacciones/like_twitter.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                        <label id="" for="Like">0</label>
                                                    </div>
                                                    <div class="mr-2 d-inline-block">
                                                        <img name="Retweet" src="{{asset("/reacciones/retweet.png")}}" alt="Retweet" title="Retweet" style="width: 20px; vertical-align: middle">
                                                        <label id="" for="Retweet">0</label>
                                                    </div>
                                                @endif()
                                            </div>
                                            <hr class="my-0">
                                            <div class="card-body" style=" " id="comments-{{$tweet->id_tweet}}" >
                                                @foreach($tweet->comments as $comment)
                                                    <div class="received_withd_msg">
                                                        <div style="padding: 2px" class="rounded">
                                                            <p class="card-text small">
                                                                <b><a href="https://twitter.com/{{$comment->username}}/status/{{$comment->comment_id}}" target="_blank"><strong>{{ $comment->name}}</strong></a></b>
                                                                <br>
                                                                {{ $comment->content }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row justify-content-center pt-2"><p>{{\Carbon\Carbon::parse($comment->created_time)->format('Y-m-d h:i:s')}}</p>
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
@endsection
@section('script')
    <script>
        var user="{{ Auth::user()->id }}";

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

        function getSentiment() {
            let datos = {"page_id":"<?php echo $_GET['id'] ?>"};
            axios.post('{{ route('classificarionTwitter.getSentiment') }}', datos).then(response => {
                resultado = (response.data);
                console.log(resultado);
                resultado.forEach(function (comment, index) {
                    console.log(comment);
                    if(comment != null){
                        selectSentiment(comment);
                    }
                });
            });
        }

        function selectSentiment(data) {
            let elemento = document.getElementById(data.comment_id);
            //var elemento = document.getElementById("1471529942022303749");
            console.log(elemento, data);
            if(elemento){
                console.log(data.sentiment);
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
            datos = {comment, sentiment, user};
            axios.post('{{ route('classificarionTwitter.sentiment',$company) }}', datos);
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
            let comment_id= id.replace('checkbox-','');
            datos = {comment_id,estado};
            console.log(datos);
            axios.post('{{ route('classificarionTwitter.status',$company) }}', datos).then(response => {
//                swal('Exito', 'Se ha actualizado la verficación', 'success');
            });
        }



    </script>

@endsection
