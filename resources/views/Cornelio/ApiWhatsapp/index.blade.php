@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('ClassifyTopics.index') }}" method="get">
                        <div class="card-header">
                            <h4>Administración registro de WAPIAD
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#ModalCrear">Crear</button>
                            </h4>
                        </div>

                        <div class="card-body table-responsive">
                            {{--<div class="input-group">--}}
                                {{--<input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">--}}
                                {{--<span class="input-group-prepend">--}}
                                    {{--<button type="submit" class="btn btn-outline-primary" id="seacrh">--}}
                                        {{--<i class="fas fa-search"></i> Buscar--}}
                                    {{--</button>--}}
                                {{--</span>--}}
                            {{--</div> --}}

                            <br>

                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Id cliente WAPIAD</th>
                                    <th>Instancia WAPIAD</th>
                                    <th>Clave sms </th>
                                    <th>Acciones</th>
                                    <th colspan="3">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($apis as $api)
                                    <tr>
                                        <td>{{ $api->id }}</td>
                                        <td>{{ $api->client_id}}</td>
                                        <td>{{ $api->instance}}</td>
                                        <td>{{ $api->key}}</td>
                                        <td>
                                            <div class="list-group-item-figure">
                                                <button type="button" onclick="show({{ $api->id }})" data-user="{{ $api->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                                <a onclick="confirmation(event)" href="./delete/{{$api->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
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
                            {{ $apis->appends($_GET)->links() }}
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
                    <h5 class="modal-title" id="exampleModalLabel">Crear registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>

                        <div class="form-group">
                            <label for="client_id">Indetificador cliente WAPIAD</label>
                            <input type="text" class="form-control" id="client_id"  placeholder="">
                        </div>

                        <div class="form-group">
                            <label for="instance">Instancia WAPIAD</label>
                            <input type="text" class="form-control" id="instance"  placeholder="">
                        </div>

                        <div class="form-group">
                            <label for="key">Clave SMS</label>
                            <input type="text" class="form-control" id="key"  placeholder="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_guardar" class="btn btn-primary">Crear registro</button>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL EDITAR --------------------------------------------------}}

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="client_id_e">Indetificador cliente WAPIAD</label>
                            <input type="text" class="form-control" id="client_id_e"  placeholder="">
                        </div>

                        <div class="form-group">
                            <label for="instance_e">Instancia WAPIAD</label>
                            <input type="text" class="form-control" id="instance_e"  placeholder="">
                        </div>

                        <div class="form-group">
                            <label for="key_e">Clave SMS</label>
                            <input type="text" class="form-control" id="key_e"  placeholder="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_editar" class="btn btn-primary">Editar registro</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        let id_api=0;
        document.getElementById('btn_editar').addEventListener('click',ModalEditar);
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
            let client_id=document.getElementById('client_id').value,
                instance=document.getElementById('instance').value,
                key=document.getElementById('key').value,
                data={client_id, instance, key};
            axios.post('{{ route('apiWhatsapp.store') }}', data).then(response => {
                console.log(response);
                if(response.data == '200'){
                    window.location = "{{ route('apiWhatsapp.index') }}";
                }
            }).catch(error=>{
                swal('Ops', 'No es posible crear un nuevo número de teléfono','warning');
            });
        }

        function show(id){
            let id_apiW = id ,
                data={id_apiW};
            id_api = id_apiW;
            axios.post("{{route('apiWhatsapp.edit')}}",data).then( response => {
                console.log(response, response.data);
                document.getElementById('client_id_e').value = response.data.client_id;
                document.getElementById('instance_e').value = response.data.instance;
                document.getElementById('key_e').value = response.data.key;
            });
        }

        function ModalEditar() {
            $("#showModal").modal('toggle');
            let id_apiW = id_api,
                client_id=document.getElementById('client_id_e').value,
                instance=document.getElementById('instance_e').value,
                key=document.getElementById('key_e').value,
                data={id_apiW, client_id, instance, key};

            axios.post('{{ route('apiWhatsapp.update') }}', data).then(response => {
                window.location = "{{ route('apiWhatsapp.index') }}";
            }).catch(error=>{
                swal('Ops', 'No es posible editar el número de teléfono','warning');
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
