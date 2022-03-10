@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                    <i class="fa fa-flask"></i>
                    <strong style="color:#0f3760;"> Detalle</strong>
                    </h4>
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
                    <h3 style="text-align: center; color:#0f3760;">Tema: {{$sub['name']}}</h3>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">

                                    <!--------------------------------------- Información ----------------------------------------------------->
                                    <div class="col-sm-12">
                                        <p class="card-text small"><strong style="color:#0f3760;">Fanpage: </strong>{{$info['page_name']}}</p>
                                        <p class="card-text small"><strong style="color:#0f3760;">Personas que interactuarón los últimos 7 días: </strong>{{$info['talking']}}</p>
                                        
                                        @if($post['url']!=null)
                                            <p class="card-text small"><strong style="color:#0f3760;">Titular: </strong>{{$post['title']}}</p>
                                        @endif
                                            <p class="card-text small"><strong style="color:#0f3760;">Publicación: </strong>{{$post['content']}}</p>
                                            <p class="card-text small"><strong style="color:#0f3760;">Fecha: </strong>{{$post['created_time']}}</p>
                                    </div>
                                    
                                    <!--------------------------------------- Reacción y comentario ----------------------------------------------------->
                                    <div class="col-sm-12">                                        
                                        <div class="mr-1 d-inline-block">
                                            <span id="reactions-{{$post['post_id']}}"></span>
                                        </div>
                                        
                                        <div class="mr-1 d-inline-block">
                                            <img src="{{ asset('reacciones/comment.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                            <label id="" for="comentarios">{{$comentarios}}</label>
                                        </div>
                                    </div>
                                    
                                    <!--------------------------------------- Nube de palabra ----------------------------------------------------->
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="col-sm-12" id="nube">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="card mb-3">
                                <input type="hidden" id="pagina" value="{{$post['page_name']}}">
                                    
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
                                        <a class="btn btn-info btn-small" title="Ver" href="https://fb.com/{{$post['post_id']}}" target="_blank">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                        
                                    <div class="mr-3 d-inline-block">
                                        <button class="btn btn-success btn-small" title="Comentarios" onclick="getRandom()">
                                            <i class="fa fa-comments"></i>
                                        </button>
                                    </div>
                                </div>
                                    
                                <hr class="my-0">
                                <div class="card-footer small text-muted">{{$post['created_time']}}</div>
                                <div class="card-body" id="comments-{{$post['post_id']}}"></div>
                                
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-text small"><strong style="color:#0f3760;">{{$info['page_name']}}</strong> cuenta con un total global de <strong style="color:#0f3760;">{{$info['fan_count']}}</strong> Fans.</p>
                                    <p class="card-text small">Sobre la publicación realizada el <strong style="color:#0f3760;">{{$post['created_time']}}</strong> con el post: <strong style="color:#0f3760;">{{$post['content']}}</strong>,
                                            es importante señalar que según los registros de Facebook este fanpage posee <strong style="color:#0f3760;">{{$info['talking']}}</strong>
                                            fans participando en conversaciones generadas durante los últimos 7 días, siendo las <strong style="color:#0f3760;">{{$interacciones}}</strong>
                                            interacciones un <strong style="color:#0f3760;">{{ round(($interacciones/$info['talking'])*100, 2)}}</strong>% de la conversación del Fanpage en este momento.
                                    </p>
                                    <p class="card-text small">Como dato adicional actualmente la publicación registra <strong style="color:#0f3760;">{{$comentarios}}</strong> comentarios<p/>
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
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script>
        
        $(document).ready(getDatos('{{$post['post_id']}}'));


        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn(response);
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn(response) {
            getReactions('{{$post['post_id']}}');
        }


        function getReactions(post) {
            var post_id= post;
            FB.api(
                '/'+post_id,
                'GET',
                {"fields":"reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares"},
                function(response) {
                    var div = post,
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
                        '<img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px; vertical-align: middle">'+
                        '<label id="" for="Like">'+like+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png" alt="Love" title="Love" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Love">'+love+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Hahaha">'+haha+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Wow">'+wow+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Sad">'+sad+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">'+
                        '<label id="" for="Angry">'+angry+'</label>'+
                        '</div>'+
                        '<div class="mr-2 d-inline-block">'+
                        '<img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">'+
                        '<label id="" for="Shared">'+shares+'</label>'+
                        '</div>';
                    $("#reactions-"+div).html(reactionsList);
                    console.log(reactionsList);
                }
            );
        }

        function getDatos(id){
            datos={post_id:id};
            showProcessing();
            axios.post('{{ route('Review.CloudPost',$company) }}', datos).then(response => {
                if(response.data.length>0){
                    hideProcessing();
                    procesarComentarios(response.data,id);
                }else{
                    $('#nube').html('<h3>No hay suficientes datos!</h3>');
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


            Highcharts.chart('nube', {
                series: [{
                    type: 'wordcloud',
                    data: datos,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }
            });
            hideProcessing();

        }


        function getRandom() {
            var start=document.getElementById('start').value,
                end=document.getElementById('end').value,
                datos;
            if(start!=='' && end!==''){
                datos={post_id:'{{$post['post_id']}}',start:start,end:end};
            }else{
                datos={post_id:'{{$post['post_id']}}'};
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
                    console.log(comentario.comment);
                    commList+='<div class="received_withd_msg"><p class="card-text small ">'+comentario.comment+'</p>' +
                        '</div><p class="small text-muted">'+comentario.created_time+'</p>';
                });
                $('#comments-'+post_id).html(commList);
            }
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





</body>
</html>