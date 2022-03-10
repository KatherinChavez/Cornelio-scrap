@extends('layouts.app')

@section('styles')
    <style>
        .cortar{
            width:200px;
            padding:20px;
            text-overflow:ellipsis;
            white-space:nowrap;
            overflow:hidden;
            transition: all 1s;
        }
        .cortar:hover {
            height: 100%;
            white-space: initial;
            overflow:visible;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('ClassifyTwitter.index') }}" method="get">
                        <div class="card-header">
                            <h4>Clasificar tema para Twitter
                                @can('ClassifyTwitter/classify')
                                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#ModalCrear">Clasificar</button>
                                @endcan
                            </h4>
                        </div>

                        <div class="card-body table-responsive">
                            <div class="input-group">
                                <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                                <span class="input-group-prepend">
                                    <button type="submit" class="btn btn-outline-primary" id="seacrh">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </span>
                            </div> <br>

                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>Página</th>
                                        <th>Contenido</th>
                                        <th>Clasificado</th>
                                        <th>Acciones</th>
                                        <th colspan="3">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classifications as $classification)
                                        <tr>
                                            <td>{{ $classification->id }}</td>
                                            <td>{{ $classification['page']->name}}</td>
                                            @if(isset($classification->content))
                                                <td><div class="cortar">{{ $classification->content }}</div></td>
                                            @else
                                                <td>{{ ' '}}</td>
                                            @endif

                                            @if(isset($classification->name))
                                                <td>{{ $classification->name}}</td>
                                            @else
                                                <td>{{ ' '}}</td>
                                            @endif
                                            <td>
                                                <div class="list-group-item-figure">
                                                    <button type="button" onclick="show({{ $classification->id }})" data-user="{{ $classification->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                    <a onclick="confirmation(event)" href="./delete/{{$classification->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                        <i class="icon-close"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row justify-content-center">
                            {{--{{ $classifications->appends($_GET)->links() }}--}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL CREAR ---------------------------------------------------}}

    <div class="modal fade" id="ModalCrear"  tabindex="-1" role="dialog" aria-labelledby="ModalCrear" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Clasificar tema</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row align-items-center">
                            <div class="col-sm-6">
                                <label class="sr-only" for="start">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-sm-6">
                                <label class="sr-only" for="end">Fecha Final</label>
                                <input type="date" class="form-control" id="end" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('subcategory_id','Tema') }}
                            {{ Form::select('subcategory_id',$topics,null,['class'=>'form-control','placeholder'=>'Seleccione tema','required']) }}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_guardar" class="btn btn-primary">Clasificar tema</button>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL EDITAR --------------------------------------------------}}

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Clasificación de tema</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="content_edit">Contenido de la publicación</label>
                            <textarea class="form-control" id="content_edit"  rows="3" disabled></textarea>
                        </div>

                        <div class="form-group">
                            {{ Form::label('subcategory','Tema') }}
                            {{ Form::select('subcategory',$topics,null,['class'=>'form-control','placeholder'=>'Seleccione tema','required']) }}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_edit_classification" class="btn btn-primary">Editar clasificación</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        let id_c=0;
        document.getElementById('btn_edit_classification').addEventListener('click',ModalEditar);
        document.getElementById('btn_guardar').addEventListener('click',ModalCrear);

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        function ModalCrear() {
            $("#ModalCrear").modal('toggle');
            let start          = document.getElementById('start').value,
                end            = document.getElementById('end').value,
                subcategory_id = document.getElementById('subcategory_id').value;
            data               = {start, end, subcategory_id};
            axios.post('{{ route('ClassifyTwitter.classify') }}', data).then(response => {
                //window.location = "{{ route('ClassifyTwitter.index') }}";
            }).catch(error=>{
                swal('Ops', 'No es posible crear una nueva palabra','warning');
            });

        }

        function show(id){
            let id_classification = id ,
                data              = {id_classification};
                id_c              = id_classification;
            axios.post("{{route('ClassifyTwitter.edit')}}",data).then( response => {
                console.log(response);
                document.getElementById('content_edit').value = response.data[0].content;
                document.getElementById('subcategory').value  = response.data[0].subcategoria_id;
            });
        }

        function ModalEditar() {
            $("#showModal").modal('toggle');
            let id_classification = id_c,
                subcategory       = document.getElementById('subcategory').value,
                data              = {id_classification, subcategory};
            axios.post('{{ route('ClassifyTwitter.update') }}', data).then(response => {
                window.location = "{{ route('ClassifyTwitter.index') }}";
            }).catch(error=>{
                swal('Ops', 'No es posible editar la palabra','warning');
            });
        }

        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        swal("Exito! Se ha eliminado de forma exitosa!", {
                            icon: "success",
                        });
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha eliminado!", "info");
                    }
                });
        }
    </script>
@endsection
