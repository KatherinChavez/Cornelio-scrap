@extends('layouts.app')
@section('content')
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="fw-bold text-center">
                            <span>
                                <i class="icon-people fa-1x"></i>
                            </span> Administrar Perfil
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="justify-content-md-start">
                            <h3 class="fw-bold">{{ $user->name }} {{ $user->last_name }} {{ $user->sec_last_name }}</h3>
                        </div>
                        <div class="justify-content-md-center row" id="pages">
                            <div class="col-sm-12 col-md-4 text-center">
                                <img src="{{ asset('img/perfil.png') }}" alt="image profile" width="200px" height="200px">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <h4><strong>Correo electrónico:</strong> {{ $user->email }}</h4>
                                @forelse($user->companies as $company)
                                <h4><strong>Empresa:</strong> {{ $company->nombre }} </h4>
                                @empty
                                    <h4>Solicitá acceso a una empresa</h4>
                                @endforelse
                                <a href="{{ route('profile.edit', $user->id) }}" class="mt-5 btn btn-sm btn-round btn-block btn-primary">
                                    Editar
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="justify-content-md-start">
                            <div align="right"><small>Usuario creado: {{ $user->created_at }}</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
    </script>
@endsection
