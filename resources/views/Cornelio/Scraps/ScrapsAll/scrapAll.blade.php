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
                                    de páginas de Facebook</h2>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-light btn-round btn-border btn-sm pull-right"
                                        data-toggle="modal"
                                        data-target="#exampleModalLong">
                                    Ayuda
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        @if (count($categories)> 0)
                            <div class="form-group">
                                {{ Form::label('page_id','Id o alias de página *') }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
                                   data-content="Ingrese el id o alias de la página que se encuetra en Facebook">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <div class="input-group mb-3">
                                    {{ Form::text('page_id','',['class' => 'form-control', 'maxlength' => '80','onchange'=>'ObtenerId()']) }}

                                    <div class="input-group-append">
                                        {{--<button class="btn btn-outline-secondary" type="button" title="Mis páginas" onclick="showpages()"><i class="fas fa-swatchbook"></i></button>--}}
                                    </div>
                                </div>
                                <p class="text-danger">{{ $errors->first('page_id')}}</p>
                            </div>
                            <div class="form-group">
                                {{ Form::label('page_name','Nombre de página') }}
                                {{ Form::text('page_name',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                            </div>

                            <div class="form-group">
                                {{ Form::label('limitePost','Cantidad de posteos *',['title'=>'Cantidad de posteos a extraer']) }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
                                   data-content="Ingrese la cantidad de publicaciones que desee almacenar">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <input type="number" class="form-control" id="limite" placeholder="1" min="1" max="20">
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                            <div class="form-group">
                                <!-- Las categoria ahora sera conocido como contenido -->
                                {{ Form::label('categoria','Seleccione el contenido *') }}
                                {{ Form::select('categoria',$categories,null,['class'=>'form-control','placeholder'=>'Seleccione la contenido','required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                        @else
                            Por favor proceda a seleccionar
                        @endif

                        <hr>
                        <div id="lista" hidden>
                            <h4>Elementos a obtener</h4>
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1"
                                               onclick="checkAll(this)">
                                        <label class="custom-control-label" for="customCheck1">Toda la página</label>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck2">
                                        <label class="custom-control-label" for="customCheck2">Posteos</label>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck3">
                                        <label class="custom-control-label" for="customCheck3">Comentarios</label>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck4">
                                        <label class="custom-control-label" for="customCheck4">Reacciones</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <div id="botonE"></div>

                            {{--<div id="botonC" style="padding-left: 38px"></div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="exampleModalLongTitle"> Extracción de información de páginas de Facebook</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>El scrap de página permitirá obtener información general de cualquier
                        tipo de página que se encuentre en Facebook.
                        </br></br>
                        <b>¿Cómo obtener la información de la página ?</b>
                        </br>
                        Será muy sencillo, solo se deberá de ingresar el id, url o alias de la
                        página que deseamos buscar.

                        </br></br>
                        <b>¿Cuál es id o alias de la página de Facebook ?</b>
                        </br>
                        Para conocer el alias o el id de la página nos dirigimos a Facebook y
                        buscamos nuestra página deseada, cabe destacar que en algunas páginas
                        aparecen el alias o el id. 
                        </br></br>
                        <b>Alias de la página</b>
                        </br>
                        <img src="{{ asset('imagen/apps/aliasFacebook.jpg') }}" alt="FaceBook" style="width: 90%">
                        </br></br></br></br>

                        <b>ID de la página</b>
                        </br>
                        <img src="{{ asset('imagen/apps/idFacebook.jpg') }}" alt="FaceBook"
                             style="width: 90%; vertical-align: middle">
                        </br></br></br></br>
                        Al obtener el id o alias para gestionar la página lo podremos ingresar en la casilla solicitada
                        </br>
                        <img src="{{ asset('imagen/apps/ejemploId.jpg') }}" alt="FaceBook"
                             style="width: 90%; vertical-align: middle">
                        </br></br>
                        <b>¿Se puede ingresar la url de la página ?</b>
                        </br>
                        Claro, el sistema permitirá recibir la url de la página y este mostrará el id
                        correspondiente de la página ingresada. 

                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            if ("{{$categories->count()>0}}") {
            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Por favor proceda a crear un contenido'
                }).then((result) => {
                    window.location = "{{ route('Category.index') }}";
                })
            }
        });

        document.getElementById("lista").hidden = true;
        let postLimit = 100,
            user = "{{ Auth::user()->id }}",
            idP = "",
            pageAccessToken = "";

        function showpages() {
            datos = {idP, nombre, categoria};
        }

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
                    console.log(response, response.access_token);
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
            ObtenerNombre(page);
        }

        function ObtenerNombre(page) {
            FB.api(
                '/' + page + '',
                'GET',
                {"fields": "name,category", "access_token": pageAccessToken},
                function (response) {
                    if (response.error) {
                        swal("Sin datos!", "Lamentamos informarle que en estos momentos no se puede obtener información página!", "warning");
                    } else {
                        let nombre = response.name,
                            idP = response.id,
                            categoria = document.getElementById("categoria").value,
                            boton = '';
                        data = {};
                        data = {categoria, nombre, idP, pageAccessToken};
                        if (response.error) {
                            //console.log('esto es un mensaje',response);
                            //swal('Intentá de nuevo', 'El id de la página está incorrecto', 'error');
                            return false;
                        }

                        document.getElementById("page_name").value = nombre;
                        document.getElementById("page_id").value = idP;
                        document.getElementById("lista").hidden = false;
                        botonE = `<a class="btn btn-sm btn-success" onclick="Opcion()" style="width: 150%">Ejecutar</a>`;
                        //botonC = `<a class="btn btn-sm btn-primary" href="{{ route('scrapComments.index') }}?page=`+idP+'&page_name='+nombre+`" style="width: 150%">Comentarios</a>`;
                        document.getElementById('botonE').innerHTML = botonE;
                        //document.getElementById('botonC').innerHTML = botonC;

                    }
                }
            );
        }

        /********************************************* Opcion ******************************************************/
        function Opcion() {
            "use strict";
            loadingPanel();
            let scrapAll = document.getElementById("customCheck1"),
                scrapAPost = document.getElementById("customCheck2"),
                scrapComment = document.getElementById("customCheck3"),
                scrapReaction = document.getElementById("customCheck4");
            var pagina = "";
            let posteos = "", comment = "", reaction = "";
            let cont = true;
            var terminado;
            if (scrapAll.checked) {
                guardarScrap().then(res1 => {
                    console.log(res1);
                    if (res1) {
                        var pagina = "scrapping de pagina exitoso\n";
                        getPost().then(res2 => {
                            console.log(res2);
                            if (res2) {
                                posteos = "scrapping de posteos exitoso\n";
                                comentario().then(res3 => {
                                    console.log(res3);
                                    if (res3) {
                                        comment = "scrapping de comentarios exitoso\n";
                                        getReactions().then(res4 => {
                                            if (res4) {
                                                reaction = "scrapping de reacciones exitoso\n";
                                                msjScrap(pagina, posteos, comment, reaction);
                                            } else {
                                                reaction = "Error: scrapping de reacciones ha fallado\n";
                                                msjScrap(pagina, posteos, comment, reaction);
                                            }
                                        })
                                    } else {
                                        comment = "Error: scrapping de comentarios ha fallado\n";
                                        reaction = "Error: scrapping de reacciones no realizado\n";
                                        msjScrap(pagina, posteos, comment, reaction);
                                    }
                                })
                            } else {
                                posteos = "Error: scrapping de posteos ha fallado\n";
                                comment = "Error: scrapping de comentarios ha fallado\n";
                                reaction = "Error: scrapping de reacciones no realizado\n";
                                msjScrap(pagina, posteos, comment, reaction);
                            }
                        })
                    } else {
                        swal('Error', 'Intente nuevamente', 'error');
                    }
                });
                cont = false;//no continuar con los siguientes checks
            } else {
                if (scrapAPost.checked && cont) {
                    posteos = (getPost()) ? "scrapping de posteos exitoso\n" : "Error: scrapping de posteos ha fallado\n";
                }
                if (scrapComment.checked && cont) {
                    comment = (comentario()) ? "scrapping de comentarios ha sido exitoso\n" : "Error: scrapping de comentarios ha fallado\n";
                }
                if (scrapReaction.checked && cont) {
                    reaction = (getReactions()) ? "scrapping de reacciones ha sido exitoso\n" : "Error: scrapping de reacciones ha fallado\n";
                }

                msjScrap(pagina, posteos, comment, reaction);
            }

        }

        function msjScrap(pagina, posteos, comment, reaction) {
            loadingPanel();
            setTimeout(()=>{
                swal({
                    icon: 'info',
                    title: 'Finalizado',
                    text: 'Se ha realizado scrapping en los siguientes:\n' + '\n' +
                    pagina +
                    posteos +
                    comment +
                    reaction,
                    button: false,
                }).then(
                    location.href="../index#pagestoge"
                )
            },5000)

        }

        function checkAll(e) {
            document.getElementById("customCheck2").checked = e.checked;
            document.getElementById("customCheck3").checked = e.checked;
            document.getElementById("customCheck4").checked = e.checked;
        }

        /********************************************* Guarda page ******************************************************/
        function guardarScrap() {
            let datos = {};
            let exit = false;
            idP = document.getElementById("page_id").value;
            nombre = document.getElementById("page_name").value;
            categoria = document.getElementById("categoria").value;
            datos = {idP, nombre, categoria, pageAccessToken};
            if (!validate(datos)) {
                return false;
            }
            getInformation(idP, nombre);
            exit = axios.post('{{ route('scrapsPage.saveScrap') }}', datos).then(response => {
                return (response.status == 200);
            });
            return exit;
        }

        function getInformation(page_id, nombre) {
            FB.api(
                "/" + page_id,
                'GET',
                {
                    "fields": "picture,fan_count,category,about,company_overview,location,phone,emails,talking_about_count",
                    "access_token": pageAccessToken
                },
                function (response) {
                    saveInformation(response, page_id, nombre);
                }
            );
        }

        function saveInformation(page, page_id, nombre) {
            let datos = {},
                page_name = nombre;
            fan_count = page.fan_count;
            category = page.category;
            about = page.about;
            company_overview = page.company_overview;
            phone = page.phone;
            email = page.emails;
            talking = page.talking_about_count;
            picture = page.picture.data.url;
            datos = {
                page_id,
                page_name,
                fan_count,
                category,
                about,
                company_overview,
                phone,
                email,
                talking,
                picture,
                pageAccessToken
            },
                axios.post('{{ route('scrapsAll.infoPage',$company) }}', datos).then(response => {
                    console.log(response);
                    if (response.status != 200) {
                        //swal('Intentá de nuevo', 'El id de la página está incorrecto', 'error');
                    }
                });
        }

        /********************************************* Post ******************************************************/
        function getPost() {
            let datos = {};
            let exit = false;
            limit = document.getElementById('limite').value;
            name = document.getElementById("page_name").value;
            page_id = document.getElementById("page_id").value;
            opcion = document.getElementById("customCheck2");
            datos = {page_id, name, limit, pageAccessToken};

            axios.post('{{ route('scrapsPage.ScrapValidation') }}', datos).then(response => {
                console.log(response);
                if (response.data == false) {
                    swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página"', 'error');
                    return false;
                }
            });

            exit = axios.post('{{ route('scrapsAll.getPost') }}', datos).then(response => {
                if (response.data) {
                    console.log(response.data)
                    //swal('Exito !', 'Se ha realizado scrap de los post', 'success');
                    return true;
                } else {
                    swal('Error', 'Intente nuevamente realizar scrap de los post', 'error');
                    return false;
                }
            });
            return exit;
        }


        /********************************************* Comment ******************************************************/
        function comentario() {
            let datos = {};
            let exit = false;
            limit = document.getElementById('limite').value;
            name = document.getElementById("page_name").value;
            page_id = document.getElementById("page_id").value;
            opcion = document.getElementById("customCheck2");
            datos = {page_id, name, limit, pageAccessToken};

            axios.post('{{ route('scrapsPage.ScrapValidation') }}', datos).then(response => {
                if (response.data == false) {
                    swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página" para observar los datos', 'error');
                    return false;
                }
            });

            exit = axios.post('{{ route('scrapsAll.getComments',$company) }}', datos).then(response => {
                if (response.data == true) {
//                    swal('Exito !', 'Se ha realizado scrap de los comentarios', 'success');
                    return true;
                } else {
                    swal('Error', 'Intente nuevamente a realizar scrap de los comentarios', 'error');
                    return false;
                }
            });
            return exit;
        }


        /********************************************* Reacciones ******************************************************/
        function getReactions() {
            let datos = {};
            name = document.getElementById("page_name").value;
            page_id = document.getElementById("page_id").value;
            opcion = document.getElementById("customCheck2");
            datos = {page_id, name, pageAccessToken};

            axios.post('{{ route('scrapsPage.ScrapValidation') }}', datos).then(response => {
                if (response.data == false) {
                    swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página"', 'error');
                    return false;
                }
            });

            exit = axios.post('{{ route('scrapsAll.scrapReacciones') }}', datos).then(response => {
                if (response.data) {
//                    swal('Exito !', 'Se ha realizado scrap de las reacciones', 'success');
                    return true;
                } else {
                    swal('Error', 'Intente nuevamente realizar scrap de las reacciones', 'error');
                    return false;
                }
            });
            return exit;
        }

        function stopRKey(evt) {
            var evt = (evt) ? evt : ((event) ? event : null);
            var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
            if ((evt.keyCode == 13) && (node.type == "text")) {
                return false;
            }
        }

        document.onkeypress = stopRKey;
    </script>
@endsection
