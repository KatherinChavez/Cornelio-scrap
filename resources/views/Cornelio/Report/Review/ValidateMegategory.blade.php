@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-line"></i>  Revisar reporte
                    @php
                    //dd($posts);
                    @endphp

                    @if(count($posts)>0)
                        @php
                            //dd($posts);
                        @endphp
                    {{--<button id="{{$posts[0]['megacategoria_id']}}" class="btn btn-primary pull-right" style="background-color: #3b5998;" onclick="actualizarTodo(this.id);"><i class="fab fa-facebook-f" style="color: white"></i></button>--}}
                    <button id="{{$posts[0]['subcategoria_id']}}" class="btn btn-primary pull-right" style="background-color: #3b5998;" onclick="actualizarTodo(this.id);"><i class="fab fa-facebook-f" style="color: white"></i></button>
                    @endif
                </div>
                <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body table-responsive">
                    <!------------------------------------------------------ Si no encuentra datos ----------------------------------------------------->
                    @if(count($posts)==0)
                        <img name="Sin alertas" src="{{ asset('imagen/apps/sin_alertas.jpg') }}" alt="Sin alertas" title="Sin alertas" style="display:block; margin:auto;">
                    @endif
                     
                    <!------------------------------------------------------ Muestra los datos que se encuentra ----------------------------------------------------->
                    @if(count($posts)>0)
                        <img name="Reporte diario" src="{{ asset('imagen/apps/encabezado_revision.jpg') }}"  alt="Revisar clasificación" title="Revisar clasificación" style="vertical-align: middle; width:100%">

                        @foreach($posts as $post)
                            
                            @if(isset($post['sub']))
                                <br><div class="w-100"></div><h2>Publicaciones del tema: {{$post['sub']}}</h2><div class="w-100"></div><br>
                            @endif

                                <div class="col-sm-11 ">
                                    <div class="card mb-5 ">
                                        @if($post['attachment']['picture']!=null)
                                            <img class="card-img-top img-fluid w-100" src="{{$post['attachment']['picture']}}" alt="">
                                        @endif
                                        @if($post['attachment']['video']!=null)
                                            <video width="320" height="240" controls>
                                                <source src="{{$post['attachment']['video']}}" type="video/mp4"></video>
                                        @endif
                                            @php
                                                    //dd($post,$post['post']);
                                            @endphp
                                        <div class="card-body">
                                            <h6 class="card-title mb-1">{{$post['post']['page_name']}}</h6>
                                            <p class="card-text small">{{$post['post']['content']}}
                                                @if($post['attachment']['url']!=null)
                                                    <br><a href="{{$post['attachment']['url']}}" target="_blank">{{$post['attachment']['title']}}</a>
                                                @endif
                                        </div>
                                        </p>

                                        <hr class="my-0">
                                        <div class="card-body py-2 small">
                                            <div class="mr-3 d-inline-block">
                                                <img src="{{ asset('reacciones/like.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                                <label id="rea-{{$post['post_id']}}" for="like">{{$post['reacciones']}}</label>
                                            </div>

                                            <div class="mr-3 d-inline-block">
                                                <img src="{{ asset('reacciones/comment.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle">
                                                <label id="comm-{{$post['post_id']}}" for="comentarios" >{{$post['comentarios']}}</label>
                                            </div>                                            
                                        </div>
                                        <hr class="my-0">
                                        <div class="card-body py-2 small">
                                            {{--<div class="mr-2 d-inline-block">--}}
                                                {{--<button type="button" class="btn btn-primary btn-small"  id="act-{{$post['post_id']}}" data-page="{{$post['page_id']}}" onclick="actualizar(this.id)" title="Actualizar" style="background-color: #3b5998;">--}}
                                                    {{--<i class="fab fa-facebook-f"></i>--}}
                                                {{--</button>--}}
                                             {{--</div>--}}
                                            <div class="mr-2 d-inline-block">
                                                <a class="btn btn-success btn-small" title="Ver" href="https://fb.com/{{$post['post_id']}}" target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                            <div class="mr-2 d-inline-block">
                                                @php
                                                   // dd($post,$post['post']);
                                                @endphp
                                                <button type="button" class="btn btn-primary btn-small" title="Telegram" onclick="telegram(this.id)" id="telegram-{{$post['post_id']}}" data-mega="{{$post['megacategoria_id']}}" data-sub="{{$post['subcategoria_id']}}">
                                                    <i class="fa fa-paper-plane"></i>
                                                </button>
                                            </div>
                                            <div class="mr-2 d-inline-block">
                                                <button type="button" onclick="whatsappGetNumeros(this.id)" id="wha-{{$post['post_id']}}" class="btn btn-success btn-small" title="WhatsApp" data-toggle="modal" data-target="#modalWha" data-page="{{$post['post']['page_name']}}" data-mega="{{$post['megacategoria_id']}}" data-sub="{{$post['subcategoria_id']}}">
                                                    <i class="fab fa-whatsapp"></i>
                                                </button>
                                            </div>
                                            <div class="mr-2 d-inline-block">
                                                <a class="btn btn-warning btn-small" id="link-{{$post['post_id']}}" title="Link" href="/ReportDetail/{{base64_encode($post['post_id'])}}/{{base64_encode($post['subcategoria_id'])}}" target="_blank">
                                                <i class="icon-share-alt"></i>
                                                </a>
                                            </div>
                                            <div class="mr-2 d-inline-block">
                                                <button type="button" class="btn btn-danger btn-small" id="{{$post['post_id']}}" data-mega="{{$post['megacategoria_id']}}" data-sub="{{$post['subcategoria_id']}}" onclick="Desclasificar(this.id)" title="Desclasificar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="mr-2 d-inline-block">
                                                <button type="button" class="btn btn-info btn-small" onclick="datos(this.id);" data-toggle="modal" data-target="#modalRecla" id="rec-{{$post['post_id']}}" data-mega="{{$post['megacategoria_id']}}" data-sub="{{$post['subcategoria_id']}}" title="Reclasificar">
                                                    <i class="fa fa-random"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <hr class="my-0">
                                        <div class="card-footer small text-muted">{{$post['post']['created_time']}}
                                            <p class="pull-right">{{$post['subcategory']['name']}}</p>
                                        </div>
                                    </div>
                                </div>
                        @endforeach 
                    @endif
                </div>


                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRecla" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reclasificar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="post_id" value="">
                <input type="hidden" id="subcategoria_id" value="">
                <input type="hidden" id="megacategoria_id" value="">
                <div class="form-group">

                    <label for="Sub" class="col-form-label">Temas</label>
                    <select class="form-control" id="Sub">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="reclasificar()" data-dismiss="modal">Reclasificar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalWha" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Notificar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="post_id-wha" value="">
                <input type="hidden" id="subcategoria_id-wha" value="">
                <input type="hidden" id="pagina-wha" value="">
                <div class="form-group">
                    <label for="contactos" class="col-form-label">Enviar a:</label>
                    {{--<select class="form-control" id="contactos">--}}
                        {{--<option value="0">Seleccione un contacto...</option>--}}
                        {{--<option value="50688333737">Hans Jimenez</option>--}}
                        {{--<option value="50688317489">Sergio Bonilla</option>--}}
                        {{--<option value="50683532094">Andrea Marín</option>--}}
                        {{--<option value="50687116664">Cesar</option>--}}
                        {{--<option value="50688825700">Hugo</option>--}}
                        {{--<option value="50672694029">Roger Mata</option>--}}
                        {{--<option value="50688731768">Sandra Castro</option>--}}
                        {{--<option value="50683730947">Teresita Arana</option>--}}
                        {{--<option value="50688249015">Melania Chacon</option>--}}
                        {{--<option value="50688518325">Mónica Chavarria</option>--}}
                        {{--<option value="50683129459">Jeisson Forero</option>--}}
                        {{--<option value="1">Todos</option>--}}
                    {{--</select>--}}
                    <div id="contactos"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="whatsapp()" data-dismiss="modal">Enviar</button>
            </div>
        </div>
    </div>
</div>


@endsection


@section('script')
    <script>
        //$(document).ready(megcategorias());
        $(document).ready(subcategoria());
        let pageAccessToken = "";


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
                {"client_id":"{{env('APP_FB_ID_2')}}","client_secret":"{{env('APP_FB_SECRET_2')}}","grant_type":"client_credentials"},
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

        /*----------------------------------------------------------- Actualizar -----------------------------------------------------------------*/

        function actualizar(id) {
            let datos = {};
                post_id=id.replace('act-','');
                pagina=$("#"+id).data("page");
            //showProcessing();
            datos = {post_id,pagina, pageAccessToken};
            axios.post('{{ route('Review.AllUpdateScrap',$company) }}', datos).then(response => {
                //$('#comm-'+post_id).html(data.Comentarios);
                //$('#rea-'+post_id).html(data.Reacciones);
                //hideProcessing();
                //getReactions(post_id);
            });
        }

        function actualizarTodo(id) {
            alert('Se envio la petición');
            datos = {id, pageAccessToken};
            axios.post('{{ route('Review.AllUpdate',$company) }}', datos).then(response => {
            });
        }
        
        /*----------------------------------------------------------- Telegram -----------------------------------------------------------------*/

        function telegram(id) {
            let datos = {};
                post_id=id.replace('telegram-','');
                mega=$("#"+id).data("mega");
                sub=$("#"+id).data("sub");
            datos = {post_id, mega, sub};
            axios.post('{{ route('Review.Telegram',$company) }}', datos).then(response => {
                alert('Mensaje Enviado');
            });
        }

        /*----------------------------------------------------------- whatsapp -----------------------------------------------------------------*/

        function whatsappGetNumeros(id) {
            let datos = {};
                sub=$('#'+id).data('sub');
                pag=$('#'+id).data('page');
            datos = {sub,pag };
            axios.post('{{ route('whatsapp.Contact',$company) }}', datos).then(response => {
                listaContactos(response.data);
                document.getElementById('subcategoria_id-wha').value=sub;  
            });
        }

        function listaContactos(data) {
            let contatosList='',
                long=data.length;
            if(long>0){
                contatosList+='<input type="radio" name="options" id="todos" value="todos">' +
                    '<label for="todos">todos</label><br>';
                for (let i=0;i<long;i++){
                    contatosList+='<input type="radio" name="options" id="'+data[i]['numeroTelefono']+'"  value="'+data[i]['numeroTelefono']+'" > '+
                    ' <label for="'+data[i]['numeroTelefono']+'">'+data[i]['descripcion']+'</label><br>'
                }
            }else{
                //contatosList+='<h3>No tiene contactos asociados a la subcategoria</h3>';
                contatosList+='<h3>No tiene contactos asociados</h3>';
            }

            $('#contactos').html(contatosList);
        }

        function  whatsapp() {
            let selected =[],datos,
                prueba=document.getElementsByName('options').c,
                phone=$('input:radio[name=options]:checked').val(),
                sub=document.getElementById('subcategoria_id-wha').value;

            datos = {phone, sub};
            axios.post('{{ route('whatsapp.Send',$company) }}', datos).then(response => {
                swal('Exito !', 'Se ha enviado el mensaje', 'success');
            });
        }

        /*----------------------------------------------------------- Reclasificar -----------------------------------------------------------------*/
        function megcategorias() {
            let datos = {};
                user_id=document.getElementById("user").value;
                datos = {user_id}; 
                megaList='<option value="0" selected>Seleccione una categoría</option>';
            
            axios.post('{{ route('Report.ItemMegacategory',$company) }}', datos).then(response => {
                let long=response.data.length;
                    for (let i=0;i<long;i++){
                        megaList+='<option value="'+response.data[i]['id']+'">'+response.data[i]['name']+'</option>';
                    }
                $('#Mega').html(megaList);
            });
        }

        $('#Mega').change(function () {
            subcategoria();
        });
        
        function subcategoria() {
            let datos = {};
                user_id=document.getElementById("user").value;
                //mega=document.getElementById('Mega').value;
                subList='<option value="0" selected>Seleccione un tema</option>';
            
            datos = {user_id};
            axios.post('{{ route('Report.ItemSubcategory',$company) }}', datos).then(response => {
                let long=response.data.length;
                    for (let i=0;i<long;i++){
                        subList+='<option value="'+response.data[i]['id']+'">'+response.data[i]['name']+'</option>';
                    }
                $('#Sub').html(subList);
            });
        }

        function datos(id) {
            let mega=$('#'+id).data('mega'),
                sub=$('#'+id).data('sub'),
                post_id=id.replace('rec-','');
            document.getElementById('post_id').value=post_id;
            document.getElementById('subcategoria_id').value=sub;
            document.getElementById('megacategoria_id').value=mega;
        }

        function reclasificar() {
            let datos = {},
                subcategoria=document.getElementById('Sub').value,
                post_id=document.getElementById('post_id').value,
                sub=document.getElementById('subcategoria_id').value;
            datos = {subcategoria, sub, post_id};
            axios.post('{{ route('Review.Reclassify',$company) }}', datos).then(response => {
                $('#telegram-'+post_id).attr('data-sub', response.data.subcategoria_id);
                $('#telegram-'+post_id).attr('data-mega', response.data.megacategoria_id);
                $('#'+post_id).attr('data-sub', response.data.subcategoria_id);
                document.getElementById("subcategoria_id").value=response.data.subcategoria_id;
                let sub_base=btoa( response.data.subcategoria_id),
                    post_base=btoa( post_id);
                //$('#link-'+post_id).attr('href','https://cornel.io/analyzer/reporteDetalle/'+post_base+'/'+sub_base)
                swal('Exito !', 'Se ha reclasificado', 'success');

            });
        }
        
        /*----------------------------------------------------------- Desclasificar -----------------------------------------------------------------*/
        
        function Desclasificar(id) {
            let datos = {};
                mega=$("#"+id).data("mega");
                sub=$("#"+id).data("sub");
                r=confirm('Esta seguro que desas desclasificar esta publicación?');
            datos = {mega, sub, id};
            if(r==true){
                axios.post('{{ route('Review.Declassify',$company) }}', datos).then(response => {
                    $('#post-'+id).remove();
                    if(response.data == 'eliminado'){
                        swal('Exito !', 'Se ha desclasificado', 'success');
                    }
                    else{
                        swal('Error !', 'La publicación ya se encuentra desclasificado', 'info');
                    }

                });
            }
        }


    </script>

@endsection