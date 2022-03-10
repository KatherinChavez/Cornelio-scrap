@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="fw-bold">Comparar páginas</h3></div>
                    <div class="card-body table-responsive">
                        <h5>Al ingresar el alias o id de la página puedes realizar comparación de las páginas que se ha obtenido en la extracción de datos</h5>
                        <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                        <div class="form-row align-items-center">
                            <div class="col-sm-6 my-1">
                                <label class="sr-only" for="start">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-sm-6 my-1">
                                <label class="sr-only" for="end">Fecha Final</label>
                                <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="row col-lg-12 text-center">
                                <div class="card-title col-lg-12 mt-3">
                                    <p class="fw-bold h4 text-center">Ingrese la páginas a comparar</p>
                                </div>
                                <div class="col-lg-5 mt--3">
                                    <div class="avatar mb-3">
                                        <img id="profile-pagina1" src="https://avatars.dicebear.com/api/initials/na.svg" alt="" class="avatar-img rounded">
                                    </div>

                                    <label id="span-pagina1" class="input-group-addon" for="pagina1"></label>
                                    <div class="input-group">
                                        <input id="pagina1" type="text" class="form-control" placeholder="Página 1"
                                               aria-describedby="basic-addon1">
                                    </div>
                                </div>
                                <div class="justify-content-center col-lg-2 mt-5">
                                    <h2 class="fw-bold">vs.</h2>
                                </div>
                                <div class="col-lg-5 mt--3">
                                    <div class="avatar mb-3">
                                        <img id="profile-pagina2" src="https://avatars.dicebear.com/api/initials/na.svg" alt="" class="avatar-img rounded">
                                    </div>
                                    <span id="span-pagina2" class="input-group-addon" for="pagina2"></span>
                                    <div class="input-group">
                                        <input id="pagina2" type="text" class="form-control" placeholder="Pagina 2"
                                               aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mt-3">
                                <button type="button" class="btn btn-sm btn-primary btn-block" onclick="verificar()">
                                    Comparar
                                </button>
                            </div>
                        </div>


                        <div class="form-row align-items-center">

                            <!------------------------------------------------ Talking -------------------------------------------------------->
                            <br><br>
                            <div id="section2" class="col-sm-12">
                                <div class="row">
                                    <div id="talking-chart" class="col-sm-12"></div>
                                </div>
                            </div>

                            <!------------------------------------------------ Calculos -------------------------------------------------------->
                            <br><br>
                            <div id="section3" class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="bar-fan-total"></div>
                                        <div id="bar-fan"></div>
                                    </div>
                                    <!------------------------------------------------ Grafica -------------------------------------------------------->
                                    <div class="col-sm-12">
                                        <div class="card-header">
                                            </br></br>
                                            <h4>
                                                <i class="fas fa-chart-line"></i> Estadística de comentarios
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart" class="container">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="card-header">
                                            </br></br>
                                            <h4>
                                                <i class="fas fa-chart-line"></i> Estadística de publicación
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div id="chartPublicaction" class="container">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Final de la section 3 -->


                            <!------------------------------------------------ Tipos -------------------------------------------------------->
                            <br><br>
                            <div id="section5" class="col-sm-12">
                                </br></br>
                                <div id="tipo-chart"></div>
                                <div id="tabla-tipo" class="form-row align-items-center">
                                    <div id="tabla-tipo1" class="col-md-6"></div>
                                    <div id="tabla-tipo2" class="col-md-6"></div>
                                    </br></br>
                                </div>
                            </div>
                            <!-- Final de la section 5 -->


                            <!------------------------------------------------ Engagement -------------------------------------------------------->
                            <br><br>
                            <div id="section6" class="col-sm-12">
                                <div id="engagement-chart"></div>
                                </br></br>
                                <div id="tabla" class="form-row align-items-center">
                                    <div id="tabla-engagement1" class="col-md-6"></div>
                                    <div id="tabla-engagement2" class="col-md-6"></div>
                                </div>
                            </div>
                            <!-- Final de la section 6 -->

                            <!------------------------------------------------ Publicación -------------------------------------------------------->
                            </br></br>
                            <div id="section7" class="col-sm-12 ocultar">
                                <br><br>
                                <h2 id="titulo-top"></h2>
                                <div id="top-1" class="form-row align-items-center">
                                    <div id="top-1-1" class="col-md-6"></div>
                                    <div id="top-1-2" class="col-md-6"></div>
                                </div>
                                <br>
                                <hr>

                                <div id="top-2" class="form-row align-items-center">
                                    <div id="top-2-1" class="col-xs-12 col-md-6"></div>
                                    <div id="top-2-2" class="col-xs-12 col-md-6"></div>
                                </div>
                                <br>
                                <hr>

                                <div id="top-3" class="form-row align-items-center">
                                    <div id="top-3-1" class="col-xs-12 col-md-6"></div>
                                    <div id="top-3-2" class="col-xs-12 col-md-6"></div>
                                </div>
                                <br>
                                <hr>

                                <div id="top-4" class="form-row align-items-center">
                                    <div id="top-4-1" class="col-xs-12 col-md-6"></div>
                                    <div id="top-4-2" class="col-xs-12 col-md-6"></div>
                                </div>
                                <br>
                                <hr>

                                <div id="top-5" class="form-row align-items-center">
                                    <div id="top-5-1" class="col-xs-12 col-md-6"></div>
                                    <div id="top-5-2" class="col-xs-12 col-md-6"></div>
                                </div>
                                <br>
                            </div>
                            <!-- Final de la section 7 -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script>
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn();
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            // alert('entre');
            FB.api(
                '/oauth/access_token',
                'GET',
                {
                    "client_id": "{{env('APP_FB_ID_2')}}",
                    "client_secret": "{{env('APP_FB_SECRET_2')}}",
                    "grant_type": "client_credentials"
                },

                function (response) {
                    if (response.access_token) {
                        pageAccessToken = response.access_token;
                        //showProcessing();
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

        $(document).ready(function () {

            $('#pagina1').focusout(function () {
                var pagina = document.getElementById('pagina1').value;
                ObtenerPagina(pagina, 1);
            });
            $('#pagina2').focusout(function () {
                var pagina = document.getElementById('pagina2').value;
                ObtenerPagina(pagina, 2);
            });
        });

        var pagina1 = 0, pagina2 = 0;
        pageAccessToken = "";

        /******************************************************** Pagina  *****************************************************************/

        function ObtenerPagina(page, tipo) {
            FB.api(
                '/' + page + '',
                'GET',
                // {"fields":"name,id,picture"},
                {"fields": "name,id,picture", "access_token": pageAccessToken},
                function (response) {
                    idP = response.id;
                    var nombre = response.name;
                    if (!response || response.error) {
                        if (tipo == 1) {
                            pagina1 = 0;
                            // $('#mensaje-alerta').html("La pag. 1 no existe o no esta disponible, por favor intentelo nuevamente");
                        }
                        if (tipo == 2) {
                            pagina2 = 0;
                            // $('#mensaje-alerta').html("La pag. 2 no existe o no esta disponible, por favor intentelo nuevamente");

                        }
                        $('#alert').removeClass('ocultar');
                        return false;
                    }
                    if (tipo == 1) {
                        $('#span-pagina1').html(response.name);
                        document.getElementById('pagina1').value = response.id;
                        pagina1 = response.id;
                        $('#profile-pagina1').attr('src', response.picture.data.url);
                        $('#alert').addClass('ocultar');

                    }
                    if (tipo == 2) {
                        $('#span-pagina2').html(response.name);
                        document.getElementById('pagina2').value = response.id;
                        pagina2 = response.id;
                        $('#profile-pagina2').attr('src', response.picture.data.url);
                        $('#alert').addClass('ocultar');
                    }
                }
            );
        }

        function verificar() {
            if (pagina1 === 0 || pagina2 === 0) {
                return false;
            }
            let user = document.getElementById("user").value;
            datos = {user, pagina1, pagina2};
            axios.post('{{ route('Comparator.CheckPage',$company ?? '') }}', datos).then(response => {
                var resultado = response.data;
                if (resultado.pagina) {
                    var pagina = resultado.pagina;
                    if (pagina === pagina1) {
                        var name = $('#span-pagina1').text();
                        swal('Ops', `La pagina ` + name +  `no tiene datos, por favor seleccione la opción consultá de Facebook`, 'error');
                        return false;
                    }
                    if (pagina === pagina2) {
                        var name = $('#span-pagina2').text();
                        swal('Ops', `La pagina ` + name +  `no tiene datos, por favor seleccione la opción consultá de Facebook`, 'error');

                        return false;
                    }
                } else {
                    getTalking();
                    dailyPost();
                    comments();
                    geTipo();
                }
            });
        }

        /******************************************************** Más hablado *****************************************************************/
        function getTalking() {
            let user = document.getElementById("user").value;
            datos = {user, pagina1, pagina2};

            axios.post('{{ route('Comparator.Talking') }}', datos).then(response => {
                talking(response.data);
            });
        }

        function talking(data) {
            var name1 = $('#span-pagina1').text();
            var name2 = $('#span-pagina2').text();
            var chart = Highcharts.chart('talking-chart', {

                chart: {
                    type: 'column'
                },

                title: {
                    text: 'Páginas con mayor interacción'
                },

                subtitle: {
                    text: 'Mayor interacción en el último mes'
                },

                legend: {
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical'
                },

                xAxis: {
                    categories: ['Último mes'],
                    labels: {
                        x: -10
                    }
                },

                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'Cantidad'
                    }
                },

                series: [{
                    name: name1,
                    data: [parseFloat(data[1].talking)]
                }, {
                    name: name2,
                    data: [parseFloat(data[2].talking)]
                }],

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            yAxis: {
                                labels: {
                                    align: 'left',
                                    x: 0,
                                    y: -5
                                },
                                title: {
                                    text: null
                                }
                            },
                            subtitle: {
                                text: null
                            },
                            credits: {
                                enabled: false
                            }
                        }
                    }]
                }
            });
        }

        /******************************************************** Post diarios *****************************************************************/

        function dailyPost(){
            let start = document.getElementById("start").value;
            end = document.getElementById("end").value;
            user = document.getElementById("user").value;
            datos = {start, end, user, pagina1, pagina2};


            axios.post('{{ route('Comparator.DailyPost') }}', datos).then(response => {
                var resultado = response.data;
                bar(resultado);

                if( resultado.status == "success") {
                    chartPublication(response.data);
                } else {
                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
                }
            });
        }

        function chartPublication(data) {
            var comment = data.Comment;
            Highcharts.chart('chartPublicaction', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Interacción de publicación'
                },
                yAxis: {
                    title: {
                        text: 'Total'
                    },
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                xAxis: {
                    categories: data.fechas
                },
                series: comment,

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }
            });
        }

        /******************************************************** Porcentaje *****************************************************************/
        function bar(data) {
            var paginas = data.Comment;
            var total1 = 0, total2 = 0, i = 1, por1 = 0, por2 = 0, barList = '', barListTotal = '';
            var name1 = $('#span-pagina1').text();
            var name2 = $('#span-pagina2').text();
            for (var key in paginas) {
                var pagina = paginas[key];
                if (i === 1) {
                    total1 = pagina.total;
                } else {
                    total2 = pagina.total;
                }
                i++;
            }
            var total = total1 + total2;
            por1 = (total1 / total) * 100;
            por2 = (total2 / total) * 100;
            barListTotal += ' <br><h1>Resumen</h1><hr><h2>Total Publicaciones</h2><div class="progress">';
//            barListTotal += '   <div class="progress-bar progress-bar-success" style="background-color:  #00b386; width: ' + por1 + '%">';
            barListTotal += '   <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="background-color:  #00b386; width: ' + por1 + '%">';
            barListTotal += '   <span class="sr-only" style="background-color: #000000;">' + total1 + ' comments (total)</span><span>' + name1 + ': ' + total1 + '</span>';
            barListTotal += '   </div>';

            barListTotal += '   <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"  style="background-color:  #2e5cb8;width: ' + por2 + '%">';
            barListTotal += '   <span class="sr-only" style="background-color: #000000;">' + total2 + '% comments (total)</span><span>' + name2 + ': ' + total2 + '</span>';
            barListTotal += '   </div>';
            barListTotal += '   </div>';
            $('#bar-fan-total').html(barListTotal);


            barList += ' <br><hr><h2>Porcentaje Publicaciones</h2><div class="progress">';
            barList += '   <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"  style="background-color: #990033; width: ' + por1 + '%">';
            barList += '   <span class="sr-only" style="background-color: #000000;">' + por1 + '% Complete</span><span>' + name1 + ': ' + por1.toFixed(2) + '%</span>';
            barList += '   </div>';
            barList += '   <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"  style="background-color:  #e68a00; width: ' + por2 + '%">';
            barList += '   <span class="sr-only" style="background-color: #000000;">' + por2 + '% Complete</span><span>' + name2 + ': ' + por2.toFixed(2) + '%</span>';
            barList += '   </div>';
            barList += '  </div>';
            $('#bar-fan').html(barList);
        }

        /******************************************************** Comentario *****************************************************************/

        function comments(){

            let start = document.getElementById("start").value;
            end = document.getElementById("end").value;
            user = document.getElementById("user").value;
            datos = {start, end, user, pagina1, pagina2};


            axios.post('{{ route('Comparator.Comments') }}', datos).then(response => {
                var resultado = response.data;
                if( resultado.status == "success") {
                    chartComment(response.data);
                } else {
                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
                }
            });
        }

        function chartComment(data) {
            var comment = data.Comment;
            Highcharts.chart('chart', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Interacción de comentarios'
                },
                yAxis: {
                    title: {
                        text: 'Total'
                    },
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                xAxis: {
                    categories: data.fechas
                },
                series: comment,

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }
            });
        }

        /******************************************************** Tipo *****************************************************************/

        function geTipo() {
            let start = document.getElementById("start").value;
            end = document.getElementById("end").value;
            user = document.getElementById("user").value;
            datos = {start, end, user, pagina1, pagina2};

            axios.post('{{ route('Comparator.TypePost',$company ?? '') }}', datos).then(response => {
                tipo(response.data);
            });
        }

        function tipo(data) {
            var video1, video2, photo1, photo2, link1, link2, status1, status2, tipoList1 = '', tipoList2 = '';
            var name1 = $('#span-pagina1').text();
            var name2 = $('#span-pagina2').text();
            video1 = data[1].video;
            photo1 = data[1].photo;
            link1 = data[1].link;
            status1 = data[1].status;

            video2 = data[2].video;
            photo2 = data[2].photo;
            link2 = data[2].link;
            status2 = data[2].status;

            tipoList1 += '<h3>' + name1 + '</h3>';
            tipoList1 += '<table class="table table-striped table-hover">';
            tipoList1 += '<tr>';
            tipoList1 += '<th>Tipo</th>';
            tipoList1 += '<th>Publicaciones</th>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Photo</td>';
            tipoList1 += '<td>' + photo1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Video</td>';
            tipoList1 += '<td>' + video1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Link</td>';
            tipoList1 += '<td>' + link1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Status</td>';
            tipoList1 += '<td>' + status1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '</table>';
            $('#tabla-tipo1').html(tipoList1);

            tipoList2 += '<h3>' + name2 + '</h3>';
            tipoList2 += '<table class="table table-striped table-hover">';
            tipoList2 += '<tr>';
            tipoList2 += '<th>Tipo</th>';
            tipoList2 += '<th>Publicaciones</th>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Photo</td>';
            tipoList2 += '<td>' + photo2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Video</td>';
            tipoList2 += '<td>' + video2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Link</td>';
            tipoList2 += '<td>' + link2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Status</td>';
            tipoList2 += '<td>' + status2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '</table>';
            $('#tabla-tipo2').html(tipoList2);

            Highcharts.chart('tipo-chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Tipo de Publicación'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        name1,
                        name2
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Photo',
                    data: [photo1, photo2]

                }, {
                    name: 'Video',
                    data: [video1, video2]

                }, {
                    name: 'Link',
                    data: [link1, link2]

                }, {
                    name: 'status',
                    data: [status1, status2]

                }]

            });
            engagement();
        }


        /******************************************************** Engagement *****************************************************************/
        function engagement() {
            let start = document.getElementById("start").value;
            end = document.getElementById("end").value;
            user = document.getElementById("user").value;
            datos = {start, end, user, pagina1, pagina2};

            axios.post('{{ route('Comparator.Engagement',$company ?? '') }}', datos).then(response => {
                procesarEngagement(response.data);
            });
        }

        function procesarEngagement(data) {
            var name1 = $('#span-pagina1').text();
            var name2 = $('#span-pagina2').text();
            var pagina1 = data.pagina1;
            var pagina2 = data.pagina2;
            var comments1 = 0, comments2 = 0, like1 = 0, like2 = 0, sad1 = 0, sad2 = 0, wow1 = 0, wow2 = 0, haha1 = 0,
                haha2 = 0,
                angry1 = 0, angry2 = 0, love1 = 0, love2 = 0, shared1 = 0, shared2 = 0, total1 = 0, total2 = 0,
                tipoList1 = '', tipoList2 = '';

            comments1 = parseFloat(pagina1.comments);
            comments2 = parseFloat(pagina2.comments);
            (pagina1.reacciones).forEach(function (reacciones, index) {

                like1 += parseFloat(reacciones.likes);
                sad1 += parseFloat(reacciones.sad);
                wow1 += parseFloat(reacciones.wow);
                haha1 += parseFloat(reacciones.haha);
                angry1 += parseFloat(reacciones.angry);
                love1 += parseFloat(reacciones.love);
                shared1 += parseFloat(reacciones.shared);
            });
            (pagina2.reacciones).forEach(function (reacciones2, index) {
                like2 += parseFloat(reacciones2.likes);
                sad2 += parseFloat(reacciones2.sad);
                wow2 += parseFloat(reacciones2.wow);
                haha2 += parseFloat(reacciones2.haha);
                angry2 += parseFloat(reacciones2.angry);
                love2 += parseFloat(reacciones2.love);
                shared2 += parseFloat(reacciones2.shared);
            });
            total1 = like1 + sad1 + wow1 + haha1 + angry1 + love1 + shared1;
            total2 = like2 + sad2 + wow2 + haha2 + angry2 + love2 + shared2;

            tipoList1 += '<h3>' + name1 + '</h3>';
            tipoList1 += '<table class="table table-striped table-hover">';
            tipoList1 += '<tr>';
            tipoList1 += '<th>Tipo</th>';
            tipoList1 += '<th>Total</th>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Like</td>';
            tipoList1 += '<td>' + like1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Love</td>';
            tipoList1 += '<td>' + love1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Sad</td>';
            tipoList1 += '<td>' + sad1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>haha</td>';
            tipoList1 += '<td>' + haha1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Wow</td>';
            tipoList1 += '<td>' + wow1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Angry</td>';
            tipoList1 += '<td>' + angry1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Shared</td>';
            tipoList1 += '<td>' + shared1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Comments</td>';
            tipoList1 += '<td>' + comments1 + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '<tr>';
            tipoList1 += '<td>Total</td>';
            tipoList1 += '<td>' + (total1 + comments1) + '</td>';
            tipoList1 += '</tr>';
            tipoList1 += '</table>';
            $('#tabla-engagement1').html(tipoList1);

            tipoList2 += '<h3>' + name2 + '</h3>';
            tipoList2 += '<table class="table table-striped table-hover">';
            tipoList2 += '<tr>';
            tipoList2 += '<th>Tipo</th>';
            tipoList2 += '<th>Total</th>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Like</td>';
            tipoList2 += '<td>' + like2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Love</td>';
            tipoList2 += '<td>' + love2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Sad</td>';
            tipoList2 += '<td>' + sad2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>haha</td>';
            tipoList2 += '<td>' + haha2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Wow</td>';
            tipoList2 += '<td>' + wow2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Angry</td>';
            tipoList2 += '<td>' + angry2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Shared</td>';
            tipoList2 += '<td>' + shared2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Comments</td>';
            tipoList2 += '<td>' + comments2 + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '<tr>';
            tipoList2 += '<td>Total</td>';
            tipoList2 += '<td>' + (total2 + comments2) + '</td>';
            tipoList2 += '</tr>';
            tipoList2 += '</table>';
            $('#tabla-engagement2').html(tipoList2);

            var chart = Highcharts.chart('engagement-chart', {

                chart: {
                    type: 'column'
                },

                title: {
                    text: 'Interacción de los usuarios'
                },

                subtitle: {
                    text: 'Último mes'
                },

                legend: {
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical'
                },

                xAxis: {
                    categories: [''],
                    labels: {
                        x: -10
                    }
                },

                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'Cantidad'
                    }
                },

                series: [{
                    name: name1,
                    data: [parseFloat(total1 + comments1)]
                }, {
                    name: name2,
                    data: [parseFloat(total2 + comments2)]
                }],

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            yAxis: {
                                labels: {
                                    align: 'left',
                                    x: 0,
                                    y: -5
                                },
                                title: {
                                    text: null
                                }
                            },
                            subtitle: {
                                text: null
                            },
                            credits: {
                                enabled: false
                            }
                        }
                    }]
                }
            });


            getTop();
        }


        /******************************************************** Publicaciones *****************************************************************/
        function getTop() {
            let start = document.getElementById("start").value;
            end = document.getElementById("end").value;
            user = document.getElementById("user").value;
            datos = {start, end, user, pagina1, pagina2};

            axios.post('{{ route('Comparator.TopPost') }}', datos).then(response => {
                armar(response.data);
            });
        }

        function armar(data) {
            var name1 = $('#span-pagina1').text();
            var name2 = $('#span-pagina2').text();
            var page1 = data.pages[pagina1].top;
            var page2 = data.pages[pagina2].top;
            var titulo = '<h2>Top de Publicaciones</h2>';
            $('#titulo-top').html(titulo);
            var postList1 = '<h3>' + name1 + '</h3>';
            var postList2 = '<h3>' + name2 + '</h3>';
            var x = 0, y = 0;
            $('#section7').removeClass('ocultar');

            for (var key in page1) {
                x++;
                postList1 = '<div id="' + page1[key].posteo + '" class="col-xs-12">' +
                    '<div id="contenido-' + page1[key].posteo + '"></div>' +
                    '<div class="col-xs-offset-1"><strong>Total de interacciones: ' + page1[key].count + '</strong></div>' +
                    '</div>';
                getDatos(page1[key].posteo);
                $('#top-' + x + '-1').html(postList1);
            }


            for (var key in page2) {
                y++;
                postList2 = '<div id="' + page2[key].posteo + '" class="col-xs-12">' +
                    '<div id="contenido-' + page2[key].posteo + '"></div>' +
                    '<div class="col-xs-offset-1"><strong>Total de interacciones: ' + page2[key].count + '</strong></div>' +
                    '</div>';
                getDatos(page2[key].posteo);
                $('#top-' + y + '-2').html(postList2);
            }
        }

        function getDatos(post_id) {
            let user = document.getElementById("user").value;
            datos = {user, post_id};

            axios.post('{{ route('Comparator.getDatos',$company ?? '') }}', datos).then(response => {
                post(response.data);
            });
        }

        function post(posteos) {
            var post = posteos[0];
            var postLists = "";
            postLists += '<div class="col-sm-12 single-post">';
            postLists += '<h3>' + post.page_name + '</h3>';
            postLists += '<div class="single-post-container">';
            postLists += '<div class="post-wrapper">'; // start post wrapper
            postLists += '<p class="post-meta">';

            var created_time = moment(post.created_time),
                formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');

            postLists += '<b>' + post.created_time + '</b>';
            postLists += '</p>';

            postLists += '<p class="post-content"><div  style=" width=200px ; padding: 20px 12px 12px 12px;">';
            postLists += post.content;
            postLists += '<div id="ad-' + post.post_id + '"></div>';
            postLists += '</div></p>';

            postLists += '</div>'; // end post wrapper
            postLists += '</div>'; // end single post container
            postLists += '</div>'; // end col6
            $('#contenido-' + post.post_id).html(postLists);
            generateAdjunto(post);
        }

        function generateAdjunto(data) {
            var AdjuntoList = '';
            var post_id = data.post_id;
            if (data.picture) {
                var imagen = data.picture;
                imagen = imagen.replace('AND', '&');
                imagen = imagen.replace('AND', '&');
                imagen = imagen.replace('AND', '&');
                imagen = imagen.replace('AND', '&');
                imagen = imagen.replace('AND', '&');
                AdjuntoList += '<img src="' + imagen + '" style="width:100%; height: auto; margin-top: 10px; ">'
                if (data.url) {
                    var url = data.url;
                    var title = data.title;
                    url = url.replace('AND', '&');
                    url = url.replace('AND', '&');
                    url = url.replace('AND', '&');
                    url = url.replace('AND', '&');
                    url = url.replace('AND', '&');
                    AdjuntoList += '<a href="' + url + '">' + title + '</a>'
                }
            }
            if (data.video) {
                var video = data.video;
                video = video.replace('AND', '&');
                video = video.replace('AND', '&');
                video = video.replace('AND', '&');
                video = video.replace('AND', '&');
                video = video.replace('AND', '&');
                AdjuntoList += '<video controls src="' + video + '"style="width:100%;  height: auto; margin-top: 10px; "></video>'
            }
            $('#ad-' + post_id).html(AdjuntoList);
        }
    </script>
@endsection
