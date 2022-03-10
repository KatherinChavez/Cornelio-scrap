@extends('layouts.rapidas')

@section('scraps')
    @include('includes.scraps_menu')
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Detalle del scrap
                        <a href="{{ route('scraps.indexCRUD',$company) }}" class="btn btn-outline-primary btn-sm float-right">
                            Volver
                        </a>
                    </h4></div>
                <div class="card-body">
                    <p><strong>Id de la página de facebook: </strong>{{ $scraps->page_id }}</p>
                    <p><strong>Nombre de la página de facebook: </strong>{{ $scraps->page_name }}</p>
                    @if($scraps->post_id != null)
                        <p><strong>Id del post de facebook: </strong>{{ $scraps->post_id }}</p>
                    @else()
                    @endif
                    <p><strong>Usuario que realizó el scrap: </strong>{{ $scraps->user->name }}</p>
                    <p><strong>Categoría a la que pertenece: </strong>{{ $scraps->categories->name }}</p>
                    <p><strong>Fecha del scrap: </strong>{{ $scraps->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-pages').className+=' active';
    </script>
@endsection
