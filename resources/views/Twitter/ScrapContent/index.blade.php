@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="fw-bold"><span><i class="icon-layers"></i></span> Extracción de contenido de Twitter</h3></div>
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                    <div class="card-body table-responsive">
                        @if(count($categories)> 0)
                            <div class="form-group">
                                <!-- Se cambia categoria por el nombre de contenido -->
                                {{ Form::label('categoria_id','Seleccione el contenido de Twitter *') }}
                                <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
                                   data-content="Selecciona el contenido que desea que se extraiga los datos de Twitter">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                {{ Form::select('categoria_id',$categories,null,['class'=>'form-control','placeholder'=>'Seleccione el contenido','required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                            <div id="lista">
                                <h4>Elementos a ejecutar</h4>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">Scrap contenido</label>
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck2">
                                            <label class="custom-control-label" for="customCheck2">Actualizar
                                                reacción</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" id="actualizar" class="btn btn-sm btn-round btn-block btn-primary"
                                        onclick="Opcion()">Consultar página de contenido
                                </button>

                                <div id="botonE"></div>
                            </div>
                        @else
                            <h2><b>No se encuentra contenidos a extraer</b></h2>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var pageAccessToken = '';

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

        /********************************************* Opcion ******************************************************/
        function Opcion() {
            let contenido = document.getElementById("categoria_id").value;

            if (contenido == '') {
                swal('Error !', 'Seleccione un contenido', 'error');
                document.getElementById("lista").hidden = true;
            } else {
                //actualizar
                document.getElementById("lista").hidden = false;
                document.getElementById("actualizar").hidden = true;
                botonE = `<button type="button" id="extraccion" class="btn btn-sm btn-round btn-block btn-success"
                                    onclick="extraccionData()">Comenzar extracción ahora</a>
                            </button>`;
                document.getElementById('botonE').innerHTML = botonE;
            }
        }

        function extraccionData() {
            let scrapCategoria = document.getElementById("customCheck1");
                scrapReaction  = document.getElementById("customCheck2");

            if (scrapCategoria.checked) {
                loadingPanel();
                getpaginas();
            }

            if (scrapReaction.checked) {
                loadingPanel();
                actualizarReacciones();
            }
        }

        /********************************************* Page ******************************************************/
        function getpaginas() {
            let datos = {},
                categoria_id = document.getElementById('categoria_id').value;
            datos = {categoria_id};
            axios.post('{{ route('scrapContent.page',$company) }}', datos);
            setTimeout(loadingPanel,4000);
            setTimeout(()=>{
                swal('Éxito', 'Nuestros bots esta leyendo las páginas de tu contenido','success');
            },5000)
        }

        /********************************************* Reaction ******************************************************/
        function actualizarReacciones() {
            let datos = {},
                categoria_id = document.getElementById('categoria_id').value;
            datos = {categoria_id};
            axios.post('{{ route('scrapContent.reaction',$company) }}', datos);
            setTimeout(loadingPanel,4000);
            setTimeout(()=>{
                swal('Éxito', 'Nuestros bots esta leyendo las páginas de tu contenido','success');
            },5000)
        }

    </script>
@endsection
