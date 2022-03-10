@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Page sentiment
                    <a href="{{ route('AdminSentiment_user.create',$company) }}" class="btn btn-outline-primary float-right">
                        Crear sentimiento
                    </a></h4>
                </div>
                <div class="card-body table-responsive">
                    <div class="input-group">
                        <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                        <span class="input-group-prepend">
                            <button type="submit" class="btn btn-outline-primary" onclick="search()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </span>
                    </div>
                    
                    <div class="table-responsive" id="table">

                    </div>
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
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }


        function search() {
            let sentimientoList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};

            axios.post('{{ route('AdminSentiment_user.search',$company) }}', datos).then(response => {
                sentimientoList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th width="10px">ID</th>
                                    <th>Nombre sentimiento</th>
                                    <th>Descripción de sentimiento</th>
                                    <th colspan="4" >&nbsp;</th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;

                let sentimiento = response.data;

                sentimiento.forEach(userSentiment => {
                    let id=userSentiment.id;
                    sentimientoList += `<tr>
                        <td>`+ userSentiment.id+`</td>
                        <td>`+ userSentiment.sentiment+`</td>
                        <td>`+ userSentiment.sentiment_detail+`</td>
                        <td width="10px">
                            <a href="./Sentiment_User/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./Sentiment_User/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                            </td>
                        </tr>`;
                });
                sentimientoList += `</tbody>
                            </table>`;
                
                $('#table').html(sentimientoList);
                    $('#tabla-log').dataTable({
                        "order": [[ 2, "desc" ]]
                    });
            });
        }
    </script>
@endsection