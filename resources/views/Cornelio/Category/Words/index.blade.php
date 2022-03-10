@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Words
                    <a href="{{ route('words.create',$company) }}" class="btn btn-outline-primary float-right">
                        Create words
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
                    <div id="word"></div>
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
            let WordsList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};
            axios.post('{{ route('words.search',$company) }}', datos).then(response => {
                WordsList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th>Palabra</th>
                                    <th>Descripción</th>
                                    <th>Prioridad</th>
                                    <th>Subcategoría</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;
                let word = response.data;
                word.forEach(words => {
                    let id=words.id;
                    WordsList += `<tr>
                        <td>`+ words.word+`</td>
                        <td>`+ words.description+`</td>
                        <td>`+ words.priority+`</td>
                        <td>`+ words.subcategory_id+`</td>
                        <td width="10px">
                            <a href="./Words/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./Words/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                            </td>
                        </tr>`;
                });
                WordsList += `</tbody>
                            </table>`;
                document.getElementById('word').innerHTML = WordsList;
            });
        }
    </script>
@endsection
