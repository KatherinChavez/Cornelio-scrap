@extends('layouts.app')

@section('content')
    {{--<content-component-twitter :contents="{{ json_encode($data) }}"></content-component-twitter>--}}
    <content-component-twitter></content-component-twitter>
    {{--<div class="col-lg-12">--}}
        {{--<div class="justify-content-center">--}}
            {{--<div class="col-lg-12">--}}
                {{--<div class="card">--}}
                    {{--<div class="card-header">--}}
                        {{--<h3 class="fw-bold"><span><i class="icon-layers"></i></span> Extracción de contenido de Twitter</h3>--}}
                    {{--</div>--}}
                    {{--<input id="user" type="hidden" value="{{ Auth::user()->id }}">--}}
                    {{--<div class="card-body table-responsive">--}}
                        {{--<div class="form-group">--}}
                            {{--<p class="h3">Tweets by username</p>--}}
                            {{--<input id="username" class="form-control" placeholder="Type an username...">--}}
                            {{--<button class="btn btn-primary mt-2 pull-right" onclick="searchTweets();">Get Tweets</button>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<p class="h3">Twitter mentions by username</p>--}}
                            {{--<input id="username1" class="form-control" placeholder="Type an username...">--}}
                            {{--<button class="btn btn-primary mt-2 pull-right" onclick="searchMentionsByUser();">Get Tweets</button>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<p class="h3">Tweets by words</p>--}}
                            {{--<input id="word" class="form-control" placeholder="Type a word to search tweets...">--}}
                            {{--<button class="btn btn-primary mt-2 pull-right" onclick="searchTwitterByWord();">Get Tweets</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection

@section('script')
    <script>
    function searchTweets() {
        let username = document.getElementById('username').value;
        axios.post('{{ route('twitter.by_username')}}', {username}).then(response => {
            if(response.data == 200){
                swal('Exito!', 'Se almaceno con exito', 'success');
            }
            else{
                swal('Error!', 'No se ha presentando error, intente más tarde', 'error');
            }
        });
    }

    function searchMentionsByUser() {
        let username = document.getElementById('username1').value;
        axios.post('{{ route('twitter.mentions_by_username')}}', {username}).then(response => {
            console.log(response);
        });
    }

    function searchTwitterByWord() {
        let word = document.getElementById('word').value;
        axios.post('{{ route('twitter.by_word')}}', {word}).then(response => {
            console.log(response);
        });
    }

//    var settings = {
//        "url": "https://api.twitter.com/2/users/by/username/ameliarueda",
//        "method": "GET",
//        "timeout": 0,
//        "headers": {
//            "Authorization": "Bearer AAAAAAAAAAAAAAAAAAAAAM3kWQEAAAAAKPxNRoxXQUCtROWL5neItPweiuY%3DZtzKiAj0R2APkzj0bqKFdpJNZLYriMNf1FwgP9AISNgylAIuTS",
//        },
//    };
//
//    $.ajax(settings).done(function (response) {
//        console.log(response);
//    });
</script>
@endsection
