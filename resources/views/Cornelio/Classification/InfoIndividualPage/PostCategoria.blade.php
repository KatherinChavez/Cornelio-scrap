@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        Publicaciones en Contenidos
                        <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Clasifica la publicación de una categoría en específico">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h4>
                </div>
                <br>
                <div class="card-body">
                    <input id="user"type="hidden" value="{{ Auth::user()->id }}">
                    <input id="categoriapoder" type="hidden">

                    <div id="sectionMegacategoria" class="row">
                    </div>

                    <br><br>
                    <div class="row justify-content-center" >

                        @foreach($posts as $post)
                            @if(!$post['classification_category'])
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <h3 class="pl-3 pt-3">{{$post->page_name}}</h3>

                                    <a class="pl-3" href="https://fb.com/{{$post->post_id}}" target="_blank" >Ir a la Publicación</a>

                                    @if($post->attachment->picture != null)
                                        <img class="card-img-top img-fluid w-100" src="{{$post->attachment->picture}}" alt="">
                                    @elseif ($post->attachment->url != null)
                                        <img class="card-img-top img-fluid w-100" src="{{$post->attachment->url}}" alt="">
                                    @elseif($post->attachment->videos != null)
                                        <img class="card-img-top img-fluid w-100" src="{{$post->attachment->video}}" alt="">
                                    @endif

                                    <div class="card-body">
                                        <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($post->created_time)->format('Y-m-d h:i:s')}}</p>
                                        <p class="card-text small">
                                            @if($post->content)
                                                {{$post->content}}
                                            @endif
                                            @if(isset($post))
                                                <br><a href="{{$post->attachment->url}}" target="_blank">{{$post->attachment->tittle}}</a>
                                            @endif
                                        </p>

                                        <hr class="my-0">
                                        <button type="button" id="modal-{{$post->post_id}}" class="btn btn-outline-primary" style="width: 100%" onclick="modal(this)">Clasificar {{$post->page_name}}</button>

                                    </div>


                                </div>
                            </div>
                            @endif
                        @endforeach

                    </div>
                    <div class="row justify-content-center">
                        {{$posts->appends(request()->except('page'))->links()}}
                    </div>


                    <!-- Modal crear-->
                    <div id="subcategoria" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Crear temas</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label>Nombre de la etiqueta</label>
                                    <br>
                                    <input type="text" id="subcategoriamodal" name="subcategoria" class = 'form-control' style="text-align: left;">
                                    <br>
                                    <br>
                                    <label>Descripción de la etiqueta</label>
                                    <br>
                                    <textarea id="detalle" name ="detalle" class = 'form-control' style="text-align: left;"></textarea>
                                    <br>
                                    <br>
                                    <br>
                                    <label>Asignada a la categoría</label>
                                    <br>
                                    <select class = 'form-control' id="megacategoria" name="megacategoria" style="padding-bottom:10px; padding-top: 10px; border-radius: 5px; "></select>
                                    <br>
                                    <br>
                                    <label>Asignada al contenido</label>
                                    <br>
                                    <select class = 'form-control' id="categoriamodal" name="categoria" style="padding-bottom:10px; padding-top: 10px; border-radius: 5px; "></select>
                                    <br>
                                    <br>
                                    <label>Canal de Telegram</label>
                                    <br>
                                    <input type="text" id="channel" name="channel" class = 'form-control' style="text-align: left;">
                                    <br>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="guardarpoder()" data-dismiss="modal">Crear etiqueta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal clasificar -->
                    <div id="categorizar" data-content='' class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <!-- Modal content-->
                                <div class="modal-header">
                                    <h4 class="modal-title">Clasificar tema </h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    {{--De subcategoria a Temas--}}
                                    <label>Temas</label>
                                    <br>
                                    <select class = 'form-control' id="subcategoriaModal" name="megacategoriaModal" style="padding-bottom:10px; padding-top: 10px; border-radius: 5px; "></select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="categorizar()" data-dismiss>Categorizar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="1" role="dialog" id="alertar-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Desea alertar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body">
                <input type="hidden" name="" id="hidden-post">
                <input type="hidden" name="" id="hidden-sub">
                <input type="hidden" name="" id="hidden-mega">
                <input type="hidden" name="" id="hidden-user">
                <button type="button" class="btn btn-success col-6" onclick="whatsappGetNumeros()">WhatsApp</button>
                <button type="button" class="btn btn-info col-6" onclick="telegram()">Telegram</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection

@section('script')
    <script>
        var pageAccessToken='';
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn(response);
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        function isLogedIn(response) {
            // count();
            cargarPost();
            cargarMegacategorias();
            cargarData();
            cargarSubcategorias2();
        }


        function cargarData() {
            let datos = {};
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                user=document.getElementById('user').value;
            datos = {user};
            axios.post('{{ route('ClassifyCategory.Category') }}', datos).then(response => {
                misCategorias(response.data);
            });
        }

        //Las categorias se conoceran como contenido
        function misCategorias(data) {
            var consulta = data;
            var categoryLists = '<option selected value="0">Seleccione un contenido...</option>';

            for (var index in consulta) {
                var id=consulta[index].id;
                var name=consulta[index].name;

                categoryLists += '<option value="'+id+'">'+name+'</option>';
                $('#paginas').html(categoryLists);
                $('#categoriamodal').html(categoryLists);


            };
        }
        function cargarPost(pagina) {
            let datos = {},
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                categoria="<?php echo $_GET['id']; ?>";
                user=document.getElementById("user").value;
                inicio="<?php echo $_GET['inicio']; ?>";
                final="<?php echo $_GET['final']; ?>";
                desde=(pagina*10)-10;
                //desde=(pagina*1)-10;
            datos = {categoria,user,inicio,final};
            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.SelectCategory'); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&categoria="+categoria+
                "&inicio="+inicio+
                "&final="+final+
                "&desde="+desde+
                "&cuanto=30",
                success: function (data) {
                //if((data).length>0){
                    var consulta=data;
                    for (var index in consulta) {
                        var fecha=consulta[index].created_time;
                        var idPage=consulta[index].page_id;
                        var idPost=consulta[index].post_id;
                        var page=consulta[index].page_name;
                        var contenido=consulta[index].content;
                        var picture = consulta[index].picture;
                        var url = consulta[index].url;
                        var video = consulta[index].video;
                        generatePostLists(contenido,fecha,idPost,page,picture,video,url);
                    }
//                }else{
//                    swal("Sin datos!", "Por favor seleccione otra fecha!", "warning");
//                }
            }
            })

        }

        function generatePostLists(contenido,fecha,post,page,picture,video,url) {
            let posts,message,
                postList='';
                inicio="<?php echo $_GET['inicio']; ?>";
                final="<?php echo $_GET['final']; ?>";

                postList += `<div class="col-md-6">
                            <div class="card mb-3">
                            <h3>`+page+`</h3>
                            <a href="https://fb.com/`+post+`" target="_blank" >Ir a la Publicación</a>`;
                            if(picture != null){
                                postList += `<img class="card-img-top img-fluid w-100" src="`+picture+`" alt="">`;
                            }
                            if(video != null){
                                postList += `<img class="card-img-top img-fluid w-100" src="`+video+`" alt="">`;
                            }
                            else if(url != null){
                                postList += `<img class="card-img-top img-fluid w-100" src="`+url+`" alt="">`;
                            }
                            postList += `<div class="card-body">
                            <p class="card-title mb-1 small">`+fecha+`</p>
                            <p class="card-text small">`+contenido+`</p>
                            <button type="button" id="modal-`+post+`" class="btn btn-outline-primary" style="width: 100%" onclick="modal(this)">Clasificar `+ page +`</button>
                            </div>
                            </div>
                            `;
            $('#posts').append(postList);

        }
        function modal(post) {
            var post_id=post.id.replace('modal-','');
            var modal=document.getElementById('categorizar');
            modal.setAttribute("data-content", post_id);
            $("#categorizar").modal();
        }
        //Las subcategoria se conocera como etiqueta
        $( "#megacategoriaModal" ).change(function() {
            let datos = {};
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                user=document.getElementById("user").value;
                Megacategoria=document.getElementById('megacategoriaModal').value;

            datos = {user,Megacategoria};
            axios.post('{{ route('ClassifyCategory.subcategory') }}', datos).then(response => {
                var consulta = response.data;
                var categoryLists = '<option selected value="0">Seleccione una etiqueta...</option>';
                for (var index in consulta) {
                    var id=consulta[index].id;
                    var name=consulta[index].name;
                    categoryLists += '<option value="'+id+'">'+name+'</option>';
                    $('#subcategoriaModal').html(categoryLists);
                }
            });

        });

        function cargarMegacategorias() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var user=document.getElementById("user").value;
            $.ajax({
                method: "POST",
                //url:"<?php echo route('ClassifyCategory.selectSubcategory'); ?>",
                url:"<?php echo route('ClassifyCategory.subcategory'); ?>",
                data:"_token="+CSRF_TOKEN+
                "&megacategoria_name=todas"+
                "&user_id="+user,
                success:function (data) {
                    var consulta=data;
                    generatecomboSubcategoria(data);
                    for (var index in consulta) {
                        var id=consulta[index].id;
                        var name=consulta[index].name;
                        var detail=consulta[index].description;
                        generarMegacategoriasList(id,name,detail);
                    }
                }
            })
        }

        function generatecomboSubcategoria(data) {
            var consulta = data;
            var categoryLists = '<option selected value="0">Seleccione un tema...</option>';
            for (var index in consulta) {
                var id=consulta[index].id;
                var name=consulta[index].name;
                categoryLists += '<option value="'+id+'">'+name+'</option>';
                $('#subcategoriaModal').html(categoryLists);
            };
        }

        function minimizar(id) {
            var clase=($('#mega-'+id).hasClass('mini'));
            if(clase==true){
                $('#mega-'+id).removeClass('mini');
            }else{
                $('#mega-'+id).addClass('mini');
            }
        }
        function ver(id) {
            window.open(
                'validarReporte/'+id,
                '_blank' // <- This is what makes it open in a new window.
            );
        }

        function generarMegacategoriasList(id,name,detail) {
            var megaList=`
            <button type="button" class="btn btn-outline-primary float-right mt-2" style="width: 20%; cursor:pointer" data-toggle="modal" data-target="#subcategoria">Crear</button>
                        `;

            var megaList2=`<div id="mega-`+id+`" class="act "></div>
                            <div id="postCategorizados`+id+`" class="megacategoria"></div>
                            </div>
                            `;
//            $('#section-megacategoria').append(megaList);
            $('#sectionMegacategoria').append(megaList2);
        }

        function cargarSubcategorias2(value) {
            let datos = {};
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                user=document.getElementById("user").value;
                categoria="<?php echo $_GET['id']; ?>";
            datos = {user, categoria};
            axios.post('{{ route('ClassifyCategory.selectSubcategory') }}', datos).then(response => {
                var consulta = response.data;
                $('#sectionMegacategoria').html('');
                for (var index in consulta) {
                    var id=consulta[index].id;
                    var name=consulta[index].name;
                    var detail=consulta[index].detail;
                    var megacategoria=consulta[index].megacategory_id;
                    generarSubgacategoriasList(id,name,megacategoria);
                }
            });
        }

        function generarSubgacategoriasList(id,name,megacategoria) {
            var subList=`<div style="cursor: pointer" id="sub-`+id+`" class="col-2 small mt-2" onclick="cargarCategorizado(this);" data-content="`+megacategoria+`" ondrop="drop(event)" ondragover="allowDrop(event)">
                         <span  class="badge badge-success" style="width: 100%;border: hidden">`+name+`</span>
                         <input type="hidden" value="0" id="var-`+id+`">
                         </div>`;
            $('#sectionMegacategoria').append(subList);
        }

        function cargarCategorizado(sub) {
            var idSub=sub.id;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var idMega=$('#'+idSub+'').attr("data-content");
            var user=document.getElementById("user").value;
            idSub=idSub.replace('sub-','');
            window.open("CategorySentiment?categoria="+idSub);
            return false;
        }

        function getNext() {
            FB.api(
                loadMore,
                function(response) {
                    generatePostLists(response);
                    showHidePagination(response);
                }
            );
        }

        function cerrar(idMega) {
            $('#postCategorizados'+idMega+'').remove();

        }

        function categorizar() {
            var idPost=$('#categorizar').attr("data-content");
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var sub=document.getElementById('subcategoriaModal').value;
            var cad='_',
                pos=idPost.indexOf(cad),
                idPagina=idPost.substr(0,pos);
            var user=document.getElementById("user").value;
            $('#'+idPost).remove();
            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.classification'); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&post_id="+idPost+
                "&page_id="+idPagina+
                "&subcategoria_id="+sub+
                "&user_id="+user,
                success: function (data) {
                   swal('Exito', 'Se ha clasificado', 'success');
                    procesar(idPost,sub);
                    $('.act').html('');
                }
            });
        }

        function guardarpoder() {
            let datos = {};
                CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                mega=document.getElementById('megacategoria').value;
                detalle=document.getElementById('detalle').value;
                sub=document.getElementById('subcategoriamodal').value;//name
                categoria=document.getElementById('categoriamodal').value;
                channel=document.getElementById('channel').value;
                user=document.getElementById('user').value;
            datos = {mega, detalle, sub, categoria, channel, user};
            axios.post('{{ route('ClassifyCategory.sub_Category') }}', datos).then(response => {
                swal("Exito!", "Se guardo la subcatergoría", "success");
            });
        }

        function procesar(idPost,idSub) {
            //var idMega=$('#'+idSub+'').attr("data-content");
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var sub=idSub.replace("sub-", "");
            var valor=document.getElementById("var-"+sub);
            var cad='_',
                pos=idPost.indexOf(cad),
                subCadena=idPost.substr(0,pos);
            var idPagina=subCadena;
            var user=document.getElementById("user").value;
            $.ajax({
                method: "POST",
                url: "<?php echo route('ClassifyCategory.getClassification'); ?>",
                data: "_token=" + CSRF_TOKEN +
                "&post_id="+idPost+
                "&page_id="+idPagina+
                "&subcategoria_id="+sub+
                "&user_id="+user,
                success: function (data) {
                    cargarSubcategorias2(data);

                }
            });
            alertar(idPost,sub,user);
        }

        function alertar(idPost,idMega,sub,user) {
            var r=confirm('Desea alertar?');
            if(r===true){
                document.getElementById('hidden-post').value=idPost; //post
                document.getElementById('hidden-mega').value=idMega; //mega
                document.getElementById('hidden-sub').value=sub;     // sub
                document.getElementById('hidden-user').value=user;   //user
                $('#alertar-modal').modal();
            }
        }
        /*----------------------------------------------------------- Telegram -----------------------------------------------------------------*/

        function telegram() {
            let sub=document.getElementById('subcategoriaModal').value;

            let categoria ="<?php echo $_GET['id']; ?>",
                post_id= document.getElementById('hidden-post').value;
            datos = {sub,categoria,post_id};
            axios.post('{{ route('ClassifyCategory.TelegramCategory',$company) }}', datos).then(response => {
                alert('Mensaje Enviado');
            });
        }

        /*----------------------------------------------------------- whatsapp -----------------------------------------------------------------*/

        function whatsappGetNumeros() {
            let datos = {};
            let sub=document.getElementById('subcategoriaModal').value;

            let categoria ="<?php echo $_GET['id']; ?>",
                post_id= document.getElementById('hidden-post').value;
            datos = {sub,categoria,post_id};
            axios.post('{{ route('ClassifyCategory.SendCategory',$company) }}', datos).then(response => {

            });
        }

    </script>
@endsection
