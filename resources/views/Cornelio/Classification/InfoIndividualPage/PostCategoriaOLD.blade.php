@extends('layouts.app')

@section('content')
    <div class="container">
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
                        {{--<div id="">--}}
                        {{--<button type="button" class="btn btn-outline-primary float-right" style="width: 20%" data-toggle="modal" data-target="#subcategoria">Crear</button>--}}
                        {{--</div> --}}
                    </div>
                    <br>
                    <div class="card-body">
                        <input id="user"type="hidden" value="{{ Auth::user()->id }}">
                        <input id="categoriapoder" type="hidden">
                        <div id="sectionMegacategoria" class="row">
                        </div>

                        </br>
                        </br>
                        <div class="row justify-content-center" id="posts">
                        </div>

                        <!-- <div id="paginacion" class="row justify-content-center"></div> -->

                        <!-- <div class="row justify-content-center">
                            <input id="load-more" type="button" onclick="getNext()" class="btn btn-lg btn-success" value="Load More">
                        </div> -->

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
                                        <h4 class="modal-title">Categorizar</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        {{--De megacategoria a categoria--}}
                                        <label>Categoria</label>
                                        <br>
                                        <select class = 'form-control' id="megacategoriaModal" name="megacategoriaModal" style="padding-bottom:10px; padding-top: 10px;border-radius: 5px; "></select>
                                        <br>
                                        <br>
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

        function count(){
            let datos = {},
                categoria="<?php echo $_GET['id']; ?>";
            user=document.getElementById("user").value;
            inicio="<?php echo $_GET['inicio']; ?>";
            final="<?php echo $_GET['final']; ?>";

            datos = {categoria,user,inicio,final};
            axios.post('{{ route('ClassifyCategory.CountSelectCategory') }}', datos).then(response => {
                respuesta = (response.data);
                numeroPaginas = respuesta / 10;
                // paginacion(numeroPaginas);

            });
        }

        function paginacion(numeroPaginas) {
            var i;
            var contenido = '<nav aria-label="Page navigation" style="width:87%; margin:5% 5% 5% 5%">';
            contenido += '        <ul class="pagination">';
            contenido += '            <li>';
            contenido += '                <a id="1" href="#" onclick="cargarPost(1)" aria-label="Previous">';
            contenido += '                    <span aria-hidden="true">&laquo;</span>';
            contenido += '                </a>';
            contenido += '            </li>';
            for (i = 1; i <= numeroPaginas; i++) {
                contenido += '            <li><a id="' + i + '" style=" width:35px; padding-top: 6px !important; text-align: center;" href="#" onclick="cargarPost(' + i + ')">' + i + '</a></li>';

            }
            contenido += '            <li>';
            contenido += '                <a href="#" onclick="cargarPost(' + numeroPaginas + ')" aria-label="Next">';
            contenido += '                    <span aria-hidden="true">&raquo;</span>';
            contenido += '                </a>';
            contenido += '            </li>';
            contenido += '        </ul>';
            contenido += '    </nav>';
            $("#paginacion").html(contenido);
            // document.getElementById("1").click();

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
                url:"<?php echo route('ClassifyCategory.SelectMegacategory'); ?>",
                data:"_token="+CSRF_TOKEN+
                    "&megacategoria_name=todas"+
                    "&user_id="+user,
                success:function (data) {
                    var consulta=data;
                    generatecomboMegacategoria(data);
                    for (var index in consulta) {
                        var id=consulta[index].id;
                        var name=consulta[index].name;
                        var detail=consulta[index].description;
                        generarMegacategoriasList(id,name,detail);
                    }
                }
            })
        }
        //Megacategoria se conocera como la categoria
        function generatecomboMegacategoria(data) {
            var consulta = data;
            var categoryLists = '<option selected value="0">Seleccione una categoría...</option>';
            for (var index in consulta) {
                var id=consulta[index].id;
                var name=consulta[index].name;
                categoryLists += '<option value="'+id+'">'+name+'</option>';
                $('#megacategoria').html(categoryLists);
                $('#megacategoriaModal').html(categoryLists);
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
            <button type="button" class="btn btn-outline-primary float-right" style="width: 20%" data-toggle="modal" data-target="#subcategoria">Crear</button>
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
            var subList=`<div id="sub-`+id+`" class="col-2 small" onclick="cargarCategorizado(this);" data-content="`+megacategoria+`" ondrop="drop(event)" ondragover="allowDrop(event)">
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
            var idMega=document.getElementById('megacategoriaModal').value;
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
                    "&megacategoria_id="+idMega+
                    "&subcategoria_id="+sub+
                    "&user_id="+user,
                success: function (data) {
//                    swal('Exito', 'Se ha clasificado', 'success');
                    procesar(idPost,sub, idMega);
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
                swal("Exito!", "Se guardo la subcatergoria", "success");
            });
        }

        function procesar(idPost,idSub, idMega) {
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
                    "&megacategoria_id="+idMega+
                    "&subcategoria_id="+sub+
                    "&user_id="+user,
                success: function (data) {
                    cargarSubcategorias2(data);

                }
            });
            alertar(idPost,idMega,sub,user);
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
            let sub ="<?php echo $_GET['id']; ?>";
            datos = {sub};
            axios.post('{{ route('ClassifyCategory.TelegramCategory',$company) }}', datos).then(response => {
                alert('Mensaje Enviado');
            });
        }

        /*----------------------------------------------------------- whatsapp -----------------------------------------------------------------*/

        function whatsappGetNumeros() {
            let datos = {};
            sub ="<?php echo $_GET['id']; ?>";
            datos = {sub};
            axios.post('{{ route('ClassifyCategory.SendCategory',$company) }}', datos).then(response => {

            });
        }

    </script>
@endsection
