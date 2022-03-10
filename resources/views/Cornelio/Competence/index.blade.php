@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header" align="center">
                        <h1> Competidor</h1>
                    </div>

                    <div class="card-body ">
                        <input type="hidden" name="company_id" id="company_id" value="{{$company_id}}">

                        {{--OPCION PARA AGREGAR NUEVA PAGINA COMO COMPENTENCIA--}}
                        <div class="card-body table-responsive">
                            <div id="agregar" hidden>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#ModalAdd">Agregar</button>
                            </div>
                        </div>

                        {{-- MUESTRA LAS PAGINAS QUE SE ENCUENTRA COMO COMPETENCIA--}}
                        <div id="lista" hidden>
                            @if(@isset($pagePrincipal))
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            Página principal
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Id de la página</th>
                                                <th>Nombre</th>
                                                <th>Contenido asignado</th>
                                                {{--<th>Acciones</th>--}}
                                                <th colspan="4">&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($pagePrincipal as $page)
                                                <tr>
                                                    <td>{{ $page->id }}</td>
                                                    <td>{{ $page->page_id }}</td>
                                                    <td>{{ $page->page_name }}</td>
                                                    <td>{{ $page->categories->name }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if(@isset($pages))
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            Página competidoras
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Id de la página</th>
                                                <th>Nombre</th>
                                                <th>Contenido asignado</th>
                                                <th>Acciones</th>
                                                <th colspan="4">&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($pages as $page)
                                                <tr>
                                                    <td>{{ $page->id }}</td>
                                                    <td>{{ $page->page_id }}</td>
                                                    <td>{{ $page->page_name }}</td>
                                                    <td>{{ $page->categories->name }}</td>
                                                    <td>
                                                        <div class="list-group-item-figure">
                                                            <button type="button" onclick="showCompetence({{ $page->page_id }})" data-user="{{ $page->page_id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                                <i class="far fa-eye"></i>
                                                            </button>
                                                            <a onclick="deleteCompetence(event)" href="/Competence/delete/{{$page->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                                <i class="icon-close"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if(@isset($pageGeneral))
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            Páginas general
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Id de la página</th>
                                                <th>Nombre</th>
                                                <th>Contenido asignado</th>
                                                <th>Acciones</th>
                                                <th colspan="4">&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($pageGeneral as $page)
                                                <tr>
                                                    <td>{{ $page->id }}</td>
                                                    <td>{{ $page->page_id }}</td>
                                                    <td>{{ $page->page_name }}</td>
                                                    <td>{{ $page->categories->name }}</td>
                                                    <td>
                                                        <div class="list-group-item-figure">
                                                            <button type="button" onclick="showCompetence({{ $page->page_id }})" data-user="{{ $page->page_id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                                <i class="far fa-eye"></i>
                                                            </button>
                                                            <a onclick="deleteCompetence(event)" href="/Competence/delete/{{$page->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                                <i class="icon-close"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{--MUESTRA LA TABLA DE DATOS --}}
                        <div id="cancelar" hidden>
                            <a class="btn btn-round btn-outline-danger float-right" onclick="Cancelar()">Atras</a>
                        </div>

                        {{--MUESTRA LOS DATOS--}}
                        <div id="data" hidden>
                            <br>
                            <h4>Datos obtenidos</h4>
                            <div class="form-group">
                                {{-------------------------- Informacion de las páginas ------------------------------}}

                                <div class="card mx-auto">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                        Información acerca de la página
                                    </h5>
                                    <div class="row justify-content-center" id="dataL"></div>
                                </div>

                                {{--------------------------- Publicacion --------------------------------------------}}

                                <div class="card mx-auto">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                        Top 10 de publicaciones con mayor interacción
                                    </h5>
                                    <div class="row justify-content-center id=" id="posts"></div>
                                </div>

                                {{-------------------------- Nube de palabra -----------------------------------------}}

                                <div class="card mx-auto">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                        <i class="fas fa-cloud"></i> Nube de palabra
                                    </h5>
                                    <div class="col-lg-12 row justify-content-center">
                                        <div class="col-lg-6" id="cloudPage"></div>
                                        <div class="col-lg-6" id="cloudPagePrincipal"></div>
                                    </div>

                                </div>

                                {{-------------------------- Pie de sentimiento de los comentarios -------------------}}

                                <div class="card mx-auto">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold " align="center">
                                        <i class="fas fa-chart-bar"></i> Sentimiento de las Conversaciones
                                    </h5>
                                    <div class="col-md-12 row justify-content-center" id="feeling"></div>
                                </div>

                                {{-------------------------- Grafica de las publicaciones ----------------------------}}

                                <div class="card mx-auto">
                                    <div class="card-header">
                                        <h4>
                                            <i class="fas fa-chart-line"></i> Estadística de las publicaciones
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="chartPost" class="container">
                                        </div>
                                    </div>
                                </div>

                                {{-------------------------- Grafica de los comentarios ------------------------------}}

                                <div class="card mx-auto">
                                    <div class="card-header">
                                        <h4>
                                            <i class="fas fa-chart-line"></i> Estadística de los comentarios
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="chartComments" class="container">
                                        </div>
                                    </div>
                                </div>

                                {{------------------------------------------------------------------------------------}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL CREAR ---------------------------------------------------}}

    <div class="modal fade" id="ModalAdd"  tabindex="-1" role="dialog" aria-labelledby="ModalAdd" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar página como competencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group col-sm-12 my-1 form-group">
                            {{ Form::label('page','Seleccione una opción*') }}
                            {{ Form::select('page',$selectPage,null,['class'=>'form-control','placeholder'=>'Seleccione una opción','required']) }}
                            <p class="text-danger">{{ $errors->first('description')}}</p>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_guardar" class="btn btn-primary">Agregar</button>
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
        document.getElementById('btn_guardar').addEventListener('click',ModalAdd);

        document.getElementById("lista").hidden = false;
        document.getElementById("agregar").hidden = false;
        document.getElementById("data").hidden = true;
        document.getElementById("cancelar").hidden = true;

        function ModalAdd() {
            $("#ModalAdd").modal('toggle');

            let company_id = document.getElementById('company_id').value,
                page = document.getElementById('page').value
                data = {company_id, page}    ;
            if( page != ''){
                axios.post('{{ route('Competence.store') }}', data).then(response => {
                    if(response.data == '200'){
                        window.location = "{{ route('Competence.index') }}";
                    }
                    else{
                        swal('Ops', 'No es posible asignar la página como competencia','warning');
                    }
                }).catch(error=>{
                    swal('Ops', 'No es posible asignar la página como competencia','warning');
                });
            }
            else{
                swal('Ops', 'No es posible agregar la página como competencia','error');
            }

        }

        function showCompetence(id) {
            document.getElementById("lista").hidden = true;
            document.getElementById("agregar").hidden = true;
            document.getElementById("data").hidden = false;
            document.getElementById("cancelar").hidden = false;

            getInformation(id);
            getTopPost(id);
            getDatosPage(id);
            getDatosPagePrincipal();
            getPosts(id);
            getComments(id);
            getTema(id);
        }

        function deleteCompetence(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡La página se desclasificará como competencia!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        swal("Exito! Se ha eliminado de forma exitosa!", {
                            icon: "success",
                        });
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha eliminado!", "info");
                    }
                });
        }

        function Cancelar() {
            document.getElementById("lista").hidden = false;
            document.getElementById("agregar").hidden = false;
            document.getElementById("data").hidden = true;
            document.getElementById("cancelar").hidden = true;

            $('#posts').html('');
            $('#cloudPage').html('');
            $('#cloudPagePrincipal').html('');
            $('#chartPost').html('');
            $('#chartComments').html('');
            $('#feeling').html('');
        }

        /*********************************** NUBE DE PALABRAS DE LA PAGINA SELECCIONADA *******************************/

        function getDatosPage(page) {
            let datos = {page_id:page};
            $('#modalPage').modal();
            axios.post('{{ route('get.cloudWord') }}', datos).then(response => {
                if(response.data.length>0){
                    $('#cloudPage').html('En un momento');
                    //$('#cloud').append('');
                    procesarComentariosPage(response.data);
                }else{
                    $('#cloudPage').html('<h3 align="center">No hay suficientes datos!</h3>');
                }

            });

        }

        function procesarComentariosPage(data) {
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas ', '.', ' .com ', '/',
                '?', ' ref ', '=', ' share ', '¡', '🤦🏻‍♀', '🥱','🤬', '👏', '✌', '😱','😱', '😡', '💩','🖤','❤','😂','💪', '🤣',
                '🏆','🤫','😁','⚫','🔴','😭','🙄', '🤷🏼‍♀', '🤷‍♂', '🤦🏻‍♂', '🤔','🤡', '"', '&quot', ' com ', 'https:', 'www'

            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                var texto=posts.comment;
                palabras.forEach(function (palabra, index3) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ");
                    }
                });
                comentarios += texto;

            });
            wordCloudCommentPage(comentarios)
        }

        function wordCloudCommentPage(comentario) {
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
            //console.log(data);

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

            Highcharts.chart('cloudPage', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación de la página seleccionada'
                },
                subtitle: {
                    text: 'Datos generados en las últimas 24 horas'
                },

            });
        }

        /*********************************** NUBE DE PALABRAS DE LA PAGINA PRINCIPAL **********************************/

        function getDatosPagePrincipal(page) {
            let datos = {page_id:page};
            $('#modalPage').modal();
            axios.post('{{ route('Competence.cloudWordPage') }}', datos).then(response => {
                if(response.data.length>0){
                    procesarComentarios(response.data);
                }else{
                    $('#cloudPagePrincipal').html('<h3 align="center">No hay suficientes datos!</h3>');
                }

            });

        }

        function procesarComentarios(data) {
            var palabras=[' de ','De ',' que ','Que ',' q ','Q ',' qué',' porque ',' lo ',' del ',' la ','El ',
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
                ' estan ', ' La ', ' la ', ' siempre ', ' Los ', ' los ', ' las ', ' Las ', ' esas ', '.', ' .com ', '/',
                '?', ' ref ', '=', ' share ', '¡', '🤦🏻‍♀', '🥱','🤬', '👏', '✌', '😱','😱', '😡', '💩','🖤','❤','😂','💪', '🤣',
                '🏆','🤫','😁','⚫','🔴','😭','🙄', '🤷🏼‍♀', '🤷‍♂', '🤦🏻‍♂', '🤔','🤡', '"', '&quot', ' com ', 'https:', 'www'

            ];
            var i;
            var comentarios = '';
            data.forEach( function(posts, index) {
                var texto=posts.comment;
                palabras.forEach(function (palabra, index3) {
                    i=0;
                    for(;i!=-1;){
                        i=texto.indexOf(palabra);
                        texto=texto.replace(palabra, " ");
                    }
                });
                comentarios += texto;

            });
            wordCloudComment(comentarios)
        }

        function wordCloudComment(comentario) {
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
            //console.log(data);

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

            Highcharts.chart('cloudPagePrincipal', {
                series: [{
                    type: 'wordcloud',
                    data: filtrado,
                    name: 'Menciones'
                }],
                title: {
                    text: 'Tema de Conversación de la página principal'
                },
                subtitle: {
                    text: 'Datos generados en las últimas 24 horas'
                },

            });
        }

        /*********************************** GRAFICA DE LA CANTIDAD DE PUBLICACIONES **********************************/
        function getPosts(page) {
            let datos = {page_id:page};

            axios.post('{{ route('Competence.StaticsPost') }}', datos).then(response => {
                var resultado = response.data;
                if( resultado.status == "success") {
                    chartPosts(response.data);
                } else {
//                    swal("Sin datos!", "No se encuentra datos!", "warning");
                }
            });
        }

        function chartPosts(data) {
            var post = data.Page;
            Highcharts.chart('chartPost', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Publicaciones generadas'
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
                series: post,

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

        /*********************************** GRAFICA DE LA CANTIDAD DE COMENTARIOS ************************************/
        function getComments(page) {
            let datos = {page_id:page};

            axios.post('{{ route('Competence.StaticsComments') }}', datos).then(response => {
                var resultado = response.data;
                if( resultado.status == "success") {
                    chartComment(response.data);
                } else {
                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
                }
            });
        }

        function chartComment(data) {
            var comments = data.Comments;
            Highcharts.chart('chartComments', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Comentarios genedaros'
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
                series: comments,

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

        /*********************************** TOP 10 CON PUBLICACIONES CON MAYOR INTERACCION ***************************/
        function getTopPost(page) {
            let datos = {page_id:page};
            axios.post('{{ route('Competence.TopPagePost') }}', datos).then(response => {
                if (response.data) {
                    TopPost(response.data);
                } else {
                    return 'No se puede mostrar los datos';
                }
            });
        }

        function TopPost(data) {
            let page = data,
                postList = '',
                posts = '';
            page.forEach(function (info, index) {
                posts = info.data;
                console.log(info.name);
                postList +=  `</br><div class="col-sm-12"><h3>`+info.name+`</h3></div></br>`;
                posts.forEach(function (post, index) {
                    //INCIO
                    postList += `<div class="col-sm-4" style="overflow: auto">
                                    <div class="card mb-3" style=" height: 500px;overflow: auto">
                                        <h5 class="card-title mb-1" style="color:#0f3760;">`+ post.name +`</h5>
                                        <p class="card-text small mb-2">`+post.content+`</p>`;

                    //FOTO, TITULO Y URL
                    if(post.picture){
                        postList += `<img src="`+post.picture+`" style=" width:100%; vertical-align: middle">`;
                    }

                    if(post.url){
                        postList += `<a href="`+post.url+`">`+post.title+`</a>`;
                    }
                    //FECHA DE PUBLICACION Y LA CANTIDAD DE INTERACION
                    postList += `<hr class="my-0">
                                <p class="card-title mb-1 small">`+post.date+`</p>
                                <hr class="my-0">
                                <div class="col-xs-offset-1"><strong>Total de interacciones: `+ post.count +` </strong></div>`;

                    //FINAL
                    postList += `
                                    </div>
                                </div>`;
                });
            });
            $('#posts').append(postList);
        }

        /*********************************** SENTIMIENTOS GENERADOS POR LOS COMENTARIOS *******************************/
        function getTema(page) {
            let datos = {page_id:page};
            axios.post('{{ route('Competence.FeelingPageComments') }}', datos).then(response => {
                if (response.data) {
                    chartTema(response.data);
                } else {
                    return 'No se puede mostrar los datos';
                }
            });
        }

        function chartTema(data) {
            var page = data.Page;
            page.forEach(function (info, index) {
                $('#feeling').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="'+ info.Name+'"></div>');
                Highcharts.chart(info.Name, {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },

                    title: {
                        text: 'Sentimiento de conversación <br>de la página  <b>'+ info.Name + '</b> <br>en las Últimas <b>24 horas</b>',
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
                            ['Positivos ' , info.positivo],
                            ['Negativos ' , info.negativo],
                            ['Neutrales ' , info.neutral],
                        ]
                    }]



                });
            });

        }

        /*********************************** INFORMACION DE LA PAGINA *************************************************/
        function getInformation(page) {
            let datos = {page_id:page};
            axios.post('{{ route('Competence.getInformation') }}', datos).then(response => {
                if (response.data) {
                    InfoPage(response.data);
                } else {
                    return 'No se puede mostrar los datos';
                }
            });
        }

        function InfoPage(data) {
            let dataList = '';
            data.forEach(function (info, index) {
                dataList += `<div class="col-sm-6" style="overflow: auto">
                                <div class="card">
                                    <img src="`+info.picture+`" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">`+info.page_name+`</h5>
                                        <p class="card-text">La página `+info.page_name+` cuenta con una cantidad de `+info.fan_count+` usuarios a los que les gusta la página
                                         y una cantidad total de `+info.talking+` personas que hablan de esta página</p>
                                         </br>
                                         <p class="card-text">Un poco más sobre `+info.page_name+`</p>
                                         <ul>
                                          <li>`+info.about+`</li>
                                          <li>La página se encuentra categorizada como `+info.category+`</li>`;
                        if( info.company_overview != null){
                            dataList += `<li>Descripción general: `+info.company_overview+`</li>`;
                        }
                        if(info.location != '' || info.location != null){
                            dataList += `<li>Se encuentra ubicado en `+info.location+`</li>`;
                        }
                        if(info.phone != null){
                            dataList += `<li>Número de teléfono `+info.phone+`</li>`;
                        }
                        if(info.emails != null){
                            dataList += `<li>Correo electronico `+info.emails+`</li>`;
                        }
                        dataList += `    </ul>
                                        </div>
                                    </div>
                                </div>`;

            });
            $('#dataL').append(dataList);
        }
    </script>
@endsection
