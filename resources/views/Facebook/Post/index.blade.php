@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4>Publicar en Facebook</h4></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('post.create') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input name="page" type="hidden" value="{{ $_GET['page'] }}">
                                    <input name="token_page" id="token_page" type="hidden" value="">
                                    <div class="form-group">
                                        <label for="contenido">Contenido</label>
                                        <textarea type="text" rows="5" class="form-control" name="contenido" id="contenido" aria-describedby="Texto a publicar" placeholder="Contenido de la publicación"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="imagen">Imagen</label>
                                        <input type="file" class="form-control-file" id="files" name="files" accept=".png,.gif,.jpg,.jpeg">
                                    </div>
                                    <button id="prev" type="button" class="btn btn-primary">Vista previa</button>
                                    <button type="submit" class="btn btn-success">Publicar</button>
                                </form>
                            </div>
                            <div class="col-md-6 invisible mt-3" id="prev-facebook">
                                <div class="card">
                                    <div class="media ml-3">
                                        <img src="" id="perfil" class="mr-3 mt-4 rounded-circle border border-secondary" alt="perfil picture">
                                        <div class="media-body">
                                            <a href="https://fb.com/{{ $_GET['page'] }}" target="_blank"><p class="mt-4 ml-0 mb-0">{{ $_GET['page_name'] }}</p></a>
                                            <p class="ml-0 mt-1 mb-0 small">{{ date('Y-m-d h:i:s A') }}</p></div>
                                    </div>
                                    <p class="m-3" id="prev-contenido"></p>
                                    <output id="list"></output>
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
    <script>
        var pageAccessToken="",
            loadMore='';

        /**
         * statusChangeCallback
         * @param response
         */
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
//                swal("Login!", "Login successful!", "success");
                let accessToken = response.authResponse.accessToken;
                isLogedIn(accessToken);
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                window.location = "{{ route('facebook.index') }}";
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                window.location = "{{ route('facebook.index') }}";
            }
        }


        /**
         * IF USER IS LOGEDIN TO FB, PULL AND DISPLAY
         * PAGE INFO, POSTS AND COMMENTS
         **/
        function isLogedIn() {
            /** start: GET AND DISPLAY PAGE INFO **/
            FB.api(
                '/{{ $_GET['page'] }}',
                'GET',
                {"fields":"access_token"},
                function(response) {
                    if(response.access_token){
                        pageAccessToken=response.access_token;
                        document.getElementById('token_page').value=pageAccessToken;
//                        getInformation();
//                        getFans();
                    }else{
                        FB.api(
                            '/me',
                            'GET',
                            {"fields":"accounts"},
                            function(response) {
                                pageAccessToken=response.accounts.data[0].access_token;
                                document.getElementById('token_page').value=pageAccessToken;
//                                getInformation();
//                                getFans();
                            }
                        );
                    }
                }
            );
            FB.api(
                '/{{ $_GET['page'] }}',
                'GET',
                {"fields":"id,cover,about,category,link,picture{url}"},
                function(response) {
                    if (response && !response.error) {
                        if(response.cover){
                            $("#perfil").attr("src", response.picture.data.url);
                        }
                        //$("#cover-photo").attr("src","/fscrdata/reaccions/cover.jpeg" );
//                        $("#page-link").attr("href", response.link);
                    }
                }
            );
            /** start: GET AND DISPLAY PAGE INFO **/

            /** start: SENDING REQUEST TO FB TO GET POSTS **/


        }
        function archivo(evt) {
            var files = document.getElementById('files').files; // FileList object
            var contenido=document.getElementById('contenido').value;
            $('#list').html('');
            if(files !==''){
                //Obtenemos la imagen del campo "file".
                for (var i = 0, f; f = files[i]; i++) {
                    //Solo admitimos imágenes.
                    if (!f.type.match('image.*')) {
                        continue;
                    }
                    var reader = new FileReader();

                    reader.onload = (function(theFile) {
                        return function(e) {
                            // Creamos la imagen.
                            document.getElementById("list").innerHTML = ['<img class="w-100" src="', e.target.result,'" title="', escape(theFile.name), '"/>'].join('');
                        };
                    })(f);

                    reader.readAsDataURL(f);
                }
            }
            $('#prev-contenido').html(contenido);
            $('#prev-facebook').removeClass('invisible')
        }

        document.getElementById('prev').addEventListener('click', archivo, false);
    </script>
@endsection

