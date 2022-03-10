<!doctype html>
<html lang="es" style="background-color:grey;">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Top 20 de las publicaciones con mayor interacción</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
<img align="center" src="https://monitoreo.cornel.io/img/encabezado_top10.png" style=" width:100%; vertical-align: middle">
<div class="container">
    <div class="row justify-content-center" >
        <div class="col-md-12">
            <div class="card-header">
                <strong style="color:#0f3760;"><h3 align="center">Top 20 de publicaciones con mayor interacción</h3></strong><br>
                <p align="center">Datos actualizados a las <?php echo date("j/n/Y h:i")?></p>
            </div>
            </br>
            <div class="card-body" >
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-sm-12">
                            <div class="table-responsive" style="overflow-x:auto;">
                            @foreach($topData as $data)
                                <div class="col-sm-12">
                                    @if(isset($data['attachment']))
                                        <div class="col-sm-12">
                                            {{--<img onerror="this.onerror=null;this.src='';" style=" width:100%; vertical-align: middle" src="{{$data['attachment']['picture']}}" >--}}
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title mb-1" style="color:#0f3760;">{{$data['name']}}</h6>
                                        <p class="card-text small">{{$data['content']}}
                                        <hr class="my-0">

                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px;margin-top: 2px; vertical-align: middle">
                                            <label id="" for="Like" style="margin-top: 2px;">{{$data['likes']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png"  alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                            <label id="" for="Love">{{$data['love']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png"  alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                            <label id="" for="Hahaha">{{$data['haha']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                            <label id="" for="Wow">{{$data['wow']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png"  alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                            <label id="" for="Sad">{{$data['sad']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png"  alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                            <label id="" for="Angry">{{$data['angry']}}</label>
                                        </div>
                                        <div class="mr-2 d-inline-block" style="width:50px; height: 50px; display: inline-block; margin-top: 5px;">
                                            <img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                            <label id="" for="Shared">{{$data['shared']}}</label>
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
