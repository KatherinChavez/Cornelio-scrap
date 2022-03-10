@extends('layouts.app')

@section('content')

    <content-component :contents="{{ json_encode($data) }}"></content-component>

@endsection

@section('script')
    <script>
        $(document).ready(()=>{

        });
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                let accessToken = response.authResponse.accessToken;
                isLogedIn(accessToken);
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }
        function isLogedIn(accessToken){
            let pageAccessToken='';
            window.FB.api(
                '/oauth/access_token',
                'GET',
                {
                    "client_id": "{{env('APP_FB_ID_2')}}",
                    "client_secret": "{{env('APP_FB_SECRET_2')}}",
                    "grant_type": "client_credentials"
                },
                function (response) {
                    if (response.access_token) {
                        pageAccessToken = response.access_token;
                        localStorage.setItem('pageToken', pageAccessToken);
                    } else {
                        window.FB.api(
                            '/me',
                            'GET',
                            {"fields": "accounts"},
                            function (response) {
                                pageAccessToken = response.access_token;
                                localStorage.setItem('pageToken', pageAccessToken);
                            }
                        );

                    }
                }
            );
        }
    </script>
@endsection
