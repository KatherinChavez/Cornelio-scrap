@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            Clasificar Tweets
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Se contara con la opción de clasificar el tipo etiqueta de una publicación en específico">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h4>
                    </div>
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                    <div class="card-body">
                        <div class="card-deck" id="classificationTopics">
                            @if(count($classifications) > 0)
                                @foreach($classifications as $classification)
                                    <div id="sub-{{$classification->id}}" class="col-3 small mt-2" onclick="loadCategory(this);">
                                        <span  class="badge badge-success" style="width: 100%;border: hidden">{{$classification->name}}</span>
                                        <input type="hidden" value="0" id="var-{{$classification->id}}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <br>
                        <div class="row justify-content-center" id="posts">
                            @foreach($tweets as $tweet)
                                @if(!$tweet->classification)
                                    <div class="col-md-4" >
                                        <div class="card mb-4" style=" height: 450px;overflow: auto">

                                            <div class="card-body">
                                                <h3 style="color: #0A5A97">{{$tweet->name}}</h3><br>
                                                @if(isset($tweet->attachment))
                                                    @if($tweet->attachment->picture != null)
                                                        <img class="card-img-top img-fluid w-100" src="{{$tweet->attachment->picture}}" alt="">
                                                    @endif
                                                @endif
                                                <br>
                                                <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($tweet->created_time)->format('Y-m-d h:i:s')}}</p>
                                                <p class="card-text small">
                                                    @if($tweet->content)
                                                        {{$tweet->content}}
                                                    @endif
                                                    @if($tweet->expanded_url)
                                                        <br>
                                                        <b><a href="{{$tweet->expanded_url}}" target="_blank">Ver más</a></b>
                                                    @endif
                                                </p>
                                                <br>
                                                <button type="button" id="modal-{{$tweet->id_tweet}}" name="{{$tweet->author_id}}" class="btn btn-primary" style="width: 100%" onclick="modal(this)">Clasificar</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div id="paginacion" class="row justify-content-center">
                            {{$tweets->appends(request()->except('page'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CLASIFICAR-->
    <div id="categorizar" data-content='' name="" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content-->
                <div class="modal-header">
                    <h4 class="modal-title">Clasificar tema </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {{--De subcategoria a Temas--}}
                    <label><b>Temas</b></label>
                    <br>
                    <select class = 'form-control' id="subcategoriaModal" name="megacategoriaModal" style="padding-bottom:10px; padding-top: 10px; border-radius: 5px; "></select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-outline-primary" onclick="categorizar()" data-dismiss>Clasificar</button>
                </div>
            </div>
        </div>
    </div>

    {{--MODAL PARA NOTIFICAR CLASIFICACION--}}
    <div class="modal fade" tabindex="1" role="dialog" id="alertar-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Desea alertar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <h6>Seleccione una opción, para notificar por medio de WhatsApp o Telegram que
                        se ha clasificado el tweet, se notifica al número de celular o canal de Telegram
                        respectivo al tema seleccionado.</h6>
                    <input type="hidden" name="" id="hidden-post">
                    <input type="hidden" name="" id="hidden-sub">
                    <input type="hidden" name="" id="hidden-mega">
                    <input type="hidden" name="" id="hidden-user">
                    <input type="hidden" name="" id="hidden-page">
                    <br>
                    <div class="form-row align-items-center">
                        <div class="col-lg-6">
                            <button type="button" class="btn btn-success" style="width: 100%" onclick="whatsappGetNumeros()"> <i class="fab fa-whatsapp"></i> WhatsApp</button>
                        </div>
                        <div class="col-lg-6">
                            <button type="button" class="btn btn-info"    style="width: 100%" onclick="telegram()"> <i class="fab fa-telegram-plane"></i> Telegram</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('script')
    <script>
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        $(document).ready(loadTopics());

        /*----------------------------------------------------------- Classification -----------------------------------*/

        function modal(post) {
            console.log(post);
            let post_id = post.id.replace('modal-', '');
            let name    = post.name.replace('', '');
            let modal   = document.getElementById('categorizar');
            modal.setAttribute("data-content", post_id);
            modal.setAttribute("name", name);
            $("#categorizar").modal();
        }

        function categorizar() {
            let id_tweet     = $('#categorizar').attr("data-content"),
                page         = $('#categorizar').attr("name"),
                subcategoria = document.getElementById('subcategoriaModal').value,
                datos        = {subcategoria, id_tweet, page};
            console.log(datos);
            axios.post('{{ route('classificarionTwitter.classificationTweet') }}', datos).then(response => {
                swal('Exito', 'Se ha clasificado', 'success');
                Notification(id_tweet,page,subcategoria);
                $('.act').html('');
            });
        }

        /*----------------------------------------------------------- Load Topics --------------------------------------*/

        function loadTopics() {
            axios.post('{{ route('ClassifyCategory.subcategory') }}').then(response => {
                let consulta = response.data,
                    categoryLists = '<option selected value="0">Seleccione un tema...</option>';
                for (index in consulta) {
                    id   = consulta[index].id;
                    name = consulta[index].name;
                    categoryLists += '<option value="'+id+'">'+name+'</option>';
                    $('#subcategoriaModal').html(categoryLists);
                };
            });
        }

        /*----------------------------------------------------------- Load Category ------------------------------------*/

        function loadCategory(sub) {
            let idSub = sub.id;
                idSub = idSub.replace('sub-',''),
                start  = Base64.decode("<?php echo $_GET['inicio'] ?>"),
                end    = Base64.decode("<?php echo $_GET['final'] ?>");
            window.location = "{{ route('classificarionTwitter.getTopics') }}?topics_id="+Base64.encode(idSub)+"&inicio="+Base64.encode(start)+"&final="+Base64.encode(end)+" ";
            return false;
        }

        /*----------------------------------------------------------- Notification -------------------------------------*/

        function Notification(id_tweet,page,sub) {
            var r=confirm('Desea alertar?');
            if(r===true){
                document.getElementById('hidden-post').value     = id_tweet; //post
                document.getElementById('hidden-sub').value      = sub;      // sub
                document.getElementById('hidden-page').value     = page;     // sub
                document.getElementById('hidden-user').value    = user;    //user
                $('#alertar-modal').modal();
            }
            else{
                setTimeout(()=>{location.reload();}, 2000);
            }
        }

        /*----------------------------------------------------------- Telegram -----------------------------------------*/

        function telegram() {
            let sub      = document.getElementById('subcategoriaModal').value,
                tweet_id = document.getElementById('hidden-post').value,
                datos    = {sub, tweet_id};
            console.log(datos);

            axios.post('{{ route('classificationTweet.Telegram_sendTwitter',$company) }}', datos).then(response => {
                swal("Exito!", "Mensaje enviado", "success");
                setTimeout(()=>{location.reload();}, 5000);
            });
        }

        /*----------------------------------------------------------- whatsapp -----------------------------------------*/

        function whatsappGetNumeros() {
            let sub      = document.getElementById('subcategoriaModal').value,
                tweet_id = document.getElementById('hidden-post').value,
                datos    = {sub, tweet_id};
            axios.post('{{ route('classificarionTwitter.sendWhatsappClassification',$company) }}', datos).then(response => {
                console.log(response.data);
                if(response.data == 200){
                    swal('Exito !', 'Se ha enviado el mensaje', 'success');
                }else if(response.data == 500){
                    swal('Error !', 'No ha sido posible enviar el mensaje, por favor sincronice la instancia de WhatsApp en el sistema de Monitoreo', 'error');
                }
                else if(response.data == 400){
                    swal('Ops !', 'No ha sido posible enviar el mensaje, error desconocido', 'error');
                }
                else if(response.data == 403){
                    swal('Ops !', 'El tema no cuenta con número de teléfono para notificar', 'error');
                }
                setTimeout(()=>{location.reload();}, 5000);
            });
        }

    </script>
@endsection

