@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('Cron.index') }}" method="get">
                        <div class="card-header">
                            <h4>Administrar ejecución de página
                            @can('Cron.create')
                                <a href="{{ route('Cron.create') }}" class="btn btn-sm btn-primary float-right">
                                    Crear
                                </a>
                            @endcan</h4>

                        </div>
                        <div class="card-body table-responsive">
                            <div class="input-group">
                                <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                                <span class="input-group-prepend">
                                <button type="submit" class="btn btn-outline-primary" id="seacrh">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            </span>
                            </div>
                                <table class="table table-striped table-hover ">
                                    <thead>
                                    <tr>
                                        <th>Página id</th>
                                        <th>Página</th>
                                        {{--<th>Minutos</th>--}}
                                        {{--<th>Ultimo scrap</th>--}}
                                        <th>Acciones</th>
                                        <th colspan="3">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($cron as $crons)
                                        @php
                                            //dd($crons, $crons->posts,$crons->posts->created_at);
                                        @endphp
                                        <tr>
                                            <td>{{ $crons->page_id}}</td>
                                            <td>{{ $crons->page_name}}</td>
                                            {{--<td>{{ $crons->time}}</td>--}}
                                            {{--<td>{{ $crons->posts->created_at}}</td>--}}

                                            <td>
                                                <a href="{{ route('Cron.edit', $crons->id) }}" class="btn btn-sm btn-icon btn-round btn-success mt-3">
                                                    <i class="icon-pencil"></i>
                                                </a>

                                                <a onclick="confirmation(event)" href="./Cron/delete/{{$crons->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                    <i class="icon-close"></i>
                                                </a>

                                                @if($crons->status == 0)
                                                    <a onclick="confirmationPlay(event)"href="{{ route('Cron.play', $crons->id) }}" class="btn btn-sm btn-icon btn-round btn-warning mt-3" title="Activar página">
                                                        <i class="fas fa-play"></i>
                                                    </a>

                                                @elseif($crons->status == 1)
                                                    <a onclick="confirmationStop(event)"href="{{ route('Cron.stop', $crons->id) }}" class="btn btn-sm btn-icon btn-round btn-warning mt-3" title="Inactivar página">
                                                        <i class="fas fa-stop"></i>
                                                    </a>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        <div id="paginacion" class="row justify-content-center">
                            {{ $cron->appends($_GET)->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

        function confirmationStop(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡Deseas inactivar la página seleccionada!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        //swal("Exito! Se ha eliminado de forma exitosa!", {
                        //    icon: "success",
                        //});
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha inactivado la página!", "info");
                    }
                });
        }

        function confirmationPlay(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡Deseas activar la página seleccionada!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        //swal("Exito! Se ha eliminado de forma exitosa!", {
                        //    icon: "success",
                        //});
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se activado la página!", "info");
                    }
                });
        }

        
    </script>
@endsection
