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
                        <div class="row justify-content-center" >
                                <h1 class="pb-2 fw-bold" style="text-align:center;">¡Bienvenido a Cornel.io!</h1>
                                <img src="https://cornelio.network/assets/img/lookingup.png" alt="navbar brand" class="navbar-brand"
                                     style="max-width: 30vw; display:flex; margin:0 auto; text-align:center; vertical-align: middle">
                                <h3 class="pb-2 fw-bold" style="text-align:center;">¡Comencemos! vamos  ver el reporte diario ...</h3>
                                <br></br>
                            </div>

                        <div class="col-md-12">
                            <div class="card card-primary bg-primary-gradient">
                                <div class="card-body">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold row justify-content-center">
                                        Top 10 de publicación con mayor interacción
                                    </h5>
                                    @php
                                    //dd($topData);
                                    @endphp

                                    <div class="table-responsive">
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            //dd( 'vamps');
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

