@extends('layouts.app')

@section('content')
<div class="col-lg-12">
    <div class="justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Números de whatsapp
                    <a href="{{ route('whatsapp.create',$company) }}" class="btn btn-outline-primary float-right">
                        Agregar nuevo número
                    </a>
                        {{--<a href="{{ route('Category.index',$company) }}" class="btn btn-outline-danger float-right mr-2">--}}
                        {{--Volver--}}
                    {{--</a>--}}
                    </h4>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Temas </th>
                            <th>Número de teléfono</th>
                            {{--<th colspan="3">&nbsp;</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($sub as $Numwhatsapp)


                            <tr>
                                {{--<td>{{ $Numwhatsapp->numeroTelefono }}</td>--}}
                                {{--<td>{{ $Numwhatsapp->descripcion }}</td>--}}
                                <td>{{ $Numwhatsapp->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-light btn-border btn-round" onclick="ModalVer({{$Numwhatsapp->id}})" title="Tema">
                                        <i class="fas fa-list"></i>
                                    </a>
                                </td>
                                {{--<td width="10px">--}}
                                    {{--@php--}}

                                    {{--@endphp--}}
                                    {{--<a href="{{ route('whatsapp.edit', [$Numwhatsapp->id]) }}" class="btn btn-sm btn-outline-success">--}}
                                        {{--Editar--}}
                                    {{--</a>--}}
                                {{--</td>--}}

                                {{--<td width="10px">--}}
                                    {{--@can('whatsapp.edit')--}}
                                        {{--<a href="{{ route('whatsapp.edit', $Numwhatsapp->id) }}" class="btn btn-sm btn-outline-dark">--}}
                                            {{--Editar--}}
                                        {{--</a>--}}
                                    {{--@endcan--}}
                                {{--</td>--}}

                                {{--<td width="10px">--}}
                                    {{--@can('whatsapp.destroy')--}}
                                        {{--{!! Form::open(['route' => ['whatsapp.destroy', $Numwhatsapp->id],--}}
                                        {{--'method' => 'DELETE']) !!}--}}
                                        {{--<button class="btn btn-sm btn-outline-danger">Eliminar</button>--}}
                                        {{--{!! Form::close() !!}--}}
                                    {{--@endcan--}}
                                {{--</td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $sub->links() }}
                </div>

                <div id="reporte" class="modal fade bd-example-modal-lg" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <!-- Modal content-->
                            <div class="modal-header">
                                <h3 class="modal-title">Números de WhatsApp</h3>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div id="Contenido"></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-categorias').className+=' active';

        function statusChangeCallback(response) {
            if (response.status === 'connected') {

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function search() {
            let CategoriesList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};
            axios.post('{{ route('Category.search',$company) }}', datos).then(response => {
                CategoriesList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>numeros</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;
                let categoria = response.data;

                categoria.forEach(categoria => {
                    let id=categoria.id;

                    console.log(id);
                    CategoriesList += `<tr>
                        <td>`+ categoria.name+`</td>
                        <td>`+ categoria.description+`</td>
                        <td width="10px">
                            <a href="./Category/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./Category/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                        </td>
                </tr>`;
                });
                CategoriesList += `</tbody>
                            </table>`;
                document.getElementById('categories').innerHTML = CategoriesList;
            });
        }

        function ModalVer(id) {
            console.log(id);
            let datos = {'subcategoria': id},
                consulta = '',
                resultado = '';

            axios.post('{{route('whatsapp.ShowNumber')}}', datos).then(response => {
                resultado = response.data;
                console.log(resultado);

                consulta += `<table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Número</th>
                                        <th>Acciones</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>`;

                resultado.forEach(numero => {

                    let id = numero.id;
                    consulta += `<tr>
                        <td>` + numero.descripcion + `</td>
                        <td>` + numero.numeroTelefono + `</td>
                        <td>
                            <a href="./whatsapp/` + id + `/edit" class="btn btn-sm btn-light btn-border btn-round">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <a onclick="confirmation(event)" href="./whatsapp/` + id + `" class="btn btn-sm btn-danger btn-round">
                              <i class="far fa-trash-alt"></i>
                            </a>
                        </td>


                </tr>`;
                });
                consulta += `</tbody>
                            </table>`;

                $('#reporte').modal();
                $('#Contenido').html(consulta);
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
