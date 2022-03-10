@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($user, ['route' => ['users.update',$user->id],
                    'method' => 'PUT']) !!}
                        @include('admin.users.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
            $(document).ready(function(){
                $("#name").on('keypress', function(e) {
                    let regex = new RegExp("^[a-zA-Z ]*$");
                    let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                    if (regex.test(str)) {
                        return true;
                    }
                    e.preventDefault();
                    return false;
                });
            });

        document.getElementById('nav-admin').className+=' active';
    </script>
@endsection
