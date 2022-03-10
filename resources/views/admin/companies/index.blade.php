@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h4>Administrador de Empresas
                            @can('companies.create')
                                <a href="{{ route('companies.create') }}"
                                   class="btn btn-sm btn-outline-primary float-right">
                                    Crear
                                </a>
                            @endcan</h4>
                        <div class="card-title">
                            Compañías del usuario
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>{{ $company->nombre }}</td>
                                    <td>{{ $company->descripcion }}</td>
                                    <td>
                                        @if($company->status==1)
                                            Activo
                                        @else
                                            Inactivo
                                        @endif
                                    </td>
                                    <td width="10px">
                                        @can('companies.edit')
                                            <a href="{{ route('companies.edit', $company->id) }}"
                                               class="btn btn-sm btn-outline-success">
                                                Editar
                                            </a>
                                        @endcan
                                    </td>
                                    <td width="10px">
                                        @can('companies.destroy')
                                            {!! Form::open(['route' => ['companies.destroy', $company->id],
                                            'method' => 'DELETE']) !!}
                                            <button class="btn btn-sm btn-outline-danger">X</button>
                                            {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{--{{ $companies->links() }}--}}
                    </div>
                </div>
                @isset($otras)
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Compañías en general
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th colspan="3">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($otras as $company)
                                    <tr>
                                        <td>{{ $company->nombre }}</td>
                                        <td>{{ $company->descripcion }}</td>
                                        <td>
                                            @if($company->status==1)
                                                Activo
                                            @else
                                                Inactivo
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('companies.edit')
                                                <a href="{{ route('companies.edit', $company->id) }}"
                                                   class="btn btn-sm btn-outline-success">
                                                    Editar
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="10px">
                                            @can('companies.destroy')
                                                {!! Form::open(['route' => ['companies.destroy', $company->id],
                                                'method' => 'DELETE']) !!}
                                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                {!! Form::close() !!}
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{--                    {{ $companies->links() }}--}}
                        </div>
                        @endisset
                    </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-company').className += ' active';
    </script>
@endsection
