<!doctype html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    

    <!-- Bootstrap CSS -->
    <title>Reporte diario</title>
</head>
{{--bg-dark--}}
<body style="background-color: #205796!important; margin: auto;">
    <div  style="width: 90%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;">
        <div class="container" style="width: 90%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;">
            <div class="row justify-content-center">
                <div class="col-md-12" style="margin-left: auto!important;margin-right: auto!important; margin-top: 3rem!important; word-wrap: break-word; background-color: #fff; background-clip: border-box; border: 1px solid rgba(0,0,0,.125); border-radius: .25rem;">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fa fa-area-chart"></i></h4>
                        </div>
                        <!---------------------------------------------------------------------------------------------------------->
                        <div class="card-body table-responsive">
                            <!------------------------------------------------------ Si no encuentra datos ----------------------------------------------------->
                            @if(count($info)==0)
                                <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                            @endif
                            <!------------------------------------------------------ Muestra los datos que se encuentra ----------------------------------------------------->
                            @if(count($info)>0)
                                <div class="row justify-content-center" >
                                    <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_reporte.jpg') }}"  alt="Reporte diario" title="Reporte diario"
                                         style="max-width: 75vw; display:flex; margin:0 auto; text-align:center; vertical-align: middle">

                                    {{--<h1 class="pb-2 fw-bold" style="text-align:center;">¡Bienvenido a Cornel.io!</h1>--}}
                                    {{--<img src="https://cornelio.network/assets/img/lookingup.png" alt="navbar brand" class="navbar-brand"--}}
                                         {{--style="max-width: 30vw; display:flex; margin:0 auto; text-align:center; vertical-align: middle">--}}
                                    {{--<h3 class="pb-2 fw-bold" style="text-align:center;">¡Comencemos! vamos  ver el reporte diario ...</h3>--}}
                                    {{--<br></br>--}}
                                </div>



                            @foreach($info as $data)
                                    <h1 class="pb-2 fw-bold" style="text-align:center;">Análisis de datos del tema {{$data['name']}}</h1>
                                    </br></br>
                                    {{-- TABLA DE DATOS --}}
                                    <div class="col-sm-12" style=" width: 100%;overflow: auto">
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
                                        <tbody style="display:table-row-group; vertical-align: middle; border-color: inherit;">
                                            @if(isset($data['posts'] ))
                                                @foreach($data['posts'] as $post)
                                                    <tr style="display:table-row; text-align:center; vertical-align: inherit; border-color: inherit;">
                                                        <th style="width:20%; padding: .3rem; border-top: 1px solid #dee2e6; text-align: inherit; color:#0f3760;">
                                                            @if(isset($data['name']))
                                                                {{$data['name']}}
                                                            @endif
                                                        </th>
                                                        <td style="width:20%; text-align:center; padding: .3rem; border-top: 1px solid #dee2e6;">
                                                            @if(isset($post['page_name']))
                                                                {{$post['page_name']}}
                                                            @endif
                                                        </td>
                                                        <td style="width:20%; text-align:center; padding: .3rem; border-top: 1px solid #dee2e6;">
                                                            @if(isset($post['content']))
                                                                {{substr($post['content'], 0, 40).'...'}}
                                                            @endif
                                                        </td>
                                                        <td style="width:20%; text-align:center; padding: .3rem; border-top: 1px solid #dee2e6;">
                                                            @if(isset($post['comentarios']))
                                                                {{$post['comentarios']}}
                                                            @endif
                                                        </td>
                                                        <td style="width:20%; text-align:center; padding: .3rem; border-top: 1px solid #dee2e6;">
                                                            @if(isset($post['TotalReacciones']))
                                                                {{$post['TotalReacciones']}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    </div>
                                    {{-- GRAFICA DE LOS DATOS --}}
                                    @if(isset($data['grafica']))
                                        <div class="row justify-content-center">
                                            <multiline><br></br></multiline>
                                            <img style="max-width: 100%" src="{{ $data['grafica']}}">
                                        </div>
                                    @endif

                                    {{-- PUBLICIDAD --}}
                                    <div class="col-sm-12" style="overflow: auto">
                                        <div class="card mb-3" style="">
                                            @if(isset($data['name']))
                                                <br><div style="width: 100%!important"></div><h2 style="color:#0f3760; text-align:center;">Publicaciones del tema {{$data['name']}}</h2><div style="width: 100%!important"></div><br>
                                            @endif
                                            @if(isset($data['posts']))
                                                @foreach($data['posts'] as $post)
                                                    <div class="" style="flex: 0 0 50%; max-width: 50%; position: relative; min-height: 1px; padding-right: 25%; padding-left: 25%;">
                                                        <div style="background-color: #fff; background-clip: border-box; border: 1px solid rgba(0,0,0,.125); border-radius: .25rem; margin-bottom: 1rem!important;">
                                                            @if($post['picture']!=null)
                                                                <img src="{{$post['picture']}}" alt="" style="border-top-left-radius: calc(.25rem - 1px); border-top-right-radius: calc(.25rem - 1px); max-width: 100%; height: auto; vertical-align: middle; border-style: none; width: 100%!important;">
                                                            @endif
                                                            @if($post['video']!=null)
                                                                <video width="100%" height="auto" controls>
                                                                    <source src="{{$post['video']}}" type="video/mp4"></video>
                                                            @endif
                                                            <div style="-ms-flex: 1 1 auto; flex: 1 1 auto; padding: 1.25rem;">
                                                                <h6 class="" style="font-size: 1rem; margin-bottom: .25rem!important; color:#0f3760;">{{$post['page_name']}}</h6>
                                                                <p class="" style="margin-bottom: 0;font-size: 80%; font-weight: 400; margin-top: 0;">{{$post['content']}}
                                                                    @if($post['url']!=null)
                                                                        <br><a href="{{$post['url']}}" target="_blank">{{$post['title']}}</a>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            <hr style="margin-top: 0; margin-bottom: 0!important; border: 0px solid rgba(0,0,0,1); border-top: 1px solid rgba(0,0,0,.1);">
                                                            <div class="" style=" padding: 1.25rem; padding-bottom: .5rem!important; padding-top: .5rem!important; font-size: 80%; font-weight: 400;">
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img src="{{ asset('reacciones/like.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="like" style=" vertical-align: middle">{{$post['like']}}</label>
                                                                </div>
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="like" src="{{ asset('reacciones/love.png') }}"  alt="Love" title="Love" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="like" style=" vertical-align: middle">{{$post['love']}}</label>
                                                                </div>
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="Hahaha" src="{{ asset('reacciones/hahaha.png') }}" alt="Hahaha" title="Hahaha" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="Hahaha">{{$post['haha']}}</label>
                                                                </div>
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="Wow" src="{{ asset('reacciones/wow.png') }}" alt="Wow" title="Wow" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="Wow">{{$post['wow']}}</label>
                                                                </div>
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="Sad" src="{{ asset('reacciones/sad.png') }}" alt="Sad" title="Sad" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="Sad" >{{$post['sad']}}</label>
                                                                </div>
                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="Angry" src="{{ asset('reacciones/angry.png') }}" alt="Angry" title="Angry" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="Angry">{{$post['anger']}}</label>
                                                                </div>

                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="shared" src="{{ asset('reacciones/shared.png') }}" alt="shared" title="shared" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="shared" style=" vertical-align: middle">{{$post['shared']}}</label>
                                                                </div>

                                                                <div style="margin-right: 3px!important; display: inline-block!important">
                                                                    <img name="comentarios" src="{{ asset('reacciones/comment.png') }}" alt="comentarios" title="Comentarios" style="width: 18px; vertical-align: middle">
                                                                    <label id="" for="comentarios" style=" vertical-align: middle">{{$post['comentarios']}}</label>
                                                                </div>
                                                            </div>
                                                            <hr style="margin-top: 0; margin-bottom: 0!important; border: 0; border-top: 1px solid rgba(0,0,0,.1);">
                                                            <div class="card-footer small text-muted" style=" font-size: 80%; font-weight: 400; color: #6c757d!important; padding: .75rem 1.25rem; background-color: rgba(0,0,0,.03); border-top: 1px solid rgba(0,0,0,.125)">{{$post['created_time']}}</div>
                                                            <div class="card-footer small" style=" font-size: 80%; font-weight: 400; padding: .75rem 1.25rem; background-color: rgba(0,0,0,.03); border-top: 1px solid rgba(0,0,0,.125)">
                                                                @foreach($post['random'] as $comment)
                                                                    <p class="card-text small"><a href="https://business.facebook.com/{{$comment->comment_id}}" target="_blank"><strong>{{ $comment->commented_from="Sin"? "Usuario FB":$comment->commented_from }}</strong></a>
                                                                        <br>
                                                                        {{ $comment->comment }}
                                                                    </p>
                                                                    <div class="row justify-content-center pt-2"><p>{{\Carbon\Carbon::parse($comment->created_time)->format('Y-m-d h:i:s')}}</p></div>
                                                                @endforeach
                                                            </div>
                                                            <p>* Muestra aleatoria de 10 comentarios</p>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            @php
                                //dd( 'vamos');
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </body>
</html>

