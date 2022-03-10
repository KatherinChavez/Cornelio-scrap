@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Empresas</h4>
                </div>
                <div class="card-body">
                    @if ($companies->count() > 0 )
                    <h4>Por favor selecciona tu empresa</h4>
                    <div class="form-group">
                        <select class="form-control" id="companies">
                            <option value="0">Seleccione...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->slug }}">{{ $company->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div id="continuar" class="col-sm-12">

                        </div>
                    </div>
                    @else
                        <div>
                            <p>Por favor contacte el administrador de su compañia para finalizar su registro.</p>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('companies').addEventListener('change',ir);
        function ir() {
            let continuarList='',
                companies=document.getElementById('companies').value;
            if(companies!==0){
//                continuarList='<a href="../index" class="btn btn-block btn-sm btn-success" id="btn-continuar">' +
//                    '<i class="fa fa-building"></i> Ingresar a la empresa</a>';

                continuarList='<a href="../Tops" class="btn btn-block btn-sm btn-success" id="btn-continuar">' +
                    '<i class="fa fa-building"></i> Ingresar a la empresa</a>';
                datos ={companies};
                axios.post('{{route('home.session')}}', datos).then(response => {

                    localStorage.setItem('company_id',response.data.company_id);
                    sessionStorage.setItem('company_id',response.data.company_id);
                });
                localStorage.setItem('company',companies);
                sessionStorage.setItem('company',companies);
            }
            $('#continuar').html(continuarList);
        }
        function statusChangeCallback(response) {
        }
    </script>
@endsection
