<!doctype html>
<html lang="es" style="background-color:grey;">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Top 10 de las publicaciones con mayor interacción</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
<img align="center" src="https://monitoreo.cornel.io/img/encabezado_top10.png" style=" width:100%; vertical-align: middle">
<div class="container">
    <div class="row justify-content-center" >
        <div class="col-md-12">
            <div class="card-header">
                <strong style="color:#0f3760;"><h3 align="center">Top 10 de publicaciones con mayor interacción</h3></strong><br>
                <p align="center">Datos actualizados a las <?php echo date("j/n/Y h:i")?></p>
            </div>
            </br>
            <div class="card-body" >
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-sm-12">
                            @foreach($topData as $data)
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-1" style="color:#0f3760;">{{$data['name']}}</h5>
                                            <p class="card-text small mb-2">{{$data['content']}}

                                            <div class="col-sm-12">
                                                @if(isset($data['attachment']))
                                                    <img src="{{$data['attachment']}}" style=" width:100%; vertical-align: middle">
                                                @endif
                                                @if(isset($data['url']) && isset($data['title']))
                                                    <a href="{{$data['url']}}">{{$data['title']}}</a>
                                                @endif
                                            </div>

                                            <hr class="my-0">

                                            @if(isset($data['reaction']))
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px;margin-top: 2px; vertical-align: middle">
                                                    <label id="" for="Like" style="margin-top: 2px;">{{$data['reaction']['likes']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png"  alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Love">{{$data['reaction']['love']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png"  alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Hahaha">{{$data['reaction']['haha']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Wow">{{$data['reaction']['wow']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png"  alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Sad">{{$data['reaction']['sad']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png"  alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Angry">{{$data['reaction']['angry']}}</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="Shared">{{$data['reaction']['shared']}}</label>
                                                </div>
                                            @else
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 6px;">
                                                    <img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png"  alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                    <label id="" for="Like">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png"  alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Love">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png"  alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Hahaha">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png"  alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Wow">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Sad">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png"  alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                    <label id="" for="Angry">0</label>
                                                </div>
                                                <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                                    <img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png"  alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                    <label id="" for="Shared">0</label>
                                                </div>
                                            @endif()
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
