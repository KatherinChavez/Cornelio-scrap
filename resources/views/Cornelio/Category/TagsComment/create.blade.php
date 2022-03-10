@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Crear tags</h4></div>
                <div class="card-body">
                    {!! Form::open(['route' => ['tags.store',$company]]) !!}
                        @include('Cornelio.Category.TagsComment.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        document.getElementById('nav-tags').className+=' active';
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                //isLogedIn();
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }
    </script>
@endsection
