@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Editar número</h4></div>
                <div class="card-body">
                    {!! Form::model($whatsapp, ['route' => ['whatsapp.update',$whatsapp->id],
                    'method' => 'PUT']) !!}
                    @include('Cornelio.Category.NumberWhatsapp.partials.form')
                    {!! Form::close() !!}
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
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

    </script>
@endsection
