@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h2 class="fw-bold text-center"><span><i
                                    class="icon-social-facebook fa-1x"></i></span> Administrador de Páginas</h2>
                    </div>
                    <div class="card-body">
                        <div class="justify-content-md-start"><h3 class="fw-bold">Mis páginas de Facebook</h3></div>
                        <div class="justify-content-md-center" id="pages">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="justify-content-md-start" id="pagestoge">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                if (!localStorage.getItem('s') == 1) {
                    swal("Login!", "Login successful!", "success");
                    localStorage.setItem('s', 1);
                }
                let accessToken = response.authResponse.accessToken;
                isLogedIn(accessToken);
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                window.location = "{{ route('home') }}";
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                window.location = "{{ route('home') }}";
            }
        }

        function isLogedIn() {
            FB.api(
                '/me/accounts',
                'GET',
                {"fields": "picture,access_token,name,page_token"},
                function (response) {
                    if (response.error) {
                        swal("Sin datos!", "Lamentamos informarle que en estos momentos no se puede mostrar ninguna página!", "warning");
                    } else {
                        generatePageLists(response);
                    }
                    PageCompany(response);
                }
            );
        }

        function generatePageLists(response) {
            try {
                let pageList = '';
                let pageListStotage = '';
                let pag = [];
                let pageFB = response.data;
                //paginas de facebook
                localStorage.setItem('FaceBookPages', JSON.stringify(response.data));
                (response.data).forEach(page => {
                    pageList += `<div class="card pages-cards mb-2 col-sm-12">
                                    <div class="row no-gutters p-2">
                                        <div class="col-xs-1 col-sm-2 mx-auto">
                                           <img src="${page.picture.data.url}" class="rounded" alt="${page.name}" width=50%>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                          <div class="card-body">
                                            <h5 class="card-title">${page.name}</h5>
                                          </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 mt-3 justify-content-center">
                                            <a class="btn btn-primary btn-round m-2 btn-sm" href="{{ route('post.index') }}?page=${page.id}&page_name=${page.name}" title="Hacer un Post"><i class="fas fa-pencil-alt"></i> Crear post</a>
                                            <a class="btn btn-success btn-round m-2 btn-sm" href="{{ route('scrapComments.index') }}?page=${page.id}&page_name=${page.name}" title="Ver Posteos"><i class="fas fa-eye"></i> Ver posteos</a>

                                        </div>
                                    </div>
                                </div>`;
                });
                // Permite mostrar mas de 10 paginas
                if (response.paging.next) {
                    next(response.paging.next);
                }
                $('#pages').append(pageList);
            } catch (ex) {
                console.error('outer', ex.message);
            }
        }

        function next(next) {
            FB.api(
                next,
                function (response) {
                    generatePageLists(response);
                }
            )
        }

        function PageCompany(response) {
            try {
                let pageList = '';
                let pageListStotage = '';
                let pag = [];
                let pageFB = response.data;

                @foreach($pages as $page)
                    pagejson = {
                    'id': '{{$page->page_id}}',
                    'token': '{{$page->token}}',
                    'picture': '{{$page->picture}}',
                    'name': '{{$page->page_name}}'
                };
                pag.push(pagejson);
                @endforeach
                localStorage.setItem('DataBasePages', JSON.stringify(pag));
                (pag).forEach(page => {
                    if (!(response.data).find(el => el.id === page.id)) {//ojo es para no duplicar con las mostradas arriba,
                        pageListStotage += `
                           <div class="card pages-cards mb-2 col-sm-12">
                                <div class="row no-gutters p-2">
                                    <div class="col-xs-1 col-sm-2 mx-auto">
                                       <img src="${page.picture}" class="rounded" alt="${page.name}" width=50%>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                      <div class="card-body">
                                        <h5 class="card-title">${page.name}</h5>
                                      </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 mt-3 justify-content-center">` +
                            `<a class="btn btn-sm btn-primary btn-round m-2" href="{{ route('scrapComments.index') }}?page=${page.id}&page_name=${page.name}" title="Ver Posteos"><i class="fas fa-eye"></i> Ver posteos</a>

                                    </div>
                                </div>
                            </div>`;
                    }
                });
                $('#pagestoge').html('<div class="justify-content-md-start"><h3 class="fw-bold">Mis páginas consultadas</h3></div>' + pageListStotage);
            } catch (ex) {

            }
        }

        function susbcribe(element) {
            loadingPanel();
            let data = {
                'page_id': element.dataset.page,
                'token': element.dataset.token,
                'page_name': element.dataset.name
            };
            axios.post('{{ route('facebook.susbcribe') }}', data).then(response => {
                loadingPanel();
                swal(response.data.status, response.data.message, response.data.type);
            });
        }

        function unsubscribe(element) {
            loadingPanel();
            let data = {
                'page_id': element.dataset.page,
                'token': element.dataset.token,
                'page_name': element.dataset.name
            };
            axios.post('{{ route('facebook.unsubscribe') }}', data).then(response => {
                loadingPanel();
                swal(response.data.status, response.data.message, response.data.type);
            });
        }

    </script>
    <style>
        .pages-cards:hover {
            transition: transform .3s; /* Animation */
            transform: scale(1.05); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
        }
    </style>
@endsection

