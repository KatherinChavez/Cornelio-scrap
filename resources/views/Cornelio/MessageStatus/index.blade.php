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
                    <form action="{{ route('messageStatus.index') }}" method="get">
                        <div class="card-header">
                            <h4>Estatus de los mensajes</h4>
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

                            <br>
                            <div class="card-body table-responsive">
                                <table class="table table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo mensaje</th>
                                        <th>Número de teléfono</th>
                                        <th>Mensaje</th>
                                        {{--<th>Compañía</th>--}}
                                        <th>Reporte</th>
                                        <th>Estatus Wapia</th>
                                        <th>Estatus</th>
                                        <th>Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($messages as $message)
                                        <tr>
                                            <td>{{ $message->id}}</td>
                                            @if($message->typeMessage == 'text')
                                                <td>Mensaje de texto</td>
                                            @else
                                                <td>PDF</td>
                                            @endif
                                            <td>{{ $message->number}}</td>
                                            <td><div class="cortar">{{ urldecode($message->message)}}</div></td>
                                            {{--<td>{{ urldecode($message->message)}}</td>--}}
                                            {{--<td>company</td>--}}
                                            {{--<td>{{ $message->report}}</td>--}}

                                            @if($message->report == 1)
                                                <td>Top 10 de reacciones matutinas</td>
                                            @elseif($message->report == 2)
                                                <td>Top 10 de reacciones de medio día</td>
                                            @elseif($message->report == 3)
                                                <td>Top 10 de reacciones de la tarde</td>
                                            @elseif($message->report == 4)
                                                <td>Top 10 de reacciones al finalizar el día</td>
                                            @elseif($message->report == 5)
                                                <td>Análisis Top 10 de reacciones matutinas</td>
                                            @elseif($message->report == 6)
                                                <td>Análisis Top 10 de reacciones de medio día</td>
                                            @elseif($message->report == 7)
                                                <td>Análisis Top 10 de reacciones de la tarde</td>
                                            @elseif($message->report == 8)
                                                <td>Análisis Top 10 de reacciones al finalizar el día</td>
                                            @elseif($message->report == 9)
                                                <td>Información del tema en el top 5 matutino</td>
                                            @elseif($message->report == 10)
                                                <td>Información del tema en el top 5 al medio día</td>
                                            @elseif($message->report == 11)
                                                <td>Información del tema en el top 5 en la tarde</td>
                                            @elseif($message->report == 12)
                                                <td>Información del tema en el top 5 al finalizar día</td>
                                            @elseif($message->report == 13)
                                                <td>Palabras de los contenidos al iniciar el día</td>
                                            @elseif($message->report == 14)
                                                <td>Palabras de los contenidos al medio día</td>
                                            @elseif($message->report == 15)
                                                <td>Palabras de los contenidos en la tarde</td>
                                            @elseif($message->report == 16)
                                                <td>Palabras de los contenidos al finalizar el día</td>
                                            @elseif($message->report == 99)
                                                <td>Alertas de publicaciones clasificadas</td>
                                            @endif

                                            <td>{{ $message->error}}</td>

                                            @if($message->status == '0')
                                                <td style="color: #AA3333"><b>No se ha enviado el mensaje</b></td>
                                            @elseif($message->status == '100')
                                                <td style="color: #ba8b00"><b>Se encuentra en proceso</b></td>
                                            @elseif($message->status == '1')
                                                <td style="color: #2ca02c"><b>Se envió con éxito el saludo</b></td>
                                            @elseif($message->status == '2')
                                                <td style="color: #2ca02c"><b>Se envió con éxito de PDF</b></td>
                                            @elseif($message->status == '3')
                                                <td style="color: #2ca02c"><b>Se envió con el éxito análisis de Top 10 </b></td>
                                            @elseif($message->status == '4')
                                                <td style="color: #2ca02c"><b>Se envió con éxito Top 5</b></td>
                                            @elseif($message->status == '5')
                                                <td style="color: #2ca02c"><b>Se envió con éxito de burbuja de palabra </b></td>
                                            @endif

                                            <td>{{ $message->created_at}}</td>

                                            <td>
                                                @if($message->status == 0)
                                                <a onclick="confirmation(event)" href="./StatusMessage/resend/{{$message->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                    <i class="fas fa-share"></i>
                                                </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-icon btn-round btn-success mt-3" disabled>
                                                        <i class="fas fa-share"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div id="paginacion" class="row justify-content-center">
                            {{ $messages->appends($_GET)->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡Deseas reenviar el mensaje seleccionado!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        //swal("Exito! Se ha eliminado de forma exitosa!", { icon: "success",});
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha enviado el mensaje!", "info");
                    }
                });
        }
    </script>
@endsection
