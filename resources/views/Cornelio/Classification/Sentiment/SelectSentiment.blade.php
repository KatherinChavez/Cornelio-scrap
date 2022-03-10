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

                            @foreach($posts as $post)
                                <div class="col-md-4" >
                                    <div class="card mb-4" style=" height: 600px;overflow: auto">
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
                                            <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($post->created_time)->format('Y-m-d h:i:s')}}</p>
                                            <p class="card-text small">
                                            @if($post->content)
                                                {{$post->content}}
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
                                                <div class="row justify-content-center pt-2">{{\Carbon\Carbon::parse($comment->created_time)->format('Y-m-d h:i:s')}}</p>
                                                    <label style="margin-left: 10px;">
                                                        <input id="checkbox-{{$comment->comment_id}}" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
                                                    </label>
                                                    <button style="margin-left: 10px;" id="{{$comment->comment_id}}"  data-comment="" data-comm-id="{{$comment->comment_id}}" onclick="generateSentiment(this)" class="btn btn-xs btn-info">Clasificar</button>
                                                </div>
                                                <div id="sen-{{$comment->comment_id}}"></div>
                                                <div id="persen-{{$comment->comment_id}}" style="margin-top: 5px"></div>
                                                <hr class="my-0"></br>
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
@endsection
@section('script')
    <script>
        var postLimit = 10,
            commentLimit = 10,
            commentLimitSecondTimes = 2000000,
            user="{{ Auth::user()->id }}",
            pageAccessToken="",
            loadMore='';

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
                isLogedIn();
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            FB.api(
                '/oauth/access_token',
                'GET',
                {"client_id":"{{env('APP_FB_ID_2')}}","client_secret":"{{env('APP_FB_SECRET_2')}}","grant_type":"client_credentials"},
                function (response) {
                    if (response.access_token) {
                            pageAccessToken = response.access_token;
                            // showScrap();
                    } else {
                        FB.api(
                            '/me',
                            'GET',
                            {"fields": "accounts"},
                            function (response) {
                                pageAccessToken = response.access_token;
                            }
                        );

                    }
                }
            );
        }

        function showScrap(){
            let datos = {"page_id":{{ $_GET['page_id'] }}};
            axios.post('{{ route('post.selectPostDB') }}', datos).then(response => {
                generatePostLists(response.data);
                showHidePagination(response.data);
                setTimeout(getSentiment(response.data),90000);
            });
        }


        function generatePostLists(data) {
            let posts,message,
                postList='';
            if(data.posts){
                posts=data.posts;
            }else{
                posts=data.data;
            }

            posts.forEach(function (post, index) {
                postList+=`<div class="col-md-6">
                                <div class="card mb-3">`;

                    if(post.picture != null){
                        postList += `<img class="card-img-top img-fluid w-100" src="`+post.picture+`" alt="">`;
                    }
                    if(post.url != null){
                        postList += `<img class="card-img-top img-fluid w-100" src="`+post.url+`" alt="">`;
                    }
                    else if(post.video != null){
                        postList += `<img class="card-img-top img-fluid w-100" src="`+post.video+`" alt="">`;
                    }
                if(post.content){
                    content=post.content;
                }else{
                    content=post.story;
                }
                var created_time = moment(post.created_time),
                    formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                    postList+='<div class="card-body">'+
                                '<p class="card-title mb-1 small">'+formated_created_time.substring(0, 10) + ' ' + formated_created_time.substring(11, 16)+'</p>'+
                                '<p class="card-text small">'+content+'';
                if(post.attachments) {
                    if (attachements.type==='share') {
                        postList += '<br><a href="' + attachements.url + '" target="_blank">' + attachements.title + '</a>';
                    }
                }
                postList+=`</p></div><hr class="my-0">
                                <div class="card-body py-2 small" id="reactions-`+post.post_id+`">Reacciones</div><hr class="my-0">
                                <div class="card-body" id="comments-`+post.post_id+`"></div>
                            </div>
                        </div>`;
                getReactions(post.post_id);
                getComments(post.post_id);
                setTimeout(getSentiment(data),10000);
            });
            postList+='';
            $('#posts').append(postList);

        }

        function getReactions(post_id) {
            FB.api(
                '/'+post_id,
                'GET',
                {"fields": "reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares","access_token":pageAccessToken},
                function(response) {
                    var div = post_id,
                        like = response.like.summary.total_count,
                        love = response.love.summary.total_count,
                        wow = response.wow.summary.total_count,
                        haha = response.haha.summary.total_count,
                        sad = response.sad.summary.total_count,
                        angry = response.angry.summary.total_count,
                        shares='',
                        reactionsList='';
                    if (response.shares) {
                         shares = response.shares.count;
                    } else {
                         shares = 0;
                    }
                    reactionsList+=
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Like" src="{{asset("/reacciones/like.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">'+
                        '<label id="" for="Like">'+like+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Love" src="{{asset("/reacciones/love.png")}}" alt="Love" title="Love" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Love">'+love+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Hahaha" src="{{asset("/reacciones/hahaha.png")}}" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Hahaha">'+haha+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Wow" src="{{asset("/reacciones/wow.png")}}" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Wow">'+wow+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Sad" src="{{asset("/reacciones/sad.png")}}" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Sad">'+sad+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Angry" src="{{asset("/reacciones/angry.png")}}" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Angry">'+angry+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Shared" src="{{asset("/reacciones/shared.png")}}" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">'+
                        '<label id="" for="Shared">'+shares+'</label>'+
                        '</div>';
                    $("#reactions-"+div).html(reactionsList);
                }
            );
        }

        function getComments(post) {
            let datos = {"post_id":post}
            axios.post('{{ route('comment.selectDB') }}', datos).then(response => {
                generateComments(response, post);
            });
        }

        function generateComments(response, post_id) {
            var comments ,
                name_from='',
                id_from='',
                commentsList=''
            ;
            if( response.comments){

                comments = response.comments;
            }else if(response.data ){
                comments=response.data.comments;
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
                                            <div style="background-color: #deeee6;padding: 2px" class="rounded">
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


//                    if(comment.comments) {
//                        var respuestas = comment.comments;
//                        (respuestas.data).forEach(function (respuesta, index) {
//                            if(respuesta.from){
//                                name_from=respuesta.from.name;
//                                id_from=respuesta.from.id;
//                            }else{
//                                id_from=respuesta.id;
//                                name_from="Ver FB"
//                            }
//                            var created_time = moment(respuesta.created_time),
//                                formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
//                                commentsList+=`<div class="received_withd_msg">
//                                                <p class="card-text small"><a href="https://business.facebook.com/ ` + id_from + `" target="_blank"><strong>`+name_from+`: </strong>`+respuesta.message+`</a>
//                                                </p>
//                                                </div>
//                                                <p class="small text-muted">`+formated_created_time.substring(0, 10) + ` ` + formated_created_time.substring(11, 16)+`</p>
//                                                <span id="`+ respuesta.id +`"  data-comment="" data-comm-id="` + respuesta.id + `" onclick="generateSentiment(this)" class="btn btn-xs btn-success">Clasificar</span>
//                                                <label style="margin-left: 10px;">
//                                                    <input id="checkbox-`+ respuesta.id +`" type="checkbox" name="verificado" onclick="comprobarCheck(this.id)"> Verificado
//                                                </label>
//                                                <div id="sen-`+respuesta.id+`"></div>
//                                                <div id="persen-`+respuesta.id+`" style="margin-top: 5px"></div>
//                                                </br></br><hr class="my-0"></br>`;
//                        })
//                    }
                });

                showHidePaginationComments(response,post_id);
            }
            $('#comments-' + post_id).append(commentsList);
        }

        function generateSentiment(commentario) {
            var comment=commentario.id;
            getSentimentPer(comment);
            var sent=commentario.getAttribute('data-comment');
            var sentimientos='<div class="sentimientos">';
            sentimientos +=   '<img class="img" id="pos-'+ comment +'" onclick="actualizar(this.id)" name="Positive" src="{{asset("/reacciones/positive.png")}}" alt="Positivo" title="Positivo" class="sentimiento badge badge-count" style=" width:40px; vertical-align: middle">';
            sentimientos +=   '<img class="img"  id="neu-'+ comment +'" onclick="actualizar(this.id)" name="Neutral" src="{{asset("/reacciones/neutral.png")}}" alt="Neutral" title="Neutral" class="sentimiento" style=" width:40px; vertical-align: middle">';
            sentimientos +=   '<img class="img" id="neg-'+ comment +'" onclick="actualizar(this.id)" name="Negativo" src="{{asset("/reacciones/negative.png")}}" alt="Negativo" title="Negativo" class="sentimiento" style=" width:40px; vertical-align: middle">';
            sentimientos+='</div>';

            $(".sentimientos").remove();
            $("#sen-"+comment).html(sentimientos);
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

        function getSentimentPer(comment){
            $('.personalizado').remove();
            let datos = {};
                page_id="<?php echo $_GET['page_id'] ?>";

                datos = {page_id,user};

                axios.post('{{ route('ClassifyFeeling.personalizedFeeling',$company) }}', datos).then(response => {
                    generateSentimentPer(response.data,comment);
                });
        }

        function generateSentimentPer(data,comment) {
            var SentimentPerList="";
            var elemento = document.getElementById(comment);
            var sent=elemento.getAttribute("data-comment");
            data.forEach( function(sentiment, index) {
                if(sent==sentiment.sentiment){
                    SentimentPerList+=`<div id="perso-`+comment+`" data-content="`+sentiment.sentiment+`" class="personalizado selPerso badge badge-info" onclick="actualizarPersonalizado(this)" >
                    <span class="badge badge-count">`+sentiment.sentiment+`</span>
                    </div>`;
                }
                if(sent!=sentiment.sentiment){
                    SentimentPerList+=`<div id="perso-`+comment+`" data-content="`+sentiment.sentiment+`" class="personalizado badge badge-info" onclick="actualizarPersonalizado(this)">
                    <spam>`+sentiment.sentiment+`</spam>
                    </div>`;
                }
            });

            $("#persen-"+comment).html(SentimentPerList);
        }

        function actualizarPersonalizado(sent) {
            let datos = {};
                sen=sent.getAttribute('data-content');
                sentimiento=sent.id;
                sentimiento=sentimiento.replace('perso-','');
                elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", sen);
                comment_id=sentimiento.replace('perso-','');
            $('#'+sentimiento).removeClass('positivo');
            $('#'+sentimiento).removeClass('negativo');
            $('#'+sentimiento).removeClass('neutral');
            $('#'+sentimiento).addClass('perso');

            $('.img').removeClass('sel');
            document.getElementById(sentimiento).click();
            datos = {sentimiento,sen,user};

            axios.post('{{ route('ClassifyFeeling.updateSentiment',$company) }}', datos).then(response => {
                swal('Exito', 'Se ha actualizados', 'success');
            });
        }

        function getSentiment() {
            let datos = {"page_id":{{ $_GET['page_id'] }}};
            axios.post('{{ route('ClassifyFeeling.Sentiment') }}', datos).then(response => {
                resultado = response.data;
                resultado.forEach(function (comment, index) {
                    if(comment.sentiment != null){
                        selectSentiment(comment.sentiment);
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

        function actualizar(id) {
            let dato = {};
                sentimiento=id;
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                sen="";

            if((sentimiento.indexOf("pos") > -1)==true){
               sentimiento=sentimiento.replace('pos-','');
                sen="Positivo";
                $('#'+id).addClass('sel');
                $('#neg-'+sentimiento).removeClass('sel');
                $('#neu-'+sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Positivo");
                $('#'+sentimiento).addClass('positivo');
                $('#'+sentimiento).removeClass('neutral');
                $('#'+sentimiento).removeClass('negativo');
                $('#'+sentimiento).removeClass('perso');

            }
            if((sentimiento.indexOf("neg") > -1)==true){
                sentimiento= sentimiento.replace('neg-','');
                sen="Negativo";
                $('#'+id).addClass('sel');
                $('#pos-'+sentimiento).removeClass('sel');
                $('#neu-'+sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Negativo");
                $('#'+sentimiento).addClass('negativo');
                $('#'+sentimiento).removeClass('positivo');
                $('#'+sentimiento).removeClass('neutral');
                $('#'+sentimiento).removeClass('perso');

            }
            if((sentimiento.indexOf("neu") > -1)==true){
                sentimiento= sentimiento.replace('neu-','');
                sen="Neutral";
                $('#'+id).addClass('sel');
                $('#neg-'+sentimiento).removeClass('sel');
                $('#pos-'+sentimiento).removeClass('sel');
                var elemento = document.getElementById(sentimiento);
                elemento.setAttribute("data-comment", "Neutral");
                $('#'+sentimiento).addClass('neutral');
                $('#'+sentimiento).removeClass('positivo');
                $('#'+sentimiento).removeClass('negativo');
                $('#'+sentimiento).removeClass('perso');

            }
            $('.selPerso').removeClass('selPerso');
            datos = {sentimiento, sen, user};
            axios.post('{{ route('ClassifyFeeling.updateSentiment',$company) }}', datos).then(response => {
//                swal('Exito', 'Se ha actualizados', 'success');
            });
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
//                swal('Exito', 'Se ha actualizado la verficación', 'success');
            });
        }
        function showHidePaginationComments(response,post_id) {
            var commentsList='';
            $('#next-'+post_id).remove();
            if(response.comments){
                var next=response.comments.paging.next;
                    commentsList+='<div class="row justify-content-center" >' +
                    '<button class="btn btn-success btn-sm next-data" id="next-'+post_id+'" data-next="'+next+'" data-post="'+post_id+'" onclick="nextComments(this)">load more</button>' +
                    '</div>';
            }else {
            }
            $('#comments-'+post_id).after(commentsList)
        }

        function showHidePagination(response) {
            if( response.paging ) {
                loadMore=response.paging.next;
                $('#load-more').show();
            } else {
                $('#load-more').hide();
            }
        }

        function getNext() {
            FB.api(
                loadMore,
                function(response) {
                    generatePostLists(response);
                    showHidePagination(response);
                }
            );
        }

        function nextComments(btn) {
            var next=$(btn).data('next'),
                post=$(btn).data('post');
            FB.api(
                next,
                function(response) {
                    generateComments(response, post);
                }
            );
        }
    </script>

@endsection
