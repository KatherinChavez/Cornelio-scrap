@extends('layouts.app')

@section('content') 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="">
                        <h4>Megacategoria
                        <a onclick="infoV()" class="mx-2">
                                <i class="fas fa-info-circle"></i>
                        </a>
                            <a href="{{ route('megacategory.create',$company) }}" class="btn btn-outline-primary btn-sm float-right">+</a>
                        </h4>
                    </div>
                    <p id="infoMega" hidden>Las Megacategorias son la empresa</p>
                </div>
                <div class="card-body table-responsive" >
                        <div class="input-group">
                            <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                            <span class="input-group-prepend">
                                <button type="submit" class="btn btn-outline-primary" onclick="search()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </span>
                        </div>
                    <div id="megacategories"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        function infoV() {
            let infoMega=document.getElementById('infoMega');
            (infoMega.hasAttribute('hidden'))? infoMega.removeAttribute('hidden'):infoMega.setAttribute('hidden','');
        }

        function statusChangeCallback(response) {
            if (response.status === 'connected') {

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function search() {
            let megategoriesList = '',
                datos = {},
                search = document.getElementById('search').value;
            datos = {search};
            axios.post('{{ route('megacategory.search',$company) }}', datos).then(response => {
                console.log(response);
                megategoriesList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>`;
                let categoria = response.data;

                categoria.forEach(megacategoria => {
                    let id=megacategoria.id;
                    megategoriesList += `<tr>
                        <td>`+ megacategoria.name+`</td>
                        <td>`+ megacategoria.description+`</td>
                        <td width="10px">
                            <a href="./Megacategory/`+id+`/edit" class="btn btn-sm btn-outline-dark">
                                Editar
                            </a>
                        </td>
                        <td width="10px">
                            <a href="./Megacategory/`+id+`" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </a>
                            </td>
                        </tr>`;
                });
                megategoriesList += `</tbody>
                            </table>`;
                document.getElementById('megacategories').innerHTML = megategoriesList;
            });
        }
    </script>
@endsection
