@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="fw-bold"><span><i class="fab fa-facebook-messenger fa-1x"></i></span> Extraer
                            conversaciones de Messenger</h3></div>
                    <div class="card-body table-responsive">
                        <input id="user" type="hidden" value="{{ Auth::user()->id }}">

                        <label for="paginas">Seleccione la pagina</label>
                        <select class="lista-pages lista form-control" id="pagina" name="pagina">
                            <option selected>Seleccione una Pagina...</option>
                        </select>
                        <br>
                        <div class="form-group">
                            <button class="btn btn-sm btn-block btn-primary btn-round" type="button" value="scrap Inbox"
                                    onclick="getDatos()">Comenzar extracción ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var image;

        function statusChangeCallback(response) {
            console.log(response);
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
                '/me',
                'GET',
                {"fields": "accounts"},
                function (response) {
                    console.log(response);
                    if (response.error) {
                        swal("Sin datos!", "Lamentamos informarle que en estos momentos no se puede mostrar las páginas!", "warning");
                    } else {
                        console.log(response);
                        generatePageLists(response);
                    }
                }
            );
        }

        function generatePageLists(response) {
            console.log(response);
            var pageLists = '';
            var data = response.accounts.data;
            data.forEach(function (element, index) {
                pageLists += '<option value="' + element.id + '">' + element.name + '</option>';

            });
            $('.lista-pages').append(pageLists);
            if (response.accounts.paging.next) {
                var siguiente = response.accounts.paging.next;
                next(siguiente);
            }

        }

        function generatePageLists2(response) {
            var pageLists = '';
            var data = response.data;
            data.forEach(function (element, index) {
                pageLists += '<option value="' + element.id + '">' + element.name + '</option>';
                // alert(tokenUser);

            });
            $('.lista-pages').append(pageLists);
            if (response.paging.next) {
                var siguiente = response.paging.next;
                next(siguiente);
            }

        }

        function next(next) {
            FB.api(
                next,
                function (response) {
                    generatePageLists2(response);

                }
            )
        }

        function getDatos() {
            var page = document.getElementById('pagina').value;
            FB.api(
                '/' + page + '',
                'GET',
                {"fields": "access_token,name"},
                function (response) {
                    scrap(response);
                }
            );
        }

        function scrap(response) {
            loadingPanel();
            let datos = {},
                page = response.id,
                page_name = response.name,
                access_token = response.access_token,
                user = document.getElementById('user').value;
            datos = {page, page_name, user, access_token};
            axios.post('{{ route('scrapsInbox.ScrapInbox',$company) }}', datos).then(response => {
                if (response.data) {
                    loadingPanel();
                    swal('Exito', 'Se ha realizado los scrap de los inbox', 'success');
                    if(page){
                        setTimeout(function () {
                            let dirección = window.location = "{{ route('SentimentInbox.selectInbox') }}?page=" + page + " ";
                        }, 2000);
                    }
                    else{
                        swal('Ops', 'Por favor seleccione una página', 'warning' );
                    }


                } else {
                    loadingPanel();
                    swal('Error', 'Intente nuevamente realizar scrap de los inbox', 'error');
                }
            });
        }


    </script>
@endsection
