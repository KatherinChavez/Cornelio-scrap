<!doctype html>
<html lang="es" style="background-color:grey;">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reporte de temas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
<div class="container">
    <div class="row justify-content-center" >
        <div class="card-body" >
            @if(isset($data))
                @if(isset($fechas))
                    <input type="hidden" name="start" id="start" value="{{$fechas['start']}}">
                    <input type="hidden" name="end" id="end" value="{{$fechas['end']}}">
                    <input type="hidden" name="end" id="sub" value="{{$sub_info['id']}}">
                @else
                    <input type="hidden" name="start" id="start" value="">
                    <input type="hidden" name="end" id="end" value="">
                @endif

                {{---------------------------------------------------- Se indica que no se encuentra datos -------------------------------------------------------}}
                @if(count($posts)==0)
                    <img name="Sin alertas" src="https://monitoreo.cornel.io/imagen/apps/sin_alertas.jpg" alt="Sin alertas" title="Sin alertas" style="display:block;">
                @endif

                <!------------------------------------------------------ Muestra los datos que se encuentra ----------------------------------------------------->
                @if(count($posts)>0)
                    <img name="Reporte diario" src="https://monitoreo.cornel.io/imagen/apps/encabezado_analisis.jpg"  alt="Reporte diario" title="Reporte diario" style="display:block; vertical-align: middle;">
                    <h2 style="color:#0f3760;">Impacto provocado sobre el tema {{$sub_info['name']}}</h2>
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
                    <br><br>
                    <!-------------------------------------------------- Estadistica --------------------------------------------------------->
                    <strong style="color:#0f3760;"><h3 align="center"><i class="fas fa-chart-bar"></i>Estadística</h3></strong>
                    <div class="row justify-content-center">
                        <multiline><br></br></multiline>
                        <img style="max-width: 100%" src="{{ $c}}">
                    </div>

                    <!-------------------------------------------------- Publicaciones --------------------------------------------------------->
                        <br><br>
                        <strong style="color:#0f3760;"><h3 align="center">Publicaciones del tema {{$sub_info['name']}}</h3></strong>
                    @foreach($posts as $post)
                        <br><br>
                        <div class="col-sm-10" style="display:block; margin-left: auto; margin-right: auto;">

                        @if($post['picture']!=null)
                            <img src="{{$post['picture']}}" alt="" class="card-img-top img-fluid w-100">
                        @endif

                        @if($post['video']!=null)
                            <video width="100%" height="auto" controls>
                            <source src="{{$post['video']}}" type="video/mp4"></video>
                        @endif

                        <h6 class="card-title mb-1">{{$post['page_name']}}</h6>
                        <p class="card-text small">{{$post['content']}}

                        @if($post['url']!=null)
                        <br>
                        <a href="{{$post['url']}}" target="_blank">{{$post['title']}}</a>
                        @endif
                        </p>
                        <hr class="my-0">
                        <div class="card-footer small text-muted">{{$post['created_time']}}</div>
                            @foreach($post['random'] as $comment)
                                <p class="card-text small"><a href="https://business.facebook.com/{{$comment->comment_id}}" target="_blank"><strong>{{ $comment->commented_from="Sin"? "Usuario FB":$comment->commented_from }}</strong></a>
                                    <br>
                                    {{ $comment->comment }}
                                </p>
                                <div class="row justify-content-center pt-2"><p>{{\Carbon\Carbon::parse($comment->created_time)->format('Y-m-d h:i:s')}}</p></div>
                            @endforeach
                            <p>* Muestra aleatoria de 10 comentarios</p>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>
</body>
</html>

