@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <pages-component :company="{{json_encode($company)}}" :is-agency="{{json_encode($isAgencia)}}" :user-id="{{json_encode($user_id)}}" ></pages-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
