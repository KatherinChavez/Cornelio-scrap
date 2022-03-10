@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4>Adminitración de notificaciones de los temas</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <div id="contenido"></div>
                        <table class="table table-striped table-hover">
                            <tbody>
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td>{{ $alert->name }}</td>
                                    @php
                                    //dd($alert);
                                    @endphp
                                    <td><input class="form-check-input" type="checkbox"
                                               id="status-{{ $alert->subcategory_id}}" name="notification"
                                               onclick="status({{ $alert->subcategory_id}})"> Estatus
                                    </td>
                                    <td><input class="form-check-input" type="checkbox"
                                               id="notification-{{ $alert->subcategory_id}}" name="notification"
                                               onclick="notificacion({{ $alert->subcategory_id}})"> Notificación
                                    </td>
                                    <td><input class="form-check-input" type="checkbox" id="report-{{ $alert->subcategory_id}}"
                                               name="report" onclick="reporte({{ $alert->subcategory_id}})"> Reporte Diario
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $alerts->links() }}
                    </div>
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

        $(document).ready(
            axios.post('{{route('alerts.consult')}}').then(response => {
                let result = response.data;
                console.log(response.data);
                result.forEach(status => {
                    console.log(status);
                    let id = status.subcategory_id,
                        noti = status.notification,
                        repo = status.report,
                        st = status.status;
                    if (noti != 0) {
                        document.getElementById('notification-' + id).checked = true;
                    }
                    if (repo != 0) {
                        document.getElementById('report-' + id).checked = true
                    }
                    if (st != 0) {
                        document.getElementById('status-' + id).checked = true
                    }
                });
            })
        );

        function status(id) {
            if (document.getElementById('status-' + id).checked === true) {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea activar este",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Tema activado", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.status',$company)}}', data).then(response => {
                            });
                            document.getElementById('status-' + id).checked = true;
                        } else {
                            swal("No se notificará");
                            document.getElementById('status-' + id).checked = false;
                        }
                    });
            } else {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea desactivar",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Tema desactivado", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.statusOff',$company)}}', data).then(response => {
                            });
                            document.getElementById('status-' + id).checked = false;
                        } else {
                            swal("El estado del tema sigue activo");
                            document.getElementById('status-' + id).checked = true;
                        }
                    });
            }
        }

        function notificacion(id) {
            if (document.getElementById('notification-' + id).checked === true) {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea notificar a este",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Notificación activada", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.notification',$company)}}', data).then(response => {
                            });
                            document.getElementById('notification-' + id).checked = true;
                        } else {
                            swal("No se notificará");
                            document.getElementById('notification-' + id).checked = false;
                        }
                    });
            } else {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea dejar de notificar a este",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Notificación desactivada", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.notificationOff',$company)}}', data).then(response => {
                            });
                            document.getElementById('notification-' + id).checked = false;
                        } else {
                            swal("La notificación sigue activa");
                            document.getElementById('notification-' + id).checked = true;
                        }
                    });
            }
        }

        function reporte(id) {
            if (document.getElementById('report-' + id).checked === true) {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea recibir el reporte diario de este",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Reporte diario activado", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.report',$company)}}', data).then(response => {
                            });
                            document.getElementById('report-' + id).checked = true;
                        } else {
                            swal("No habrá reporte diario");
                            document.getElementById('report-' + id).checked = false;
                        }
                    });
            } else {
                let data = '';
                data = {id};
                swal({
                    title: "¿Desea dejar de recibir el reporte diario de este",
                    text: "tema?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            swal("Reporte diario desactivado", {
                                icon: "success",
                            });
                            axios.post('{{route('alerts.reportOff',$company)}}', data).then(response => {
                            });
                            document.getElementById('report-' + id).checked = false;
                        } else {
                            swal("La notificación sigue activa");
                            document.getElementById('report-' + id).checked = true;
                        }
                    });
            }
        }
    </script>
@endsection
