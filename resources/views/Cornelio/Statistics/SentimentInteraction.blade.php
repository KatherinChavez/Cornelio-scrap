@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="row justify-content">
            <div class="col-lg-12">
                <div class="card animated bounceIn">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold"><span><i class="fas fa-chart-pie"></i> Sentimientos </span></h3>
                        </div>  
                    </div>
                    <div class="card-body">
                        <div class="form-group form-control" id="queryTopics">
                            <p class="h5">
                                Aquí puede consultar los sentimientos generados de las publicaciones y comentarios clasificados de acuerdo al tema deseado</p>

                            <div class="form-row align-items-center">
                                <div class="col-sm-6 my-4">
                                    <label class="sr-only" for="start">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                                </div>
                                <div class="col-sm-6 my-4">
                                    <label class="sr-only" for="end">Fecha Final</label>
                                    <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                                </div>
                                <div class="col-sm-12 my-3" >
                                    {{ Form::label('id','Seleccione un contenido *') }}<br>
                                    {{ Form::select('id',$topics,null,['class'=>'form-control','placeholder'=>'Seleccione un contenido','required']) }}
                                </div>
                            </div>
                            <div class="input-group form-group">
                                <button class="btn btn-block btn-primary btn-round" onclick="consultarSentimiento()">
                                    Consultar datos
                                </button>
                            </div>
                        </div>

                            <div class="card-body" id="getTopics">
                                <div class="col-md-12 row justify-content-center" id="chart-detail"></div>

                                <button class="btn btn-block btn-danger btn-round" onclick="cancelar()">
                                        Cancelar
                                    </button>
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
    <script src="https://code.highcharts.com/modules/networkgraph.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>

        document.getElementById("queryTopics").hidden = false;
        document.getElementById("getTopics").hidden = true;
        function consultarSentimiento() {
            let start = document.getElementById('start').value,
                end = document.getElementById('end').value,
                topics = document.getElementById('id').value,
                data = {start, end, topics};

            if(topics){
                axios.post('{{ route('Statistics.getTopics') }}', data).then(response => {
                    $('#chart-detail').append('<h2 class="col-md-12 mt-5 b-b1 pb-2 mb-4" style="text-align:center"><b>Datos del tema '+response.data.tema+'</b></h2>');
                    let comments = response.data.Comment;
                    let posts = response.data.Post;
                    if(posts.length>0){
                        chartSentimentPost(posts);
                    }
                    if(comments.length>0){
                        chartSentimentComment(comments);
                    }

                    document.getElementById("queryTopics").hidden = true;
                    document.getElementById("getTopics").hidden = false;
                }).catch((error) => {
                    swal('Ops', 'No es posible consultar los datos del tema seleccionada','warning');
                })
            }else {
                swal('Ops', 'Seleccione un tema en especifico','warning');
            }

        }

        function chartSentimentPost(posts) {
            posts.forEach(function (post, index) {
                console.log(post);
                $('#chart-detail').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="post_sentiment"></div>');
                Highcharts.chart("post_sentiment", {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },

                    title: {
                        text: '<b>Sentimientos generados de las publicaciones </b>',

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

                    colors: ['#317f43', '#CB3234', '#ffff00', '#ff9933'],
                    series: [{
                        type: 'pie',
                        name: 'Porcentaje de Reacciones',
                        innerSize: '50%',
                        data: [
                            ['Positivos ' , post.positivo],
                            ['Negativos ' , post.negativo],
                            ['Neutrales ' , post.neutral],
                            ['Mixto '     , post.mixto],
                        ]
                    }]
                });
            });
        }
        function chartSentimentComment(comments) {
            comments.forEach(function (comment, index) {
                $('#chart-detail').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="comment_sentiment"></div>');
                Highcharts.chart("comment_sentiment", {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },

                    title: {
                        text: '<b>Sentimientos generados de los comentarios</b>',

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

                    colors: ['#317f43', '#CB3234', '#ffff00', '#ff9933'],
                    series: [{
                        type: 'pie',
                        name: 'Porcentaje de Reacciones',
                        innerSize: '50%',
                        data: [
                            ['Positivos ' , comment.positivo],
                            ['Negativos ' , comment.negativo],
                            ['Neutrales ' , comment.neutral],
                            ['Mixto '     , comment.mixto],
                        ]
                    }]



                });
            });
        }
        function cancelar() {
            $('#chart-detail').html('');
            document.getElementById("queryTopics").hidden = false;
            document.getElementById("getTopics").hidden = true;
        }

    </script>
@endsection
