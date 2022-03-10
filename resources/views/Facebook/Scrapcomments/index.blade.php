@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4>Publicaciones</h4></div>
                    <div class="card-body">
                        <div class="row justify-content-center" id="posts">

                        </div>
                        <div class="row justify-content-center">
                            <input id="load-more" type="button" onclick="getNext()" class="btn btn-lg btn-success"
                                   value="Ver más"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let postLimit = 10,
            commentLimit = 50,
            commentLimitSecondTimes = 200,
            user = "{{ Auth::user()->id }}",
            pageAccessToken = "",
            loadMore = '';


        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                isLogedIn();

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            FB.api(
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
                        showScrap();
                    } else {
                        FB.api(
                            '/me',
                            'GET',
                            {"fields": "accounts"},
                            function (response) {
                                pageAccessToken = response.access_token;
                            }
                        );

                    }
                }
            );
        }

        function showScrap() {
            FB.api(
                '/{{ $_GET['page'] }}',
                'GET',
                {
                    "fields": "posts.limit(" + postLimit + "){message,attachments{title,type,url,description},story,id,full_picture,created_time}",
                    "access_token": pageAccessToken
                },
                function (response) {
                    // Insert your code here
                    generatePostLists(response);
                    showHidePagination(response.posts);
                }
            );
        }

        function generatePostLists(data) {
            try {
                let posts, message,
                    postList = '';
                if (data.posts) {
                    posts = data.posts.data;
                } else {
                    posts = data.data;
                }
                if (posts !== undefined) {
                    posts.forEach(function (post, index) {
                        postList += `<div class="col-md-6">
                                <div class="card mb-3">`;
                        if (post.attachments) {
                            var attachements = post.attachments.data[0];
                            if (attachements.type === "photo" || attachements.type === 'cover_photo' || attachements.type === 'share') {
                                postList += '<img class="card-img-top img-fluid w-100" src="' + post.full_picture + '" alt="">';
                            }
                            if (attachements.type === "video") {
                                postList += '<video width="320" height="240" controls><source src="' + attachements.url + '" type="video/mp4"></video>';
                            }
                        }
                        if (post.message) {
                            message = post.message;
                        } else {
                            message = post.story;
                        }
                        var created_time = moment(post.created_time),
                            formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                        postList += '<div class="card-body">' +
                            '<p class="card-title mb-1 small">' + formated_created_time.substring(0, 10) + ' ' + formated_created_time.substring(11, 16) + '</p>' +
                            '<p class="card-text small">' + message + '';
                        if (post.attachments) {
                            if (attachements.type === 'share') {
                                postList += '<br><a href="' + attachements.url + '" target="_blank">' + attachements.title + '</a>';
                            }
                        }
                        postList += `</p></div><hr class="my-0">
                                <div class="card-body py-2 small" id="reactions-` + post.id + `">Reacciones</div><hr class="my-0">
                                <div class="card-body" id="comments-` + post.id + `"></div>
                            </div>
                        </div>`;
                        getReactions(post);
                        getComments(post);
                    });
                    postList += '';
                    $('#posts').append(postList);
                } else {
                    postList = `<div class="row text-center">
                                    <h2 class="fw-bold">No se encontraron posteos</h2>
                                </div>`;
                    $('#load-more').attr('hidden', 'true');
                    $('#posts').append(postList);
                    swal('Oops', 'No se encontraron posteos',)
                }
            } catch (ex) {
                console.error('outer', ex.message);
            }
        }

        function getReactions(post) {
            var post_id = post.id;
            FB.api(
                '/' + post_id,
                'GET',
                {
                    "fields": "reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares",
                    "access_token": pageAccessToken
                },

                function (response) {
                    var div = post.id,
                        like = response.like.summary.total_count,
                        love = response.love.summary.total_count,
                        wow = response.wow.summary.total_count,
                        haha = response.haha.summary.total_count,
                        sad = response.sad.summary.total_count,
                        angry = response.angry.summary.total_count,
                        shares = '',
                        reactionsList = '';
                    if (response.shares) {
                        shares = response.shares.count;
                    } else {
                        shares = 0;
                    }
                    reactionsList +=
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Like" src="https://monitoreo.cornel.io/reacciones/like.png" alt="Like" title="Like" style=" width:20px; vertical-align: middle">' +
                        '<label id="" for="Like">' + like + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Love" src="https://monitoreo.cornel.io/reacciones/love.png" alt="Love" title="Love" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Love">' + love + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Hahaha" src="https://monitoreo.cornel.io/reacciones/hahaha.png" alt="Haha" title="Haha" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Hahaha">' + haha + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Wow" src="https://monitoreo.cornel.io/reacciones/wow.png" alt="Wow" title="Wow" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Wow">' + wow + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Sad" src="https://monitoreo.cornel.io/reacciones/sad.png" alt="Sad" title="Sad" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Sad">' + sad + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Angry" src="https://monitoreo.cornel.io/reacciones/angry.png" alt="Angry" title="Angry" style="width: 20px; vertical-align: middle">' +
                        '<label id="" for="Angry">' + angry + '</label>' +
                        '</div>' +
                        '<div class="mr-2 d-inline-block">' +
                        '<img name="Shared" src="https://monitoreo.cornel.io/reacciones/shared.png" alt="Shared" title="Shared" style="width: 18px; vertical-align: middle">' +
                        '<label id="" for="Shared">' + shares + '</label>' +
                        '</div>';
                    $("#reactions-" + div).html(reactionsList);
                }
            );
        }

        function getComments(post) {
            FB.api(
                '/' + post.id + '',
                'GET',
                {
                    "fields": "comments.limit(" + commentLimit + "){created_time,from,message,comments{message,created_time,from}}",
                    "access_token": pageAccessToken
                },
                function (response) {
                    generateComments(response, post.id);
                }
            );
        }

        function generateComments(response, post_id) {
            var comments,
                name_from = '',
                id_from = '',
                commentsList = '';
            if (response.comments) {
                comments = response.comments.data;
            } else if (response.data) {
                if ((response.data).length > 0) {
                    comments = response.data;
                }
            }
            if (comments) {
                comments.forEach(function (comment, index) {
                    if (comment.from) {
                        name_from = comment.from.name;
                        id_from = comment.id;
                    } else {
                        id_from = comment.id;
                        name_from = "Ver FB"
                    }
                    var created_time = moment(comment.created_time),
                        formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                    commentsList += '<div class="received_withd_msg">' +
                        '<p class="card-text small"><a href="https://business.facebook.com/' + id_from + '" target="_blank"><strong>' + name_from + ': </strong>' + comment.message + '</a>' +
                        '</p>' +
                        '</div>' +
                        '<p class="small text-muted">' + formated_created_time.substring(0, 10) + ' ' + formated_created_time.substring(11, 16) + '</p>';
                    if (comment.comments) {
                        var respuestas = comment.comments;
                        (respuestas.data).forEach(function (respuesta, index) {
                            if (respuesta.from) {
                                name_from = respuesta.from.name;
                                id_from = respuesta.from.id;
                            } else {
                                id_from = respuesta.id;
                                name_from = "Ver FB"
                            }
                            var created_time = moment(respuesta.created_time),
                                formated_created_time = created_time.format('YYYY-MM-DDTHH:mm:ssZ');
                            commentsList += '<div class="received_withd_msg">' +
                                '<p class="card-text small"><a href="https://business.facebook.com/' + id_from + '" target="_blank"><strong>' + name_from + ': </strong>' + respuesta.message + '</a>' +
                                '</p>' +
                                '</div>' +
                                '<p class="small text-muted">' + formated_created_time.substring(0, 10) + ' ' + formated_created_time.substring(11, 16) + '</p>';
                        })
                    }
                });
                showHidePaginationComments(response, post_id);
            }
            $('#comments-' + post_id).append(commentsList);
        }

        function showHidePaginationComments(response, post_id) {
            var commentsList = '',
                comments='';
            $('#next-' + post_id).remove();
            if (response.comments) {
                comments = response.comments;
            } else if (response.data) {
                if ((response.data).length > 0) {
                    comments = response;
                }
            }
            if (comments.paging.next) {
                var next = comments.paging.next;
                commentsList += '<div class="mb-3 row justify-content-center" >' +
                    '<button class="btn btn-success btn-sm next-data" id="next-' + post_id + '" data-next="' + next + '" data-post="' + post_id + '" onclick="nextComments(this)">Ver más comentarios</button>' +
                    '</div>';
            }else{
            }
            $('#comments-' + post_id).after(commentsList)
        }

        function getNextComments() {
            var commentsList = '';
            $('#next-' + post_id).remove();
            if (response.comments.paging.next) {
                var next = response.comments.paging.next;
                commentsList += '<div class="row justify-content-center" >' +
                    '<button class="btn btn-success btn-sm next-data" id="next-' + post_id + '" data-next="' + next + '" data-post="' + post_id + '" onclick="nextComments(this)">load more</button>' +
                    '</div>';
            } else {
            }

            $('#comments-' + post_id).after(commentsList)
        }

        function showHidePagination(response) {
            if (response) {
                loadMore = response.paging.next;
                $('#load-more').show();
            } else {
                $('#load-more').hide();
            }
        }

        function getNext() {
            FB.api(
                loadMore,
                function (response) {
                    generatePostLists(response);
                    showHidePagination(response);
                }
            );
        }

        function nextComments(btn) {
            let next = $(btn).data('next'),
                post = $(btn).data('post');
            FB.api(
                next,
                function (response) {
                    generateComments(response, post);
                }
            );
        }
    </script>

@endsection
