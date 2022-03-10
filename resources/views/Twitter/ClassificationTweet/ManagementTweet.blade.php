<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        .highcharts-credits{display: none;}
    </style>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/8f70cb5d5a.js"></script>
    <title>Admin Reporte</title>
</head>
{{--bg-dark--}}
<body style="background-color: #205796!important; margin: auto;">
{{--container--}}
<div class="container">
    {{--row justify-content-center--}}
    <div class="row justify-content-center" >
        {{--col-md-12--}}
        <div class="col-md-12">
            {{--card mx-auto mt-5--}}
            <div class="card mx-auto mt-5" >
                {{--card-header--}}
                <div class="card-header">
                    <i class="fa fa-cogs"></i>
                    <strong style="color:#0f3760;"> Administrar Notificación</strong>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="card sm-6">
                                    <input type="hidden" id="compania" value="{{$compania}}">
                                    <input type="hidden" id="pagina" value="{{$tweet['name']}}">
                                    @if($attachment)
                                        <img src="{{$attachment['picture']}}" alt="" class="card-img-top img-fluid w-100">
                                    @endif

                                    <div class="card-body">
                                        <h6 class="card-title mb-1" style="color:#0f3760;">{{$tweet['name']}}</h6>
                                        <p class="card-text small">{{$tweet['content']}}</p>
                                        
                                        @if($tweet['expanded_url'])
                                            @if($tweet['expanded_url']!=null)
                                            <br><a href="{{$tweet['expanded_url']}}" target="_blank"><i class="fa fa-plus-circle"></i> Ver más</a>
                                            @endif
                                        @endif
                                    </div>
                                    <hr class="my-0">
                                    <div class="card-body py-2 small">
                                        <div class="mr-2 d-inline-block">
                                            <button type="button" onclick="whatsappGetNumeros(this.id)" id="wha-{{$tweet['id_tweet']}}" class="btn btn-success btn-small" title="WhatsApp" data-toggle="modal" data-target="#modalWha" data-page="{{$tweet['name']}}" data-mega="" data-sub="{{$subcategoria}}" >
                                                <i class="fa fa-whatsapp"></i>
                                            </button>
                                        </div>

                                        <div class="mr-2 d-inline-block">
                                            <button type="button" class="btn btn-primary btn-small" title="Telegram" onclick="telegram(this.id)" id="telegram-{{$tweet['id_tweet']}}" data-sub="{{$subcategoria}}">
                                                <i class="fa fa-telegram"></i>
                                            </button>
                                        </div>
                                        <div class="mr-2 d-inline-block">
                                            <a class="btn btn-warning btn-small" title="Ver" href="https://{{$tweet['link']}}" target="_blank">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                        <div class="mr-2 d-inline-block">
                                            <button type="button" name="lista" class="btn btn-danger btn-small" id="{{$tweet['id_tweet']}}" data-sub="{{$subcategoria}}" onclick="Desclasificar(this.id)" title="Desclasificar">
                                                <i class="fa fa-close"></i>
                                            </button>
                                        </div>
                                        <div class="mr-2 d-inline-block">
                                            <button type="button" class="btn btn-info btn-small" data-toggle="modal" data-target="#modalRecla" id="{{$tweet['id_tweet']}}" data-sub="{{$subcategoria}}" title="Reclasificar">
                                                <i class="fa fa-random"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="my-0">
                                    <div class="card-footer small text-muted">{{$tweet['created_time']}}</div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer" ><p style=" text-align: center; color:#0f3760;">Reporte Generado por <a href="https://goo.gl/4oMRBD" target="_blank">Agencia Digital de Costa Rica </a>- <a href="https://goo.gl/w2Stra" target="_blank">Cornel.io</a> Todos los Derechos Reservados</p></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
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
                <input type="hidden" id="post_id" value="{{$tweet['id_tweet']}}">
                <input type="hidden" id="subcategoria_id" value="{{$subcategoria}}">
                <div class="form-group">
                    <label for="contactos" class="col-form-label">Enviar a:</label>
                    <select class="form-control" id="contactos">
                        <option value="0">Seleccione un contacto...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="whatsapp()" data-dismiss="modal">Enviar</button>
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
                <input type="hidden" id="post_id" value="{{$tweet['id_tweet']}}">
                <input type="hidden" id="subcategoria_id" value="{{$subcategoria}}">
                <div class="form-group">
                    <label for="Sub" class="col-form-label">Tema</label>
                    <select class="form-control" id="Sub">
                        <option value="0" selected>Seleccione un tema</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{$subcategory->id}}" data-megacategoria="{{$subcategory->megacategory_id}}">{{$subcategory->name}}</option>
                        @endforeach
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

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/wordcloud.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
    $(document).ready(deshabilitar());

    function deshabilitar() {
        let datos = {};
        sub=document.getElementById('subcategoria_id').value;
        id=document.getElementById('post_id').value;
        datos = {sub, id};
        axios.post('{{ route('classificationTweet.ToDisableTwitter',$company) }}', datos).then(response => {
            console.log(response);
            if(response.data == 'eliminado'){
                document.getElementsByName("lista")[0].disabled = false;
            }
            else{
                document.getElementsByName("lista")[0].disabled = true;
                swal('Error !', 'La publicación se encuentra desclasificado', 'info');
            }

        });
    }

    /*----------------------------------------------------------- Telegram -----------------------------------------------------------------*/

    function telegram(id) {
        let datos = {};
        tweet_id=id.replace('telegram-','');
        sub=$("#"+id).data("sub");
        datos = {tweet_id,sub}
        console.log('telegram', datos);
        axios.post('{{ route('classificationTweet.Telegram_sendTwitter') }}', datos).then(response => {
            alert('Mensaje Enviado');
        });
    }

    /*----------------------------------------------------------- whatsapp -----------------------------------------------------------------*/

    function whatsappGetNumeros(id) {
        let datos = {};
        post_id=id.replace('telegram-','');
        mega=$("#"+id).data("mega");
        pag=$('#'+id).data('page');
        sub=$("#"+id).data("sub");
        compania=document.getElementById('compania').value;
        datos = {post_id, mega, sub, pag,compania};
        console.log(datos);
        axios.post('{{ route('whatsapp.Contact',$company) }}', datos).then(response => {
            listaContactos(response.data);
            //document.getElementById('subcategoria_id-wha').value=sub;
        });
    }

    function listaContactos(data) {
        let contatosList='',
            long=data.length;
        if(long>0){
            console.log(long);
            contatosList+=`<option value="0">Seleccione un contacto...</option>`;
            for (let i=0;i<long;i++){
                contatosList+=`<option value="`+data[i]['numeroTelefono']+`">`+data[i]['descripcion']+`</option>`;

            }
        }else{
            contatosList+='<h3>No tiene contactos asociados a la subcategoria</h3>';
        }
        console.log(contatosList);
        $('#contactos').html(contatosList);
    }

    function whatsapp() {
        let datos   = {},
            phone   = document.getElementById('contactos').value,
            tweet_id = document.getElementById('post_id').value,
            sub     = document.getElementById('subcategoria_id').value,
            pagina  = document.getElementById('pagina').value;
        if(phone != 0){
            datos = {phone, tweet_id, sub, pagina};
            console.log(datos);
            axios.post('{{ route('classificationTweet.Whatsapp_sendTwitter',$company) }}', datos).then(response => {
                console.log(response.data);
                if(response.data == 200){
                    swal('Exito !', 'Se ha enviado el mensaje', 'success');
                }else if(response.data == 500){
                    swal('Error !', 'No ha sido posible enviar el mensaje, por favor sincronice la instancia de WhatsApp en el sistema de Monitoreo', 'error');
                }
                else if(response.data == 400){
                    swal('Ops !', 'No ha sido posible enviar el mensaje, error desconocido', 'error');
                }
            });
        }
        else{
            swal('Ops !!', 'No se ha seleccionado ningún contacto', 'error');
        }
    }

    /*----------------------------------------------------------- Reclasificar -----------------------------------------------------------------*/

    function subcategoria() {
        let datos = {};
        //user_id=document.getElementById("user").value;
        mega=document.getElementById('Mega').value;
        subList='<option value="0" selected>Seleccione un tema</option>';
        datos = {user_id, mega};
        axios.post('{{ route('Report.ItemSubcategory') }}', datos).then(response => {
            let long=response.data.length;
            for (let i=0;i<long;i++){
                subList+='<option value="'+response.data[i]['id']+'">'+response.data[i]['name']+'</option>';
            }
            $('#Sub').html(subList);
        });
    }

    function reclasificar() {
        let datos        = {},
            subcategoria = document.getElementById('Sub').value,
            page         = document.getElementById('pagina').value,
            tweet_id     = document.getElementById('post_id').value,
            compania     = document.getElementById('compania').value;

        datos = {subcategoria, page, tweet_id, compania};
        console.log(datos);
        axios.post('{{ route('classificationTweet.ReclassifyTwitter',$company) }}', datos).then(response => {
            $('#telegram-'+tweet_id).attr('data-sub',  response.data.subcategoria_id);
            $('#'+tweet_id).attr('data-sub', response.data.subcategoria_id);
            document.getElementById("subcategoria_id").value=response.data.subcategoria_id;
            document.getElementsByName("lista")[0].disabled = false;
            swal('Exito !', 'Se ha reclasificado', 'success');

        });
    }

    /*----------------------------------------------------------- Desclasificar -----------------------------------------------------------------*/

    function Desclasificar(id) {
        let datos = {};
        mega=$("#"+id).data("mega");
        sub=$("#"+id).data("sub");
        r=confirm('Esta seguro que desas desclasificar esta publicación?');
        datos = {sub, id};
        console.log(datos);
        if(r==true){
            axios.post('{{ route('classificationTweet.DeclassifyTweet',$company) }}', datos).then(response => {
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
</body>
</html>
