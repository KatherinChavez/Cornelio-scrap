<!doctype html>
<html lang="en">
<head>

    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="format-detection" content="date=no"/>
    <meta name="format-detection" content="address=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="x-apple-disable-message-reformatting"/>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Kreon:400,700|Playfair+Display:400,400i,700,700i|Raleway:400,400i,700,700i|Roboto:400,400i,700,700i"  rel="stylesheet"/>

    <title>Información general del tema</title>

    <style type="text/css" media="all">
        sup {
            font-size: 100% !important;
        }
    </style>

    <style type="text/css" media="screen">
        /* Linked Styles */
        body {
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
            min-width: 100% !important;
            width: 100% !important;
            background: #1e52bd;
            -webkit-text-size-adjust: none
        }

        a {
            color: #000001;
            text-decoration: none
        }

        p {
            padding: 0 !important;
            margin: 0 !important
        }

        img {
            -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */
        }


        .text-footer2 a {
            color: #fffffa;
        }

        /* Mobile styles */
        @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
            .mobile-shell {
                width: 100% !important;
                min-width: 100% !important;
            }

            .td {
                width: 100% !important;
                min-width: 100% !important;
            }

            .p30-15 {
                padding: 30px 15px !important;
            }

            .p30-15-0 {
                padding: 30px 15px 0px 15px !important;
            }

            .p0-15-30 {
                padding: 0px 15px 30px 15px !important;
            }
            .fluid-img img {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }

        }
    </style>
</head>
<body class="body" style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#1e52bd; -webkit-text-size-adjust:none;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#1e52bd">
    <tr>
        <td align="center" valign="top">
            <!-- Main -->
            <table width="850" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                <tr>
                    <td class="td" style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                        <repeater>
                            <layout label='Section 3'>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ebebeb" style=" padding: 70px 0px 0px;background-color:#fffffa; border-radius: 5px 75px 5px 5px; margin-top: 40px;">
                                    @if(isset($fechas))
                                        <input type="hidden" name="start" id="start" value="{{$fechas['start']}}">
                                        <input type="hidden" name="end" id="end" value="{{$fechas['end']}}">
                                    @else
                                        <input type="hidden" name="start" id="start" value="">
                                        <input type="hidden" name="end" id="end" value="">
                                    @endif
                                    <tr>
                                        @if(count($posts)==0)
                                            <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:20px; line-height:36px; text-align:center; ">
                                                <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                                            </td>
                                        @endif

                                        @if(count($posts)>0)
                                            <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:20px; line-height:36px; text-align:center; ">
                                                <br><br>
                                                <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_analisis.jpg') }}"  alt="Reporte diario" title="Reporte diario" style="display:block; margin-left: auto; margin-right: auto;" >
                                                <h1 style="color:#0f3760;">Impacto provocado sobre el tema {{$sub['name']}}</h1>

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

                                                <div class="row" style="margin-left: auto; margin-right: auto;">
                                                    <br><br>
                                                    <input type="hidden" name="idTopics" id="idTopics" value="{{$sub['id']}}">
                                                    <div class="justify-content-center" id="estadisticaC" style="display:block; margin-left: auto; margin-right: auto;"></div>

                                                    <br>

                                                    <div class="col-sm-12">
                                                        <br>
                                                        <h2>Publicaciones del tema {{$sub['name']}}</h2>
                                                        <br>
                                                    </div>

                                                    @foreach($posts as $post)
                                                        <br>
                                                        <div class="col-sm-4" style="overflow: auto; margin-right: auto;">
                                                            <div class="card mb-3" style=" height: 700px;overflow: auto" style=" overflow: auto">
                                                                <br>
                                                                <h5 class="card-title mb-1" style="color:#0f3760;">{{$post['page_name']}}</h5>
                                                                @if($post['picture']!=null)
                                                                    <img src="{{$post['picture']}}" alt="" class="card-img-top img-fluid w-100">
                                                                @endif

                                                                @if($post['video']!=null)
                                                                    <video width="100%" height="auto" controls>
                                                                        <source src="{{$post['video']}}" type="video/mp4"></video>
                                                                @endif


                                                                <div class="card-footer small text-muted"><h6 style="margin-left: auto">{{$post['created_time']}}</h6></div>
                                                                <p class="card-text small">{{$post['content']}}
                                                                    @if($post['url']!=null)
                                                                        <br>
                                                                        <a href="{{$post['url']}}" target="_blank">{{$post['title']}}</a>
                                                                    @endif
                                                                </p>

                                                                <hr class="my-0">
                                                                <div class="card-footer small text-muted">
                                                                    @if(isset($post['likes']))
                                                                    <div class="mr-2 d-inline-block">
                                                                        <img name="Like" src="{{asset("/reacciones/like.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                                        <label id="" for="Like">{{$post['likes']}}</label>
                                                                    </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Like" src="{{asset("/reacciones/like.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                                            <label id="" for="Like">0</label>
                                                                        </div>
                                                                    @endif


                                                                    @if(isset($post['love']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Love" src="{{asset("/reacciones/love.png")}}" alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Love">{{$post['love']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Like" src="{{asset("/reacciones/love.png")}}" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                                                                            <label id="" for="Like">0</label>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($post['haha']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Hahaha" src="{{asset("/reacciones/hahaha.png")}}" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Hahaha">{{$post['haha']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Hahaha" src="{{asset("/reacciones/hahaha.png")}}" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Hahaha">0</label>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($post['wow']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Wow" src="{{asset("/reacciones/wow.png")}}" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Wow">{{$post['wow']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Wow" src="{{asset("/reacciones/wow.png")}}" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Wow">0</label>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($post['sad']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Sad" src="{{asset("/reacciones/sad.png")}}" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Sad">{{$post['sad']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Sad" src="{{asset("/reacciones/sad.png")}}" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Sad">0</label>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($post['angry']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Angry" src="{{asset("/reacciones/angry.png")}}" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Angry">{{$post['angry']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Angry" src="{{asset("/reacciones/angry.png")}}" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                                                                            <label id="" for="Angry">0</label>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($post['shared']))
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Shared" src="{{asset("/reacciones/shared.png")}}" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                                            <label id="" for="Shared">{{$post['shared']}}</label>
                                                                        </div>
                                                                    @else
                                                                        <div class="mr-2 d-inline-block">
                                                                            <img name="Shared" src="{{asset("/reacciones/shared.png")}}" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                                                                            <label id="" for="Shared">0</label>
                                                                        </div>
                                                                   @endif

                                                                </div>
                                                                <hr class="my-0">
                                                                <div class="card-body" id="comments-{{$post['post_id']}}"></div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </layout>
                        </repeater>
                        <!-- Footer -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="p30-15-0" bgcolor="#fffffa" style="border-radius: 0px 0px 20px 20px; padding: 70px 30px 0px 30px;"> </td>
                            </tr>
                        </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="text-footer2 p30-15" style="padding: 30px 15px 50px 15px; color:#a9b6e0; font-family:'Raleway', Arial,sans-serif; font-size:12px; line-height:22px; text-align:center;">
                                    <preferences class="link-white" style="color:#fffffa; text-decoration:none;"> &copy; Cornelio, </preferences>
                                    <multiline> una marca registrada por la</multiline>
                                    <preferences class="link-white" style="color:#fffffa; text-decoration:none;">Agencia Digital de Costa Rica - </preferences>
                                    <preferences class="link-white" style="color:#fffffa; text-decoration:none;">{{ date('Y') }}</preferences>
                                </td>
                            </tr>
                        </table>
                        <!-- END Footer -->
                    </td>
                </tr>
            </table>
            <!-- END Main -->
        </td>
    </tr>
</table>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/wordcloud.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>

    $(document).ready(getDatos(), getRandom());

    function getDatos(id){
        let idTopics = document.getElementById('idTopics').value,
            datos = {idTopics};

        axios.post('{{ route('Notification.topicsComparatorCloud') }}', datos).then(response => {
            if((response.data)){
                procesarComentarios(response.data.Tema,id);
            }else{
                $('#estadisticaC').html('<h3>No hay suficientes datos!</h3>');

            }

        });
    }

    function procesarComentarios(data,id) {
        let palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
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
            ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', '?', '!','¡', '😲',
        ];
        data.forEach(function (post, indexsub) {
            let comentario="";
            let filtrado=[];
            tema = post.name;

            $('#estadisticaC').append('<div class="col-md-12 mt-5 b-b1 pb-2 mb-4" id="' + tema + '"></div>');
            let comentariosTema = post.comment;

            comentariosTema.forEach(function (posts, index1) {
                var texto = posts.comment;
                palabras.forEach(function (palabra, index2) {
                    i = 0;
                    for (; i != -1;) {
                        i = texto.indexOf(palabra);
                        texto = texto.replace(palabra, " ")
                    }
                });
                comentario += '' + texto;
            });

            var text = comentario;
            if (text.length > 1) {
                var lines = text.split(/[,\. ]+/g),
                    dataP = Highcharts.reduce(lines, function (arr, word) {
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
                dataP = dataP.sort(function (a, b) {
                    return (b.weight - a.weight)
                });
                var temporal;
                var hasta = dataP.length;
                if (hasta > 60) {
                    hasta = 60;
                }
                for (i = 0; i < hasta; i++) {
                    var x = dataP[i].name;
                    if (x.length > 3) {
                        temporal = dataP[i];
                        filtrado.push(temporal);
                    }
                }
                Highcharts.chart(tema, {
                    series: [{
                        type: 'wordcloud',
                        data: filtrado,
                        name: 'Menciones'
                    }],
                    title: {
                        text: 'Tema de Conversación del tema ' + tema + ''
                    }
                });

            }else{
                $('#estadisticaC').append('<div class="col-md-12 mt-5 b-b1 pb-2 mb-4" id="' + tema + '"> <h2>No hay suficientes datos! </h2></div>');
                console.log('no entre');
            }
        });
    }


    /************************************* Comentarios ***************************************************/

    function getRandom() {
        var start=document.getElementById('start').value,
            end=document.getElementById('end').value,
            datos;
        if(start!=='' && end!==''){
            datos={subcategoria_id:'{{$sub['id']}}',start:start,end:end};
        }else{
            datos={subcategoria_id:'{{$sub['id']}}'};
        }
        axios.post('{{ route('Report.MessageRandom') }}', datos).then(response => {
            comentarios(response.data);
        });
    }

    function comentarios(data) {
        var longitud=data.length;
        var i;
        for(i=0; i<longitud; i++ ){
            var com=data[i].comentarios,post_id=data[i].post, commList='';
            com.forEach(function (comentario,index) {
                commList+='<div class="received_withd_msg"><p class="card-text small ">'+comentario.comment+'</p>' +
                    '</div><p class="small text-muted">'+comentario.created_time+'</p>';
            });
            $('#comments-'+post_id).html(commList);
        }
    }


    /******************************************************************************************************************/

</script>

</body>
</html>
