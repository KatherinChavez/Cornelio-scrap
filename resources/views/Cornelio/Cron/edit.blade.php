@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Administración de ejecución de página</div>
                <div class="card-body">
                    {!! Form::model($cron, ['route' => ['Cron.update',$cron->id],
                    'method' => 'PUT']) !!}
                        @include('Cornelio.Cron.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">
        document.getElementById('nav-areas').className+=' active';
    </script>
@endsection