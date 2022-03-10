@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Tags comments
                    <a href="{{ route('tags.create',$company) }}" class="btn btn-outline-primary float-right">
                        Create tags
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
                    <div id="tags"></div>
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
            let TagsList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};
            axios.post('{{ route('tags.search',$company) }}', datos).then(response => {
                TagsList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;
                let tag = response.data;

                tag.forEach(tags => {
                    let id=tags.id;
                    TagsList += `<tr>
                        <td>`+ tags.name+`</td>
                        <td>`+ tags.type+`</td>
                        <td width="10px">
                            <a href="./TagsComment/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./TagsComment/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                            </td>
                        </tr>`;
                });
                TagsList += `</tbody>
                            </table>`;
                document.getElementById('tags').innerHTML = TagsList;
            });
        }

    
    </script>
@endsection
