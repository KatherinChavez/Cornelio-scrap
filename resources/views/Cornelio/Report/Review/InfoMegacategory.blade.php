@extends('layouts.app')

@section('content')
    <div class="col-lg-12">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="fw-bold">Validación y reporte de contenido</h3></div>
                    <div class="card-body">
                        <input id="user" type="hidden" value="{{ Auth::user()->id }}">
                        <div class="form-group">
                            {{ Form::label('id','Seleccione un contenido *') }}
                            {{ Form::select('id',$Category,null,['class'=>'form-control','placeholder'=>'Seleccione un contenido','required']) }}
                            <p class="text-danger">{{ $errors->first('description')}}</p>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <button type="button" id="obtener"
                                            class="btn btn-sm btn-round btn-primary pull-left btn-block mb-3" onclick="today()">
                                        Información del día de hoy
                                    </button>
                                </div>
                                <div class="col-lg-6">
                                    <button type="button" id="obtener"
                                            class="btn btn-sm btn-round btn-success pull-right btn-block mb-3"
                                            onclick="verificar()">
                                        Verificar información
                                    </button>
                                </div>
                            </div>
                        </div>
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
                //window.location = "{{ route('facebook.index',$company) }}";
            } else {
                //window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function today() {
            var cont = document.getElementById('id').value;
            console.log(cont);
            if (cont != 0) {
                let megaE = btoa(cont);
                window.open(
                    'MecategoryToday/' + megaE,
                    '_blank'
                );
            } else {
                swal('Ops', 'Por favor seleccione un contenido', 'warning');
            }
        }

        function verificar() {
            var cont = document.getElementById('id').value;
            if (cont != 0) {
                let megaE = btoa(cont);
                window.open(
                    'ValidateMegategory/' + cont,
                    '_blank' // <- This is what makes it open in a new window.
                );
            } else {
                swal('Ops', 'Por favor seleccione un contenido', 'warning');
            }
        }


    </script>

@endsection
