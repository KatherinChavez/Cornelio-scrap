@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            Menciones de twitter de {{$page_name}}
                            <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Se cuenta con la opción de conocer y clasificar los sentimiento de los comentarios">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <br>
                        <div class="row justify-content-center" id="posts">
                            @foreach($tweets as $tweet)
                                <div class="col-md-4" >
                                    <div class="card mb-4" style=" height: 450px;overflow: auto">
                                        <div class="card-body" id="body-{{$tweet->referenced_tweets}}">
                                            @if($tweet->referenced_tweets <> 0)
                                                @php
                                                    $app = \App\Models\Twitter\TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
                                                    $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
                                                    $curl = curl_init();
                                                    curl_setopt_array($curl, array(
                                                        CURLOPT_URL => "https://api.twitter.com/1.1/statuses/show.json?id=$tweet->referenced_tweets",
                                                        CURLOPT_RETURNTRANSFER => true,
                                                        CURLOPT_ENCODING => '',
                                                        CURLOPT_MAXREDIRS => 10,
                                                        CURLOPT_TIMEOUT => 0,
                                                        CURLOPT_FOLLOWLOCATION => true,
                                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                        CURLOPT_CUSTOMREQUEST => 'GET',
                                                        CURLOPT_HTTPHEADER => array(
                                                            "Authorization: Bearer $bearer",
                                                        ),
                                                    ));

                                                    $response = curl_exec($curl);
                                                    curl_close($curl);
                                                    $response_deco = json_decode($response);
                                                    $references = \App\Models\Twitter\TweetMention::where('referenced_tweets', $tweet->referenced_tweets)->get();
                                                @endphp
                                                <h3 style="color: #0A5A97">
                                                    <b><a href="https://twitter.com/{{$response_deco->user->screen_name}}/status/{{$tweet->id_mention}}" target="_blank"><strong>{{$response_deco->user->name}}</strong></a></b>
                                                </h3>
                                                <h5>@ {{$response_deco->user->screen_name}}</h5>
                                                <br>

                                                <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($response_deco->created_at)->format('Y-m-d h:i:s')}}</p>

                                                <p class="card-text small">
                                                    <b>{{$response_deco->text}}</b>
                                                </p>
                                                <hr class="my-0"><br>
                                                <div class="card-body" style=" " id="comments-{{$tweet->referenced_tweets}}" >
                                                    @foreach($references as $reference)
                                                        <div class="received_withd_msg">
                                                            <div style="padding: 2px" class="rounded">
                                                                <p class="card-text small">
                                                                    <b><a href="https://twitter.com/{{$reference->username}}/status/{{$reference->id_mention}}" target="_blank"><strong>{{ $reference->name}}</strong></a></b>
                                                                    <br>
                                                                    {{$reference->text}}
                                                                </p>
                                                                <p>{{\Carbon\Carbon::parse($reference->created_time)->format('Y-m-d h:i:s')}}</p>
                                                            </div>
                                                        </div>
                                                        <hr class="my-0"><br>
                                                    @endforeach
                                                </div>

                                            @elseif($tweet->referenced_tweets == 0)
                                                <h3 style="color: #0A5A97">
                                                    <b><a href="https://twitter.com/{{$tweet->username}}/status/{{$tweet->id_mention}}" target="_blank"><strong>{{$tweet->name}}</strong></a></b>
                                                </h3>
                                                <h5>@ {{$tweet->username}}</h5>
                                                <br>
                                                <p class="card-title mb-1 small">{{\Carbon\Carbon::parse($tweet->created_time)->format('Y-m-d h:i:s')}}</p>

                                                <p class="card-text small">
                                                    <b>{{$tweet->text}}</b>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="paginacion" class="row justify-content-center">
                            {{$tweets->appends(request()->except('page'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var user="{{ Auth::user()->id }}";

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

    </script>

@endsection
