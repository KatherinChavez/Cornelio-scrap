@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form method="get">
                        <div class="card-header">
                            <div class="form-row align-items-center">
                                <h4>
                                    Sincronización de conexión de WhatsApp
                                </h4>
                            </div>
                        </div>
                        <br>
                        <div class="card-body table-responsive">

                            @if($data != null)
                                <div class="form-row ">
                                    <div class="col-md-12">
                                        <button class="btn btn-outline-success float-right" type="button" title="Reconectar instancia" onclick="Reconnect()"><i class="fas fa-plug"></i>  Reconectar instancia</button>
                                        <button class="btn btn-outline-danger float-right" type="button" title="Reiniciar instancia" onclick="Reboot()"><i class="fas fa-power-off"></i>  Reinciar instancia</button>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card-body">
                                            <h5><b>Para usar WhatsApp:</b></h5>
                                            <ol>
                                                <li>Abre WhatsApp en tu teléfono</li>
                                                <li>Toca <b>Menú</b> o <b>Configuración</b> y selecciona <b>Dispositivos vinculados</b></li>
                                                <li>Cuando se active la cámara, apunta tu teléfono hacia esta pantalla para escanear el código</li>
                                            </ol>

                                            <ul id="messages">
                                                <embed type="text/html" src="https://wapiad.com/api/getqr.php?client_id={{$data->client_id}}&instance={{$data->instance}}" width="600" height="500">
                                                <div id="pairing" style="margin:10px"></div>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card-body">
                                            <video src="https://wapiad.com/media/scan.mp4" class="embed-responsive-item" type="video/mp4" autoplay muted loop width="470" height="550"></video>
                                        </div>
                                    </div>

                                </div>
                            @else
                                <div class="form-row ">
                                    <div class="card-body text-center" id="home-title">
                                        <h1 class="pb-2 fw-bold">No cuenta con una instancia</h1>
                                        <h4 class="pb-2 fw-bold">Comuníquese con el administrador para brindarle una instancia</h4>
                                    </div>
                                </div>
                            @endif
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

        function Reconnect() {
            axios.post('{{ route('sync_up.Reconnect') }}').then(response => {
                if(response.data == 200){
                    swal('Éxito !', 'Instancia reconectada correctamente', 'success');
                }
                else{
                    swal('Error !', 'No ha sido posible reconectar la instancia', 'error');
                }
            });
        }
        function Reboot() {
            axios.post('{{ route('sync_up.Reboot') }}').then(response => {
                if(response.data == 200){
                    swal('Éxito !', 'Instancia reiniciada correctamente', 'success');
                }
                else{
                    swal('Error !', 'No ha sido posible reiniciada la instancia', 'error');
                }
            });
        }
    </script>
@endsection
