@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Subcategory
                    <a href="{{ route('subcategory.create',$company) }}" class="btn btn-outline-primary float-right">
                        Create subcategory
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
                    <div id="subcategories"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-subcategorias').className+=' active';

        function search() {
            let subategoriesList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};
            axios.post('{{ route('subcategory.search',$company) }}', datos).then(response => {
                subategoriesList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Canal</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;
                let subcategoria = response.data;

                subcategoria.forEach(subcategorias => {
                    let id=subcategorias.id;
                    console.log(subcategorias);
                    subategoriesList += `<tr>
                        <td>`+ subcategorias.name+`</td>
                        <td>`+ subcategorias.detail+`</td>
                        <td>`+ subcategorias.channel+`</td>
                        <td width="10px">
                            <a href="./Subcategory/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./Subcategory/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                            </td>
                        </tr>`;
                });
                subategoriesList += `</tbody>
                            </table>`;
                document.getElementById('subcategories').innerHTML = subategoriesList;
            });
        }
    </script>
@endsection