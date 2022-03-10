<div class="form-group">
    {{-- LA MEGACATEGORIA SE CONOCERA COMO CATEGORIA--}}
    {{ Form::label('name','Categoría *') }}
    <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
       data-content="En esta opción se permitirá crear o seleccionar una categoría que obtendrá los contenidos y sus temas en específico">
        <i class="fas fa-info-circle"></i>
    </a>
    <div class="input-group">
        <div id="divEmpresa">
            {{ Form::text('empresa',null,['class' => 'form-control', 'maxlength' => '80','id'=>'txtEmpresas', 'placeholder'=>'Categoría']) }}
        </div>

        <p class="text-danger">{{ $errors->first('name')}}</p>
        <div class="custom-control custom-checkbox m-2 ml-2" id="checkEmpresas">
            <input type="checkbox" class="custom-control-input" id="misEmpresas">
            <label class="custom-control-label " for="misEmpresas">Mis categorías</label>
        </div>
    </div>
</div>


<div class="form-group">
    {{--LA CATEGORIA SE CONOCERA COMO CONTENIDO--}}
    {{ Form::label('categoria','Contenidos *') }}
    <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
       data-content="En esta opción se debe de ingresar la información descriptiva que contendrá las páginas que se desea obtener desde Facebook">
        <i class="fas fa-info-circle"></i>
    </a>
    {{ Form::text('categoria',null,['class' => 'form-control', 'maxlength' => '80', 'placeholder'=>'Contenido']) }}
    <p class="text-danger">{{ $errors->first('categoria')}}</p>
</div>
<hr>

<div class="form-group">

    <div class="row" id="headsub">
        <label class="form-control col-3" id="subtema">Temas *
            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
               data-content="En esta opción se creará temas en específico acerca de la información que deseamos obtener desde Facebook">
                <i class="fas fa-info-circle"></i>
            </a>
        </label>
        <label class="form-control col-8" id="canal">
            Canal de telegram *
            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información"
               data-content="En esta opción se ingresara el número del canal de un grupo">
                <i class="fas fa-info-circle"></i>
            </a>
        </label>

    </div>

    <div id="divSubTemas">
        @if(empty($subcategories))
            <div class="row">
                <input name="subtema0" id="subtema" class="subtema form-control col-3" placeholder="Temas"/>
                <input name="canal0" id="canal" class="canal form-control col-4" placeholder="-10027570000" onblur="getChannel(this)"/>
                <input name="nombrecanal0" id="nombrecanal" class="canal form-control col-4" placeholder="Nombre del canal" onblur="getChannel(this)"/>
            </div>
        @else
            @foreach($subcategories as $subcategory)
                <div class="row">
                    {{ Form::text('subtema0',$subcategory->name,['class' => 'form-control col-3', 'maxlength' => '20', 'data-subcategoriaId'=>$subcategory->id]) }}
                    {{ Form::text('canal0',$subcategory->channel,['class' => 'form-control col-4', 'maxlength' => '20','onblur'=>"getChannel(this)"]) }}
                    {{ Form::text('nombrecanal0',$subcategory->nameTelegram,['class' => 'form-control col-4', 'maxlength' => '50','onblur'=>"getChannel(this)",'placeholder'=>"Nombre del canal"]) }}
                </div>
            @endforeach
        @endif
    </div>
    <div class="m-2">
        <p><span style="font-size:6px;">
            <a type="button" onclick="showsubtema()" class="btn btn-sm m-2" style="color: green"> Agregar tema
                <i class="fas fa-plus-square"></i>
            </a>
            </span></p>
    </div>
</div>

<hr class="m-3">


<div class="form-group">
    {{ Form::label('description','Descripción *') }}
    {{ Form::textarea('description',null,['class' => 'form-control', 'maxlength' => '200','rows'=>'4','require' ]) }}
    <p class="text-danger">{{ $errors->first('description')}}</p>
</div>


<div class="form-group">
    <a type="button" id="myBtn" onclick="submit()" class="btn btn-primary btn-block">Guardar categoría</a>
</div>

@section('script')
    <script>
        document.getElementById("myBtn").disabled = false;

        $(document).ready(function () {
            $("#empresa").on('keypress', function (e) {
                let regex = new RegExp("^[a-zA-Z ]*$");
                let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });

        });

        document.getElementById('misEmpresas').addEventListener('change', showEmpresas);

        function showsubtema() {
            let subtema = '<div class="row">' +
                '<input name="subtema0" data-subcategoriaId="0" class="form-control col-3" placeholder="Temas"/>' +
                '<input name="canal0" class="form-control col-4" placeholder="-10027570000" onblur="getChannel(this)"/>' +
                '<input name="nombrecanal0"  class=" form-control col-4" placeholder="Nombre del canal" onblur="getChannel(this)"/>' +
                '<a type="button" onclick="deletesubtema(this)" class="col-1" style="color: #4c110f"><i class="fas fa-minus-circle"></i></a>' +
                '</div>';
            $(divSubTemas).append(subtema);
        }


        function showEmpresas() {

            if (document.getElementById('misEmpresas').checked) {
                if ('{{$empresas->count()>0}}') {
                    let sel = '{{ Form::select('empresa',$empresas,'Seleccione una empresa',['class' => 'selectpicker form-group','id'=>'selEmpresas'])}}';
                    $(divEmpresa).html(sel);
                } else {
                    alert('No tienes empresas');
                    document.getElementById('misEmpresas').checked = false;
                    document.getElementById('checkEmpresas').hidden = true;
                }
            } else {
                @php($categorias??$categorias=['empresa'=>null])
                let sel = '{{ Form::text('empresa',($categorias)?$categorias['empresa']:null,['class' => 'form-control input', 'maxlength' => '20','id'=>'txtEmpresas']) }}';
                $(divEmpresa).html(sel);
            }
        }

        function deletesubtema(obj) {
            $(obj).parent().remove();
        }

        function submit() {
            const subtemas = [];
            let tema = document.getElementsByName('subtema0');
            let canal = document.getElementsByName('canal0');
            let nombre = document.getElementsByName('nombrecanal0');
            document.getElementById("myBtn").disabled = true;

            for (let i = 0; i < tema.length; i++) {
                let subtema = tema[i].value,
                    channel = canal[i].value,
                    nameTelegram = nombre[i].value,
                    id = tema[i].dataset.subcategoriaid;

                if (subtema.length === 0 || channel.length === 0 || nameTelegram.length === 0) {
                    swal('Opss', 'Valores incompletos en temas', 'error');
                    return false;
                }
                subtemas.push({
                    subtema,
                    channel,
                    nameTelegram,
                    id
                });

            }

            let data = {
                'subtemas': subtemas,
                'empresa': document.getElementsByName('empresa')[0].value,
                'categoria': document.getElementsByName('categoria')[0].value,
                'description': document.getElementsByName('description')[0].value,
            };

            if (data.empresa==='' || data.categoria==='' || data.description===''){
                swal('Error', 'Debe de completar los campos categoría, contenido y descripción', 'error');
                return false;
            }

            if (window.location.href.indexOf('edit') > -1) {
                data.id = idcategoria.value;
                axios.put('{{ route('Category.update') }}', data).then(response => {
                    window.location.href = "{{route('Category.index')}}";
                    showSmallAlert(response.data);
                });

            } else {
                axios.post('{{ route('Category.store') }}', data).then(response => {
                    window.location.href = "{{route('Category.index')}}";
                    showSmallAlert(response.data);
                });
            }

        }


            function showSmallAlert(msj) {
                let mensajero = document.getElementById('mensaje-alerta');
                let mensaje = '<div class="container">' +
                    '<div class="row">' +
                    '<div class="col-md-8 offset-md-2">' +
                    '<div class="alert alert-success">' + msj +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                $(mensajero).html(mensaje)
            }

            function getChannel(element) {
                axios.get('https://api.telegram.org/bot' + '{{env("TELEGRAM_TOKEN")}}' + '/getUpdates').then(response => {
                    let i = element.name.substr(-1);
                    let channels = response.data.result.filter(e => e.channel_post);
                    if (channels.length > 0) {
                        if (element.name.includes('nombrecanal')) {
                            indice = channels.findIndex(e => e.channel_post.chat.title.toUpperCase() == element.value.toUpperCase());
                            //indice=channels.findIndex(e => e.channel_post.chat.username.toUpperCase()==element.value.toUpperCase());
                            if (indice != -1) {
                                element.parentNode.children[1].value = channels[indice].channel_post.chat.id;
                                element.value = channels[indice].channel_post.chat.title;
                            } else {
                                return alertCanalTelegram();
                            }
                        } else {
                            indice = channels.findIndex(e => e.channel_post.chat.id == element.value);

                            if (indice != -1) {
                                element.value = channels[indice].channel_post.chat.id;
                                element.parentNode.children[2].value = channels[indice].channel_post.chat.title;
                            } else {
                                return alertCanalTelegram();
                            }
                        }
                    } else {
                        alertCanalTelegram();

                    }
                });
            }

            function alertCanalTelegram() {
                swal({
                    title: "Opps!",
                    text: "Sigue los siguientes pasos:" + "\n" +
                    "1. Crea el canal en Telegram" + "\n" +
                    "2 .Agrega el bot {{env('TELEGRAM_BOT')}} a tu canal" + "\n" +
                    "3 .Envia el siguiente mensaje 'bot ingresado' en el canal" + "\n" +
                    "4 .Ingresa el Nombre del canal",
                });
            }
    </script>
@endsection
