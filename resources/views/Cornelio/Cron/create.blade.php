@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Crear ejecución de página</h4>
                    <button class="btn btn-sm btn-success float-right" id="btn-paginas">Mis paginas</button>
                </div>
                <div class="card-body">
                    <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                    <div class="form-group">
                        {{ Form::label('page_id','Id de la página *') }}
                        {{ Form::text('page_id',null,['class' => 'form-control', 'disabled']) }}
                        <p class="text-danger">{{ $errors->first('page_id')}}</p>
                    </div>
                    <div class="form-group">
                        {{ Form::label('page_name','Nombre de la página *') }}
                        {{ Form::text('page_name',null,['class' => 'form-control', 'disabled']) }}
                        <p class="text-danger">{{ $errors->first('page_name')}}</p>
                    </div>

                    <hr style="width:100%;text-align:left;margin-left:0">
                    <div class="row justify-content-center">
                        <div class="col-md-12 form-group">
                            <label>Seleccione el tiempo y la aplicación para extraer las publicaciones de la página</label>
                        </div>
                        <div class="col-md-6 form-group">
                            {{ Form::label('timePost','Periodo *') }}
                            {{ Form::select('timePost', ['5' => 'Cada 5 minutos',
                                                         '20' => 'Cada 20 minutos',
                                                         '45' => 'Cada 45 minutos',
                                                         '60' => 'Cada hora',
                                                         '180' => 'Cada tres hora',
                                                         '360' => 'Cada seis hora',
                                                         '540' => 'Cada nueve hora',
                                                         '1440' => 'Una vez al día',
                                                         '720' => 'Dos veces al día',
                                                         '4320' => 'Cada tres días',
                                                         '10080' => 'Una vez a la semana',
                                                         '21600' => 'Cada 15 días',
                                                         '43800' => 'Una vez al mes'],
                                                          null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}
                            {{--{{ Form::select('timePost', ['5' => 'Cada 5 minutos', '20' => 'Cada 20 minutos', '45' => 'Cada 45 minutos', '60' => 'Una vez por hora', '1440' => 'Una vez al día', '720' => 'Dos veces al día'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}--}}
                            <p class="text-danger">{{ $errors->first('time')}}</p>
                        </div>
                        <div class="col-md-6 form-group">
                            {{ Form::label('idPost','Aplicación *') }}
                            {{ Form::select('idPost',$app,null,['class'=>'form-control', 'placeholder'=>'Seleccione una aplicación', 'required'=>'required']) }}
                            <p class="text-danger">{{ $errors->first('subcategory_id')}}</p>
                        </div>
                    </div>

                    <hr style="width:100%;text-align:left;margin-left:0">
                    <div class="row justify-content-center">
                        <div class="col-md-12 form-group">
                            <label for="">Seleccione el tiempo y la aplicación para extraer las reacciones de la página</label>
                        </div>
                        <div class="col-md-6 form-group">
                            {{ Form::label('timeReaction','Periodo *') }}
                            {{ Form::select('timeReaction', ['5' => 'Cada 5 minutos',
                                                             '20' => 'Cada 20 minutos',
                                                             '45' => 'Cada 45 minutos',
                                                             '60' => 'Cada hora',
                                                             '180' => 'Cada tres hora',
                                                             '360' => 'Cada seis hora',
                                                             '540' => 'Cada nueve hora',
                                                             '1440' => 'Una vez al día',
                                                             '720' => 'Dos veces al día',
                                                             '4320' => 'Cada tres días',
                                                             '10080' => 'Una vez a la semana',
                                                             '21600' => 'Cada 15 días',
                                                             '43800' => 'Una vez al mes'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}
                            <p class="text-danger">{{ $errors->first('time')}}</p>
                        </div>
                        <div class="col-md-6 form-group">
                            {{ Form::label('idReaction','Aplicación *') }}
                            {{ Form::select('idReaction',$app,null,['class'=>'form-control', 'placeholder'=>'Seleccione una aplicación', 'required'=>'required']) }}
                            <p class="text-danger">{{ $errors->first('subcategory_id')}}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('limit_time','Tiempo limite para inactivar página *') }}
                        {{ Form::select('limit_time', ['60' => 'Una hora',
                                                         '180' => 'Tres horas',
                                                         '480' => 'Ocho horas',
                                                         '720' => 'Doce horas',
                                                         '1440' => 'Un día',
                                                         '4320' => 'Tres días',
                                                         '7200' => 'Cinco días',
                                                         '10080' => 'Siete días',
                                                         '21600' => 'Quince días',
                                                         '43200' => 'Treinta días'],
                                                          null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}
                        <p class="text-danger">{{ $errors->first('limit_time')}}</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('limit','Limite para extracción de publicación de Facebook *') }}
                        <input type="number" class="form-control" id="limit" placeholder="1" min="1" max="100">
                        <p class="text-danger">{{ $errors->first('limit')}}</p>
                    </div>

                    <button class="btn btn-sm btn-primary" onclick=store() >Guardar</button>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="misPaginas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mis páginas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="tabla" class="table-responsive">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

@endsection
@section('script')
    <script type="text/javascript">

        $("#btn-paginas").on("click",function () {
            $("#misPaginas").modal({backdrop: "static"});
            getPaginas();
        });

        function getPaginas() {
            let datos={};
            user_id=document.getElementById('user').value

            datos = {user_id};
            axios.post('{{ route('scrapsLast.page') }}', datos).then(response => {
                tablaPaginas(response.data);
            });
        }

        function tablaPaginas(data) {
            let tablist =`<table class="table table-striped table-hover" id="tablaPaginas">
                            <thead>
                                <tr>
                                    <th>Página id</th>
                                    <th>Página</th>
                                </tr>
                            </thead>
                            <tbody id="developers">`;

            data.forEach(function (page, index) {
                tablist+='<tr id="'+page.page_id+'" onclick="load(this.id)" data-pagina="'+page.page_name+'">' +
                            '<th>'+page.page_id+'</th>' +
                            '<th>'+page.page_name+'</th>' +
                        '</tr>';
            });
            tablist+='</tbody></table>';
            $("#tabla").html(tablist);
            $('#tablaPaginas').DataTable();
        }

        function load(id) {
            var pagina=$('#'+id).data("pagina");
            document.getElementById('page_id').value=id;
            document.getElementById('page_name').value=pagina;
            $("#misPaginas").modal("hide");
        }

        function store() {
            let datos = {};
                page_id = document.getElementById('page_id').value;
                page_name = document.getElementById('page_name').value;
                timePost = document.getElementById('timePost').value;
                timeReaction = document.getElementById('timeReaction').value;
                appReaction = document.getElementById('idReaction').value;
                appPost = document.getElementById('idPost').value;
                limit_time = document.getElementById('limit_time').value;
                limit = document.getElementById('limit').value;

                if(limit <= 100){
                    datos = {page_id, page_name, timePost,timeReaction, appPost,  appReaction, limit_time, limit};
                    axios.post('{{ route('Cron.store') }}', datos).then(response => {
                        window.location = "{{ route('Cron.index') }}" ;
                    }).catch(error=>{
                        swal('Ops', 'No es posible crear esta configuración, revisa los datos de la página','warning');
                    });
                }
                else{
                    swal('Ops', 'El limite de extracción de publicación no puede superar más de 100','error');
                }

        }

    </script>
@endsection
