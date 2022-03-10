@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><h4>Listado de scraps</h4></div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th width="10px">Nombre de la página</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($scraps as $scrap)
                            <tr>
                                <td>{{ $scrap->page_name }}</td>
                                <td>{{ $scrap->categories->name }}</td>
                                <td>{{ $scrap->created_time }}</td>
                                <td width="10px">
                                    <a href="{{ route('scrapsPage.showCRUD', [$company,$scrap->id]) }}" class="btn btn-outline-success btn-sm float-right">
                                        Ver
                                    </a>
                                </td>
                                <td width="10px">
                                    {!! Form::open(['route' => ['scrapsPage.destroy',$company, $scrap->id],
                                    'method' => 'DELETE']) !!}
                                    <button class="btn btn-outline-danger btn-sm float-right">Eliminar</button>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $scraps->links() }}
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
