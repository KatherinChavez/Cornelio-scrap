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
                            </span> Editar Perfil
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
                                {!! Form::model($user, ['route' => ['profile.update',$user->id],'method' => 'PUT']) !!}
                                <div class="row mr-2 ml-2">
                                </div>
                                {{--<div class="form-group">--}}
                                    {{--<label for="email" class="placeholder"><b>{{ __('Correo electrónico *') }}</b></label>--}}
                                    {{--{{ Form::text('email',$user->email,['class' => 'form-control', 'maxlength' => '80', 'required','autofocus', 'tabindex'=>'4']) }}--}}
                                    {{--@if ($errors->has('email'))--}}
                                        {{--<span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                                <div class="form-group">
                                    <label for="email" class="placeholder"><b>{{ __('Correo electrónico *') }}</b></label>
                                    {{ Form::text('email',$user->email,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required','autofocus', 'tabindex'=>'4']) }}
                                    <p class="text-danger">{{ $errors->first('email')}}</p>
                                </div>

                                <div class="form-group">
                                    @forelse($user->companies as $company)
                                        <h4><strong>Empresa:</strong> {{ $company->nombre }} </h4>
                                    @empty
                                        <h4>Solicitá acceso a una empresa</h4>
                                    @endforelse
                                </div>
                                <div class="form-group">
                                    {{ Form::label('label','¿Desea cambiar su contraseña?') }}
                                </div>
                                <div class="form-group position-relative">
                                    <label for="password" class="placeholder"><b>{{ __('Contraseña') }}</b></label>
                                    <div class="input-group position-relative">
                                        <input id="password" name="password" type="password" class="form-control" maxlength="20">
                                        <div class="show-password">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </div>
                                    <p class="text-danger">{{ $errors->first('password')}}</p>
                                </div>
                                <div class="form-group position-relative">
                                    <label for="password-confirm" class="placeholder"><b>{{ __('Confirmar Contraseña') }}</b></label>
                                    <div class="input-group position-relative">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" maxlength="20">
                                        <div class="show-password">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{ Form::submit('Guardar datos',['class' => 'btn btn-sm btn-round btn-block btn-primary']) }}
                                </div>
                                {!! Form::close() !!}
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
