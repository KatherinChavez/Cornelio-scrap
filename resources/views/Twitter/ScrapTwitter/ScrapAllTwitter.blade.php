@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">
                                <h2><span><i class="fab fa-twitter"></i></span> Extracción de información de páginas de Twitter</h2>
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
                                {{ Form::label('username','Nombre de usuario de Twitter *') }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
                                   data-content="Ingrese el nombre de usuario de la página que se encuetra en Twitter">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <div class="input-group mb-3">
                                    {{ Form::text('username','',['class' => 'form-control', 'maxlength' => '80','onchange'=>'ObtenerId()']) }}

                                    <div class="input-group-append">
                                        {{--<button class="btn btn-outline-secondary" type="button" title="Mis páginas" onclick="showpages()"><i class="fas fa-swatchbook"></i></button>--}}
                                    </div>
                                </div>
                                <p class="text-danger">{{ $errors->first('page_id')}}</p>
                            </div>
                            <div class="form-group">
                                {{ Form::label('page_name','Nombre de la página de Twitter ') }}
                                {{ Form::text('page_name',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('page_id','Identificador de la página de Twitter') }}
                                {{ Form::text('page_id',null,['class' => 'form-control', 'maxlength' => '80', 'disabled' => 'true']) }}
                            </div>

                            <div class="form-group">
                                {{ Form::label('limitePost','Cantidad de tweets *',['title'=>'Cantidad de posteos a extraer']) }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
                                   data-content="Ingrese la cantidad de publicaciones que desea almacenar">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <input type="number" class="form-control" id="limite" placeholder="10" min="10" max="100">
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
                    <h2 class="modal-title" id="exampleModalLongTitle"> Extracción de información de páginas de Twitter</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>El scrap de página permitirá obtener información general de cualquier
                        tipo de página que se encuentre en Twitter.
                        <br><br>
                        <b>¿Cómo obtener la información de la página ?</b>
                        <br>
                        Será muy sencillo, solo deberás de ingresar el nombre de usario de
                        la página que deseas buscar.

                        <br><br>
                        <b>¿Cuál es el nombre de usuario de la página de Twitter ?</b>
                        <br>
                        Para conocer el nombre de usuario de la página nos dirigimos a Twitter y
                        buscamos en la página deseada las siguientes opciones
                        <br><br>
                        <b>Nombre de usuario</b>
                        <br>
                        <img src="{{ asset('imagen/apps/twitter1.PNG') }}" alt="Twitter" style="width: 90%">
                        <br><br>

                        <b>Otra forma de obterner el nombre de usuario</b>
                        <br>
                        <img src="{{ asset('imagen/apps/twitter2.PNG') }}" alt="Twitter" style="width: 90%; vertical-align: middle">
                        <br><br>
                        Al obtener el nombre de usuario para gestionar la página lo podremos ingresar en la casilla solicitada
                        <br>
                        <img src="{{ asset('imagen/apps/twitter3.PNG') }}" alt="Twitter" style="width: 90%; vertical-align: middle">
                        <br>
                        <b>Cabe destacar que al momento de ingresar el nombre de usuario no se debe encontrar el @, únicamente se debe de ingresar el nombre de usuario</b>
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
                    text: 'Por favor proceda a crear un contenido de Twitter'
                }).then((result) => {
                    window.location = "{{ route('twitter.index') }}";
                })
            }
        });

        document.getElementById("lista").hidden = true;
        let user = "{{ Auth::user()->id }}",
            idP = "";

        /********************************************* FACEBOOK ******************************************************/

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
            let username  = document.getElementById("username").value;
            let split     = username.split("@").join("");
            ObtenerNombre(split);
        }

        function ObtenerNombre(username) {
            let datos = {"username":username};
            axios.post('{{ route('twitterScrap.validate') }}', datos).then(response => {
                if(response.status == 200){
                    document.getElementById("page_name").value = response.data.data.name;
                    document.getElementById("page_id").value = response.data.data.id;
                    document.getElementById("lista").hidden = false;
                    botonE = `<a class="btn btn-sm btn-success" onclick="Opcion()" style="width: 150%">Ejecutar</a>`;
                    document.getElementById('botonE').innerHTML = botonE;
                }
            });
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
                    location.href="../TwitterScrap"
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
            username = document.getElementById("username").value;
            nombre = document.getElementById("page_name").value;
            categoria = document.getElementById("categoria").value;
            datos = {idP, nombre, categoria, username};
            if(categoria){
                exit = axios.post('{{ route('twitter.saveScrapTwitter') }}', datos).then(response => {
                    return (response.status == 200);
                });
            }
            else{
                loadingPanel();
                swal('Error', 'Debe de seleccionar un contenido', 'error');
            }
            return exit;
        }

        /********************************************* Post ******************************************************/
        function getPost() {
            let datos = {};
            let exit = false;
            limit = document.getElementById('limite').value;
            name = document.getElementById("page_name").value;
            username = document.getElementById("username").value;
            id = document.getElementById("page_id").value;
            opcion = document.getElementById("customCheck2");
            datos = {id, name, limit, username};
            if(limit >= 10 && limit <= 100){
                axios.post('{{ route('twitterScrap.validate') }}', datos).then(response => {
                    console.log(response);
                    if (response.status != 200) {
                        swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página"', 'error');
                        return false;
                    }
                });

                exit = axios.post('{{ route('twitterScrap.scrapTweet') }}', datos).then(response => {
                    console.log('post',response);
                    if (response.data == 200) {
                        //swal('Exito !', 'Se ha realizado scrap de los post', 'success');
                        return true;
                    } else {
                        //swal('Error', 'Intente nuevamente realizar scrap de los post', 'error');
                        return false;
                    }
                });
                return exit;
            }
            else if(limit < 10){
                loadingPanel();
                swal('Error', 'La cantidad de posteo que ha ingresado debe ser mayor o igual a 10', 'error');
                //return false;
            }
            else if(limit > 100){
                loadingPanel();
                swal('Error', 'La cantidad de posteo que ha ingresado no puede ser mayor a 100', 'error');
                //return false;
            }
        }


        /********************************************* Comment ******************************************************/
        function comentario(){
            let datos = {};
            let exit = false;
            limit = document.getElementById('limite').value;
            name = document.getElementById("page_name").value;
            username = document.getElementById("username").value;
            id = document.getElementById("page_id").value;
            opcion = document.getElementById("customCheck2");
            datos = {name, limit, id};

            if(limit >= 10 && limit <= 100) {
                axios.post('{{ route('twitterScrap.validate') }}', datos).then(response => {
                    if (response.status != 200) {
                        swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página"', 'error');
                        return false;
                    }
                });

                exit = axios.post('{{ route('twitterScrap.commentTweet') }}', datos).then(response => {
                    console.log('comenta', response);
                    if (response.data == 200 && response.status == 200) {
                        //swal('Exito !', 'Se ha realizado scrap de los post', 'success');
                        return true;
                    } else {
                        //swal('Error', 'Intente nuevamente realizar scrap de los post', 'error');
                        return false;
                    }
                });
                return exit;
            }
            else if(limit < 10){
                loadingPanel();
                swal('Error', 'La cantidad de comentario que ha ingresado debe ser mayor o igual a 10', 'error');
                //return false;
            }
            else if(limit > 100){
                loadingPanel();
                swal('Error', 'La cantidad de comentario que ha ingresado no puede ser mayor a 100', 'error');
                //return false;
            }

        }


        /********************************************* Reacciones ******************************************************/
        function getReactions() {
            let datos = {};
            name = document.getElementById("page_name").value;
            id = document.getElementById("page_id").value;
            username = document.getElementById("username").value;
            limit = document.getElementById('limite').value;
            opcion = document.getElementById("customCheck2");
            datos = {id, name, username, limit};

            axios.post('{{ route('twitterScrap.validate') }}', datos).then(response => {
                console.log('validar' , response);
                if (response.status != 200) {
                    swal('Error', 'Esta página no se encuentra regitrada, por favor seleccionar la opción "Toda la página"', 'error');
                    return false;
                }
            });

            exit = axios.post('{{ route('twitterScrap.reactionTweet') }}', datos).then(response => {
                console.log('reaccion',response.data);
                if (response.data == 200 && response.status == 200) {
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
