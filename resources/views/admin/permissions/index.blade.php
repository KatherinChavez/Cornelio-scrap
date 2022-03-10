@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Permisos de empresas de cornelio
                    @can('permissions.create')
                    <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-outline-primary float-right">
                        Crear
                    </a>
                        @endcan</h4>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10px">ID</th>
                                <th>Nombre</th>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td width="10px">
                                    @can('permissions.show')
                                    <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-sm btn-outline-success">
                                        Ver
                                    </a>
                                    @endcan
                                </td>
                                <td width="10px">
                                    @can('permissions.edit')
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-outline-dark">
                                        Editar
                                    </a>
                                    @endcan
                                </td>
                                <td width="10px">
                                    @can('permissions.destroy')
                                    {!! Form::open(['route' => ['permissions.destroy', $permission->id],
                                    'method' => 'DELETE']) !!}
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-agencia').className+=' active';
    </script>
@endsection
