@extends('layouts.app')

@section('styles')
    @include('Cornelio.Classification.Sentiment.Style')
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="col-lg-12"><span><i class="fab fa-facebook-messenger"></i></span> Conversaciones de Messenger
                </h3>
            </div>
            <div class="card-body">
                <div class="row col-lg-12">
                    <div class="col-lg-5">
                        <p class="h4">Tus conversaciones</p>
                        <div class="list-group list-group-messages list-group-flush" id="conversaciones"></div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card conversations">
                            <input id="conv_data" type="hidden" value="">
                            <div id="conversation-header"></div>
                            <div class="card-body conversations-body ml-3" id="mensajes">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="myModal" class="modal fade" data-sentiment="Neutral" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Reacciones</h2>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                        </div>
                        <div class="modal-body">
                            <div class="sentiment pull-right">
                                <p id="modal-sent"></p>
                            </div>
                            <div id="sentimiento"></div>
                            <div id="sentimientoPersonalisado" style="margin-top: 5px"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="">Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @endsection

        @section('script')
            <script>
                var user = "{{ Auth::user()->id }}";

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
                        '/me/accounts',
                        'GET',
                        {},
                        function (response) {
                            verificar(response.data);
                        }
                    );
                }

                function verificar(data) {
                    var page_id = "<?php echo $_GET['page'] ?>";
                    var contador = 0;
                    data.forEach(function (paginas, index) {
                        if (paginas.id == page_id) {
                            contador = 1;
                            generateSentiment();
                            getSentimentPer();
                            getconversations();
                        }
                    });
                }

                function getconversations() {
                    let datos = {};
                    page_id = "<?php echo $_GET['page'] ?>";
                    datos = {page_id, page_id};
                    axios.post('{{ route('SentimentInbox.conversation',$company) }}', datos).then(response => {
                        conversacion(response.data);
                    });
                }

                function conversacion(data) {
                    let conversacionesList = '';
                    admin = "<?php echo $_GET['page'] ?>";
                    data.forEach(function (conv, index) {
                        var conversacion = conv.conv_id;
                        var autor = conv.author_id;
                        conversacionesList +=
                            `<div class="list-group-item unread" id="` + conversacion + `" onclick="cargaMensajes(this)">
                                <div class="list-group-item-figure">
                                    <a class="user-avatar">
                                        <div class="avatar">
                                            <img src="https://avatars.dicebear.com/api/initials/'` + conv.author + `'.svg" class="avatar-img rounded-circle">
                                        </div>
                                    </a>
                                </div>
                                <div class="list-group-item-body pl-3 pl-md-4">
                                    <div class="row">
                                        <div class="col-12 col-lg-10">
                                            <h4 class="list-group-item-title">
                                                <a>` + conv.author + `</a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#conversaciones').append(conversacionesList);
                }

                function cargaMensajes(conv) {
                    let datos = {};
                    page = "<?php echo $_GET['page'] ?>";
                    conversacion = conv.id;
                    document.getElementById('conv_data').value = conversacion;
                    $('.convsel').removeClass('convsel');
                    $('#' + conversacion).addClass('convsel');
                    datos = {conversacion};
                    axios.post('{{ route('SentimentInbox.message',$company) }}', datos).then(response => {
                        generarMensajes(response.data)
                    });
                }

                function generarMensajes(conv) {
                    let mensajeList = "";
                    let header = "";
                    let receiver = "";
                    conv.forEach(function (mensaje, index) {
                        let author = mensaje.author;
                        author_id = mensaje.author_id;
                        fecha = mensaje.created_time;
                        msg = mensaje.message;
                        mensaje_id = mensaje.msg_id;
                        admin = "<?php echo $_GET['page'] ?>";

                        if (author_id == admin) {
                            mensaje_id = mensaje_id.replace('m_mid.$', '');

                            mensajeList +=
                                `<div class="message-content-wrapper">
                                    <div class="message message-out">
                                        <div class="message-body">
                                            <div class="message-content">
                                                  <div class="content">` + msg + `</div>
                                            </div>
                                            <div class="checkbox-inline">
                                                 <button id="` + mensaje_id + `" onclick="openModal(this)" class="btn btn-xs btn-round mt--2" style="position: relative">👍</button>
                                                 <label class="pull-right mt-1">
                                                      <input id="checkbox-` + mensaje_id + `" name="verificado" type="checkbox" onclick="comprobarCheck(this.id)"> Verificado</input>
                                                 </label>
                                            </div>
                                            <div class="date">` + fecha + `</div>
                                        </div>
                                    </div>
                                </div>`;
                        }

                        if (author_id != admin) {
                            mensaje_id = mensaje_id.replace('m_mid.$', '');
                            receiver = author
                            mensajeList +=
                                `<div class="message-content-wrapper">
                                     <div class="message message-in">
                                        <div class="avatar avatar-sm">
                                           <img src="https://avatars.dicebear.com/api/initials/'` + author + `'.svg" alt="..." class="avatar-img rounded-circle border border-white">
                                        </div>
                                        <div class="message-body">
                                            <div class="message-content">
                                                <div class="name">` + author + `</div>
                                                <div class="content">` + msg + `</div>
                                            </div>
                                            <div class="checkbox-inline">
                                                 <button id="` + mensaje_id + `" onclick="openModal(this)" class="btn btn-xs btn-round mt--2" style="position: relative">👍</button>
                                                 <label class="pull-right mt-1">
                                                      <input id="checkbox-` + mensaje_id + `" name="verificado" type="checkbox" onclick="comprobarCheck(this.id)"> Verificado</input>
                                                 </label>
                                            </div>
                                            <div class="date">` + fecha + `</div>
                                        </div>
                                    </div>
                                </div>`;
                        }
                        header =
                            `<div class="message-header">
                                <div class="message-title">
                                    <div class="user ml-2">
                                        <div class="avatar">
                                            <img src="https://avatars.dicebear.com/api/initials/'` + receiver + `'.svg" alt="..." class="avatar-img rounded-circle border border-white">
                                        </div>
                                        <div class="info-user ml-2">
                                            <span class="name">` + receiver + `</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                    });
                    $('#mensajes').html(mensajeList);
                    $('#conversation-header').html(header);
                    obtenerSentimiento();
                }

                function generateSentiment() {
                    var sentimientos = '<div class="sentimientos">';
                    sentimientos += '<img class="img" id="Positivo" data-dismiss="modal" name="Positive" src="https://cornel.io/analyzer/reaccions/positive.png" alt="Positivo" title="Positivo" class="sentimiento" onclick="actualizar(this)" style=" width:60px; vertical-align: middle">'
                    sentimientos += '<img class="img" id="Neutral" data-dismiss="modal" name="Neutral" src="https://cornel.io/analyzer/reaccions/neutral.png" alt="Neutral" title="Neutral" class="sentimiento" onclick="actualizar(this)" style=" width:60px; vertical-align: middle">'
                    sentimientos += '<img class="img" id="Negativo" data-dismiss="modal" name="Negativo" src="https://cornel.io/analyzer/reaccions/negative.png" alt="Negativo" title="Negativo" class="sentimiento" onclick="actualizar(this)" style=" width:60px; vertical-align: middle">'
                    sentimientos += '</div>';
                    $('#sentimiento').html(sentimientos);
                }

                function openModal(msg) {
                    var mensaje_id = msg.id;
                    var elemento = document.getElementById(mensaje_id);
                    var sentimiento = elemento.getAttribute('data-sentimen');
                    var modal = document.getElementById('myModal');
                    modal.setAttribute("data-content", mensaje_id);
                    $('.sel').removeClass('sel');
                    $('.selPerso').removeClass('selPerso');
                    if (sentimiento == 'Neutral') {
                        $('#modal-sent').addClass('neutral');
                        $('#Neutral').addClass('sel');
                    }
                    if (sentimiento == 'Positivo') {
                        $('#modal-sent').addClass('positivo');
                        $('#Positivo').addClass('sel');
                    }
                    if (sentimiento == 'Negativo') {
                        $('#modal-sent').addClass('negativo');
                        $('#Negativo').addClass('sel');
                    }
                    if (sentimiento != 'Positivo' && sentimiento != 'Neutral' && sentimiento != 'Negativo' && sentimiento != 'undefined') {
                        $('#modal-sent').addClass('perso');
                        $('#' + sentimiento).addClass('selPerso');
                    }
                    $("#myModal").modal();
                }

                function getSentimentPer() {
                    $('.personalizado').remove();
                    let datos = {};
                    page_id = "<?php echo $_GET['page'] ?>";
                    datos = {page_id, user};
                    axios.post('{{ route('ClassifyFeeling.personalizedFeeling',$company) }}', datos).then(response => {
                        generateSentimentPer(response.data);
                    });
                }

                function generateSentimentPer(data) {
                    var SentimentPerList = "";
                    var elemento = document.getElementById("myModal");
                    var sent = elemento.getAttribute("data-sentiment");
                    $("#sentimientoPersonalisado").html(SentimentPerList);
                    data.forEach(function (sentiment, index) {
                        if (sent == sentiment.sentiment) {
                            SentimentPerList += `<div id="` + sentiment.sentiment + `" data-dismiss="modal" data-content="` + sentiment.sentiment + `" class="personalizado selPerso" onclick="actualizar(this)">
                                        <spam>` + sentiment.sentiment + `</spam>
                                        </div>`;
                        }
                        if (sent != sentiment.sentiment) {
                            SentimentPerList += `<div id="` + sentiment.sentiment + `" data-dismiss="modal" data-content="` + sentiment.sentiment + `" class="personalizado" onclick="actualizar(this)">
                                        <spam>` + sentiment.sentiment + `</spam>
                                        </div>`;
                        }
                    });
                    $("#sentimientoPersonalisado").html(SentimentPerList);
                }
                function actualizar(sent) {
                    let datos = {};
                    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    conv_id = document.getElementById('conv_data').value;
                    modal = document.getElementById('myModal');
                    msg_id = modal.getAttribute("data-content");
                    sentimiento = sent.id;
                    datos = {conv_id, msg_id, sentimiento, user};
                    axios.post('{{ route('SentimentInbox.store',$company) }}', datos).then(response => {
                    });
                    msg_id = msg_id.replace('m_mid.$', '');
                    var elemento = document.getElementById(msg_id);
                    elemento.setAttribute('data-sentimen', sentimiento);
                    if (sentimiento == 'Neutral') {
                        $('#' + msg_id).removeClass('positivo');
                        $('#' + msg_id).removeClass('perso');
                        $('#' + msg_id).removeClass('negativo');
                        $('#modal-sent').removeClass('positivo');
                        $('#modal-sent').removeClass('perso');
                        $('#modal-sent').removeClass('negativo');
                        $('#' + msg_id).addClass('neutral');
                    }
                    if (sentimiento == 'Positivo') {
                        $('#' + msg_id).removeClass('neutral');
                        $('#' + msg_id).removeClass('perso');
                        $('#' + msg_id).removeClass('negativo');
                        $('#modal-sent').removeClass('neutral');
                        $('#modal-sent').removeClass('perso');
                        $('#modal-sent').removeClass('negativo');
                        $('#' + msg_id).addClass('positivo');
                    }
                    if (sentimiento == 'Negativo') {
                        $('#' + msg_id).removeClass('neutral');
                        $('#' + msg_id).removeClass('perso');
                        $('#' + msg_id).removeClass('positivo');
                        $('#modal-sent').removeClass('neutral');
                        $('#modal-sent').removeClass('perso');
                        $('#modal-sent').removeClass('positivo');
                        $('#' + msg_id).addClass('negativo');
                    }
                    if (sentimiento != 'Positivo' && sentimiento != 'Neutral' && sentimiento != 'Negativo' && sentimiento != 'undefined') {
                        $('#' + msg_id).removeClass('negativo');
                        $('#' + msg_id).removeClass('positivo');
                        $('#' + msg_id).removeClass('neutral');
                        $('#modal-sent').removeClass('neutral');
                        $('#modal-sent').removeClass('positivo');
                        $('#modal-sent').removeClass('negativo');
                        $('#' + msg_id).addClass('perso');
                    }
                }
                function storeSentiment() {
                    var elemento = document.getElementById('myModal');
                    var msg_id = elemento.getAttribute("data-content");
                    alert(msg_id);
                }
                function obtenerSentimiento() {
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    var conv_id = document.getElementById('conv_data').value;
                    $.ajax({
                        method: "POST",
                        url: " <?php echo route('SentimentInbox.sentimentInbox', $company); ?>",
                        data: "_token=" + CSRF_TOKEN +
                            "&conv_id=" + conv_id +
                            "&user_id=" + user,
                        success: function (data) {
                            if (data != "") {
                                generarSentimiento(data);
                            }
                        }
                    })
                }
                function generarSentimiento(data) {
                    data.forEach(function (conv, index) {
                        var msg_id = conv.msg_id,
                            sentimiento = conv.sentiment;
                        if (msg_id != "") {
                            msg_id = msg_id.replace('m_mid.$', '');
                            var elemento = document.getElementById(msg_id);
                            if (conv.estado == 1) {
                                var elemento = document.getElementById(msg_id);
                                $('#checkbox-' + msg_id).prop('checked', true);
                            }
                            elemento.setAttribute('data-sentimen', sentimiento);
                            if (sentimiento == 'Neutral') {
                                $('#' + msg_id).addClass('neutral');
                            }
                            if (sentimiento == 'Positivo') {
                                $('#' + msg_id).addClass('positivo');
                            }
                            if (sentimiento == 'Negativo') {
                                $('#' + msg_id).addClass('negativo');
                            }
                            if (sentimiento != 'Positivo' && sentimiento != 'Neutral' && sentimiento != 'Negativo' && sentimiento != 'undefined') {
                                $('#' + msg_id).addClass('perso');
                            }
                        }
                    });
                }
                function comprobarCheck(id) {
                    var estado = "";
                    if ($('#' + id).is(':checked')) {
                        estado = 1;
                    } else {
                        estado = 0;
                    }
                    actualizarVerificar(id, estado);
                }
                function actualizarVerificar(id, estado) {
                    let conv_id = document.getElementById('conv_data').value;
                    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    msg_id = id.replace('checkbox-', '');

                    datos = {conv_id, estado, user, msg_id};
                    axios.post('{{ route('SentimentInbox.status',$company) }}', datos).then(response => {
                        swal('Exito', 'Se ha actualizado la verficación', 'success');
                    });
                }
            </script>
            <style>
                #conversaciones {
                    height: 91vh;
                    overflow: auto;
                }
            </style>
@endsection
