@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header"><h4>Usuarios de cornelio</h4>
                @can('users.create')
                    <a href="{{ route('users.crea') }}" class="btn btn-sm btn-outline-primary float-right">
                        Crear usuario
                    </a>
                    @endcan</h4>
                </div>
                <div class="card-body table-responsive">
                    <div class="card">
                        <form action="{{ route('users.index') }}" method="get">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control border-info" placeholder="Buscar el usuario">
                                <span class="input-group-prepend">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10px">ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Fecha de ingreso</th>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td width="10px">
                                    @can('users.show')
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-success">
                                        Ver
                                    </a>
                                    @endcan
                                </td>
                                <td width="10px">
                                        @can('users.edit')
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-dark">
                                        Editar
                                    </a>
                                    @endcan
                                </td>
                                <td width="10px">
                                    @can('users.destroy')
                                    {!! Form::open(['route' => ['users.destroy', $user->id],
                                    'method' => 'DELETE']) !!}
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
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
