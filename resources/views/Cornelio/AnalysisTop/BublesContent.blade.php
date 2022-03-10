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
    <title>Conversación social</title>
    <!--[if gte mso 9]>

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
                                    <input type="hidden" name="idCompanie" id="idCompanie" value="{{$idCompanie}}">
                                    <input type="hidden" name="idContents" id="idContents" value="{{$idContents}}">
                                    <tr>
                                        <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:32px; line-height:36px; text-align:center; padding-bottom:20px;">

                                            <multiline>
                                                Conversación social
                                                <br></br>
                                                <p style="text-align: center;"> Datos de las últimas <b>24 horas</b></p>
                                            </multiline>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="h2-center" style="color:#000000; font-family:'Playfair Display', Times, 'Times New Roman', serif; font-size:32px; line-height:36px; text-align:center; padding-bottom:20px;">
                                            <div id="data" hidden>
                                            <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p0-15-30" style="padding: 0px 30px 70px 30px;" bgcolor="#fffffa">
                                            <div class="row">
                                                <div class="col-md-12 row justify-content-center" id="chart" style="display:block; margin-left: auto; margin-right: auto;"></div>
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>

    $(document).ready(getBubbles());

    /******************************************************************************************************************/

    function getBubbles() {
        let idCompanie = document.getElementById('idCompanie').value,
            idContents = document.getElementById('idContents').value,
            datos = {idCompanie, idContents};
//            datos = {idCompanie};
        axios.post('{{ route('Notification.wordBublesContent') }}', datos).then(response => {
            if(response.data.length>0){
                document.getElementById("chart").hidden = false;
                chartBubbles(response.data);
            } else {
                document.getElementById("data").hidden = false;
                $('#chart').html('<h3 style="margin-right: auto">No hay suficientes datos!</h3>');
                //document.getElementById("chart").hidden = true;
            }
        });
    }

    function chartBubbles(data) {
        Highcharts.chart('chart', {
            chart: {
                type: 'packedbubble',
                height: '350%'
            },
            title: {
                text: 'Top 10 de las palabras más usadas según los generadores de contenidos'
            },
            tooltip: {
                useHTML: true,
                pointFormat: '<b>{point.name}:</b> {point.value}'
            },
            plotOptions: {
                packedbubble: {
                    minSize: '20%',
                    maxSize: '100%',
                    zMin: 0,
                    zMax: 1000,
                    layoutAlgorithm: {
                        gravitationalConstant: 0.05,
                        splitSeries: true,
                        seriesInteraction: false,
                        dragBetweenSeries: true,
                        parentNodeLimit: true
                    },
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}',
                        filter: {
                            property: 'y',
                            operator: '>',
                            value: 250
                        },
                        style: {
                            color: 'black',
                            textOutline: 'none',
                            fontWeight: 'normal'
                        }
                    }
                }
            },
            series: data
        });
    }

    /******************************************************************************************************************/

</script>

</body>
</html>
