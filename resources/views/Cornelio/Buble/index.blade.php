@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header" align="center">
                        <h1> Reporte de burbuja temática</h1>
                    </div>

                    <div class="card-body ">
                        {{-- MUESTRA LAS PAGINAS QUE SE ENCUENTRA COMO COMPETENCIA--}}
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre / Descripción </th>
                                        <th>Reporte</th>
                                        <th>Acciones</th>
                                        <th colspan="4">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bubles as $buble)
                                            <tr>
                                                <td>{{ $buble->id }}</td>
                                                <td>{{ $buble->descripcion }}</td>
                                                @if($buble->report == 13)
                                                    <td>Reporte matutino</td>
                                                @elseif($buble->report == 14)
                                                    <td>Reporte al medio día</td>
                                                @elseif($buble->report == 15)
                                                    <td>Reporte al finalizar la tarde</td>
                                                @elseif($buble->report == 16)
                                                    <td>Reporte al finalizar el día</td>
                                                @endif
                                                <td>
                                                    <div class="list-group-item-figure">
                                                        <button type="button" onclick="show({{ $buble->id }})" data-user="{{ $buble->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#ModalShow">
                                                            <i class="far fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('bubles.edit', $buble->id) }}"  class="btn btn-sm btn-icon btn-round btn-warning mt-3" >
                                                            <i class="icon-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL SHOW --------------------------------------------------}}

    <div class="modal fade" id="ModalShow" tabindex="-1" role="dialog" aria-labelledby="ModalShow" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reporte de burbuja temática </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="posts"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let id_t=0;
        function show(id){
            let id_telephone = id ,
                postList = '',
                info = '',
                data={id_telephone};
            id_t = id_telephone;
            axios.post("{{route('bubles.show')}}",data).then( response => {
                $('#posts').html('');
                info = response.data.data;
                postList += `<div class="form-group">
                                <p><strong>Nombre de la empresa: </strong>`+info.company+`</p>
                            </div>

                            <div class="form-group">
                                <p><strong>Nombre / descripción: </strong>`+info.descripcion+`</p>
                            </div>`;

                if(info.group_id != null && info.group_id != 0){
                    postList += `<div class="form-group">
                                    <p><strong>Indetificador de grupo: </strong>`+info.group_id+`</p>
                                 </div>`;
                }
                if(info.numeroTelefono != 0){
                    postList += `<div class="form-group">
                                    <p><strong>Número de teléfono: </strong>`+info.numeroTelefono+`</p>
                                 </div>`;
                }
                postList += `<div class="form-group">
                               <h5>Tipo de reporte </h5>`;

                if(info.report == 13){
                    postList += ` <p><strong>Reporte matutino</strong></p>`;
                }
                if(info.report == 14){
                    postList += ` <p><strong>Reporte al medio día</strong></p>`;
                }
                if(info.report == 15){
                    postList += ` <p><strong>Reporte al finalizar la tarde</strong></p>`;
                }
                if(info.report == 16){
                    postList += ` <p><strong>Reporte al finalizar el día</strong></p>`;
                }
                postList += `</div>`;
                if(response.data.contents){
                    let contents = response.data.contents;
                    postList += `<div class="form-group">
                                    <h4>Contenido seleccionado</h4>
                                    <ul style="list-style-type:circle">`;
                    contents.forEach(function (content, index) {
                        console.log(content);
                        postList += `<li><label>`+content.name+`</label></li>`;
                    });
                    postList += `</div>
                                    </ul>`;
                }

                $('#posts').append(postList);
            });
        }
    </script>
@endsection
