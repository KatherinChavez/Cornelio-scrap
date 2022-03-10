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
    <!--[if !mso]><!-->
    {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">--}}
    <link href="https://fonts.googleapis.com/css?family=Kreon:400,700|Playfair+Display:400,400i,700,700i|Raleway:400,400i,700,700i|Roboto:400,400i,700,700i"  rel="stylesheet"/>
    <!--<![endif]-->
    <title>Análisis</title>
    <!--[if gte mso 9]>
    <style type="text/css" media="all">
        sup {
            font-size: 100% !important;
        }
    </style>
    <![endif]-->


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
                                    <input type="hidden" name="idTopics" id="idTopics" value="{{$idTopics}}">
                                    <tr>
                                        <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:32px; line-height:36px; text-align:center; padding-bottom:20px;">

                                            <multiline>
                                                Nube de palabra generado por los comentarios
                                                <br></br>
                                            </multiline>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p0-15-30" style="padding: 0px 30px 70px 30px;" bgcolor="#fffffa">
                                            {{--<div class="justify-content-center" id="estadisticaC" style="display:block; margin-left: auto; margin-right: auto;"></div>--}}

                                            <div class="row">
                                                <div class="col-md-12 row justify-content-center" id="estadisticaC" style="display:block; margin-left: auto; margin-right: auto;"></div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:32px; line-height:36px; text-align:center; padding-bottom:20px;">
                                            <multiline>
                                                Sentimientos generados en los comentarios
                                                <br></br>
                                            </multiline>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="p0-15-30" style="padding: 0px 30px 70px 30px;" bgcolor="#fffffa">
                                            <div class="col-md-12 row justify-content-center">
                                                <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                                    <i class="fas fa-chart-bar"></i> Sentimiento de Conversaciones
                                                </h5>

                                                <div class="col-md-12 row justify-content-center" id="estadistica"></div>
                                            </div>

                                        </td>
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

    $(document).ready(getDatos(), getTema());

    function getDatos(id){
        let idTopics = document.getElementById('idTopics').value,
            datos = {idTopics};

        axios.post('{{ route('Notification.analysisCloud') }}', datos).then(response => {
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

            $('#estadisticaC').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="' + tema + '"></div>');
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
                $('#estadisticaC').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="' + tema + '"><h2> No hay suficientes datos! </h2></div>');
                console.log('no entre');
            }
        });
    }


    /************************************* Sentimientos de los tema ***************************************************/
    function getTema() {
        let idTopics = document.getElementById('idTopics').value,
            datos = {idTopics};

        axios.post('{{ route('Notification.analysisFeeling') }}', datos).then(response => {
            if((response.data)){
                chartTema(response.data);
            }else{
                $('#estadistica').html('<h3>No hay suficientes datos!</h3>');
                return 'No se puede mostrar los datos';
            }

        });

    }

    function chartTema(data) {
        var reacciones = data.Tema;
        reacciones.forEach(function (reaccion, index) {
            $('#estadistica').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="'+ reaccion.Tema+'"></div>');
            Highcharts.chart(reaccion.Tema, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },

                title: {
                    text: 'Sentimiento de conversación <br>del tema  <b>'+ reaccion.Tema + '</b> <br>en las Últimas <b>24 horas</b>',

                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }

                },

                colors: ['#317f43', '#CB3234', '#ffff00'],
                series: [{
                    type: 'pie',
                    name: 'Porcentaje de Reacciones',
                    innerSize: '50%',
                    data: [
                        ['Positivos ' , reaccion.interation.positivo],
                        ['Negativos ' , reaccion.interation.negativo],
                        ['Neutrales ' , reaccion.interation.neutral],
                    ]
                }]



            });
        });

    }

    /******************************************************************************************************************/

</script>

</body>
</html>
