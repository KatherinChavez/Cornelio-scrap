@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">
                                <h2><span><i class="fab fa-facebook-square"></i></span> Extracción de información
                                    de publicación de Facebook</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <div id="cancelar" hidden>
                            <a class="btn btn-round btn-outline-danger float-right" onclick="Cancelar()">Cancelar</a>
                        </div>

                        {{-- OPCION PARA INGRESAR LA URL DE LA PUBLICACIÓN DE FACEBOOK--}}
                        <div id="scrap" hidden>
                            <div class="form-group">
                                {{ Form::label('page_id','Url de la publicación *') }}
                                {{ Form::text('page_id','',['class' => 'form-control', 'maxlength' => '80','onchange'=>'ObtenerId()']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('page_name','Nombre de página') }}
                                {{ Form::text('page_name',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('post_id','Identificador de la página') }}
                                {{ Form::text('post_id',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                            </div>
                        </div>

                        {{--BOTON PARA HACER SCRAP--}}
                        <div id="opcionScrap" hidden>
                            <button type="button" id="actualizar" class="btn btn-sm btn-round btn-block btn-primary"
                                    onclick="Opcion()">Consultar página de contenido
                            </button>
                        </div>


                        {{--MUESTRA LOS DATOS--}}
                        <div id="lista" hidden>
                            <br>
                            <h4>Datos obtenidos de la publicación</h4>
                            <div class="form-group">
                                {{--------------------------- Publicacion -------------------------------}}
                                <div class="posts" id = "posts"></div>

                                {{-------------------------- Nube de palabra --------------------------}}
                                <div class="card-body">
                                    <div class="col-sm-12" id="cloudComment"></div>
                                </div>

                                {{-------------------- Graficas de los temas --------------------}}

                                <div class="col-md-12 row justify-content-center">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                        <i class="fas fa-chart-bar"></i> Sentimiento de Conversaciones
                                    </h5>
                                    <div class="col-md-12 row justify-content-center" id="estadistica"></div>
                                </div>

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
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script>
        document.getElementById('nav-scraps').className += ' active';
        document.getElementById("scrap").hidden = false;
        document.getElementById("cancelar").hidden = true;
        document.getElementById("lista").hidden = true;
        let postLimit = 100,
            user = "{{ Auth::user()->id }}",
            pageName = "",
            pageId="",
            postId="",
            pageAccessToken = "";

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                let accessToken = response.authResponse.accessToken;
                isLogedIn(accessToken);
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        function isLogedIn(token) {
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

        /********************************************* Obtiene page ******************************************************/

        function ObtenerId() {
            let page = document.getElementById("page_id").value;
            let str = page;
            let res = '';


            if(str.indexOf("/posts/") !== -1){
                res = str.split("/posts/");
            }
            else if(str.indexOf("/videos/") !== -1){
                res = str.split("/videos/");
            }
            else{
                swal("Error!", "Lo sentimos! No podemos encontrar la publicación ingresada", "error");
                return 'final';
            }

            let page_url = res[0];
            let post_id = res[1];
            ObtenerNombre(page_url, post_id);
        }

        function ObtenerNombre(page, post) {
            FB.api(
                '/' + page + '',
                'GET',
                {"fields": "name,category", "access_token": pageAccessToken},
                function (response) {
                    if (response.error) {
                        swal("Sin datos!", "Lamentamos informarle que en estos momentos no se puede obtener información de la publicación!", "warning");
                    } else {
                        pageName = response.name;
                        pageId = response.id;
                        postId = pageId+'_'+post;

                        if (response.error) {
                            return false;
                        }

                        document.getElementById("page_name").value = pageName;
                        document.getElementById("page_id").value = pageId;
                        document.getElementById("post_id").value = postId;
                        document.getElementById("opcionScrap").hidden = false;
                    }
                }
            );
        }

        /********************************************** Opcion ********************************************************/
        function Opcion() {
            let data = {pageName, pageId, postId};
            showProcessing();
            axios.post('{{route('scrapsPost.getPost')}}', data).then(response=>{
                if(response.data != 500){
                    document.getElementById("lista").hidden = false;
                    document.getElementById("scrap").hidden = true;
                    document.getElementById("opcionScrap").hidden = true;
                    document.getElementById("cancelar").hidden = false;
                    hideProcessing();
                    selectPublication(response.data);
                    wordClassification(postId);
                    getCloud(postId);
                    getReactionsPost(response.data.reactions);
                    getComments(response.data.comments, postId);
                }
                else{
                    hideProcessing();
                    swal('error', 'No se puedo realizar la extracción de datos');
                }
            }).catch(error=>{
                hideProcessing();
                swal('Ops', 'Se ha encontrado problema','warning');
            });
        }

        /********************************************** Publicacion ********************************************************/

        function selectPublication(datos) {
            var post = datos;
            var postList = "";
            try {
                postList += `<div class="row justify-content-center">
                                <div class="col-md-8">
                                   <h2 style="color:#0f3760;">`+ post.page_name + `</h2>`;

                if (post.attachment) {
                    var attachements = post.attachment;
                    if (attachements.type === "photo" || attachements.type === 'cover_photo' || attachements.type === 'share') {
                        postList += '<img class="card-img-top img-fluid w-100" src="' + attachements.picture + '" alt="">';
                    }
                    if (attachements.type === "video") {
                        postList += '<video width="320" height="240" controls><source src="' + attachements.url + '" type="video/mp4"></video>';
                    }
                }

                if (post.content) {
                    message = post.content;
                }
                var created_time = moment(post.created_time),
                    formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                postList += '<div class="card-body">' +
                    '<p class="card-title mb-1 small">' + formated_created_time.substring(0, 10) + ' ' + formated_created_time.substring(11, 16) + '</p>' +
                    '<p class="card-text small">' + message + '';

                if (post.attachment) {
                    var attachements = post.attachment;
                    if (attachements.type === 'share') {
                        postList += '<br><a href="' + attachements.url + '" target="_blank">' + attachements.title + '</a>';
                    }
                }
                postList += `</p></div><hr class="my-0">
                        <div class="card-body py-2 small" id="reactions-` + post.post_id + `"></div><hr class="my-0">
                        <div class="card-body" id="comments-` + post.post_id + `"></div>
                    </div>
                </div>`;
                getReactionsPost(post.reactions);
                //getComments(post.comments);
                postList += '';
                $('#posts').append(postList);
            }
            catch (ex) {
                console.error('outer', ex.message);
            }
        }

        /********************************************** Reaccion ********************************************************/

        function getReactionsPost(post) {
            var div = post.post_id,
                like = post.likes,
                love = post.love,
                wow = post.wow,
                haha = post.haha,
                sad = post.sad,
                angry = post.angry,
                shares = '',
                reactionsList = '';

            if (post.shares) {
                shares = post.shares;
            } else {
                shares = 0;
            }

            reactionsList +=
                `<div class="mr-2 d-inline-block">
                    <img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px; vertical-align: middle">
                    <label id="" for="Like">` + like + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png" alt="Love" title="Love" style="width: 20px; vertical-align: middle">
                    <label id="" for="Love">` + love + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">
                    <label id="" for="Hahaha">` + haha + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">
                    <label id="" for="Wow">` + wow + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">
                    <label id="" for="Sad">` + sad + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">
                    <label id="" for="Angry">` + angry + `</label>
                </div>

                <div class="mr-2 d-inline-block">
                    <img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">
                    <label id="" for="Shared">` + shares + `</label>
                </div>`;

            //$("#reactions-" + div).append(reactionsList);
            $("#reactions-" + div).html(reactionsList);
        }

        /********************************************** Comentario ********************************************************/

        function getComments(post, postId) {
            var comments = post,
                name_from = '',
                id_from = '',
                commentsList = '';

            if (comments) {
                comments.forEach(function (comment, index) {

                        id_from = comment.author_id;
                        name_from = "Ver FB";

                    var created_time = moment(comment.created_time),
                        formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                    commentsList += `<div class="received_withd_msg">
                                        <p class="card-text small"><a href="https://business.facebook.com/`+ comment.comment_id +` " target="_blank"><strong> Ver Facebook </strong></a>
                                        <br>
                                        `+ comment.comment + `
                                        </p>
                                    </div>`;
                });
            }
            commentsList += '';
            $('#comments-' + postId).append(commentsList);
        }

        /******************************************** Grafica de sentimiento ******************************************/
        function wordClassification(post) {
            let datos = {post:post};
            axios.post('{{route('scrapsPost.wordClassification')}}', datos).then(response=>{
                let reacciones = response.data.Tema;
                reacciones.forEach(function (reaccion, index) {
                    Highcharts.chart('estadistica', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Sentimiento de conversación <br>de la publicación',

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
                                ['Positivos ' , reaccion.positivo],
                                ['Negativos ' , reaccion.negativo],
                                ['Neutrales ' , reaccion.neutral],
                            ]
                        }]
                    });
                });
            });
        }

        /******************************************** Nube de palabra *************************************************/
        function getCloud(post_id) {
            let datos = {postId : post_id};
            showProcessing();
            axios.post('{{ route('ClassifyCategory.comment') }}', datos).then(response => {
                if(response.data.length>0){
                    hideProcessing();
                    procesarComentariosPost(response.data);
                }else{
                    $('#nube').html('<h3>No hay suficientes datos!</h3>');
                    hideProcessing();
                }
            });
        }

        function procesarComentariosPost(data) {
            var comentariosPost="";
            var palabras=[' de ','De ',' que ','Que ','Qu ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
                ' el ',' por ','Por ',' como ',' cómo ','Y ',' y ',' un ',' una ',' uno ',' mas ', ' más ', '?', '¡',
                ' se ',' no ','No ',' si ','Si ', 'A ',' a ',' en ',' es ','está ',' eso ',' esos ',' pero ',
                'Image/Emoji',' para ',' las ',' su ',' sus ',' esa ','!',' ser ',' sin ',' ya ',' los ',' te ',
                ' me ','Me ',' ja ',' jaja ',' je ',' jeje ',' les ',' la ',' le ',' son ','DE ','QUE ','LA ',' con ',
                'Pero ',' este ',' esta ',' hace ',' poco ',' toda ','Toda ','Todo ',' todo ',' bien ','Bien ',' estos ',
                ' estas ','Estos ','Estas ','Está ',' esto ',' solo ',' cada ',' todos ','Todos ',' nada ',' ellos '
            ];
            var i;
            data.forEach( function(post, index) {
                var texto=post.comment;
                palabras.forEach(function (palabra, index2) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ")
                    }
                });
                comentariosPost+=" "+texto;
            });
            wordCloudPost(comentariosPost);
        }

        function wordCloudPost(comentario) {
            var text = comentario;
            var lines = text.split(/[,\. ]+/g),
                data = Highcharts.reduce(lines, function (arr, word) {
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

            data=data.sort(function (a, b){
                return (b.weight - a.weight)
            });

            var filtrado=[];
            var temporal;
            var hasta=data.length;
            if(hasta>60){
                hasta=60;
            }
            for(i=0;i<hasta;i++){
                var x= data[i].name;
                if(x.length>3){
                    temporal=data[i];
                    filtrado.push(temporal);
                }
            }

            Highcharts.chart('cloudComment', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación'
                }

            });
            $('#reporteProcesando').addClass('procesar');
            $('#reporteContenido').removeClass('procesar');

        }

        /********************************************** Cancelar ********************************************************/
        function Cancelar() {
            document.getElementById("scrap").hidden = false;
            document.getElementById("lista").hidden = true;
            document.getElementById("cancelar").hidden = true;

            document.getElementById("page_name").value = '';
            document.getElementById("page_id").value = '';
            document.getElementById("post_id").value = '';

            $('#posts').html('');
        }
        /********************************************** Carga ********************************************************/

        function showProcessing() {
            $("body").append(
                '<div id="overlay-processing" style="background: #F0F0F0; height: 100%; width: 100%; opacity: .9; padding-top: 10%; position: fixed; text-align: center; top: 0;z-index: 2147483647;">' +
                '<h2 style="color: #333333" id="estado">Leyendo comentarios</h2>' +
                '<i class="fa fa-cog fa-spin fa-3x fa-fw" aria-hidden="true"></i>' +
                '<span class="sr-only">Processing</span></div>'
            );
        }

        function hideProcessing() {
            $('body').find('#overlay-processing').remove();
        }


    </script>
@endsection
