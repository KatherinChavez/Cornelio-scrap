<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Cron;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Illuminate\Http\Request;
use App\Http\Controllers\Facebook;

class Last100Controller extends Controller
{
    public function ScrapLast(){
        return view('Cornelio.Scraps.Last100.ScrapLast');
    }

    //Se preguntara por las conversaciones que se presentaron en la página
    public function lastPost(Request $request){
        $config = array(
            'app_id' => env('APP_FB_ID'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );
        $fb = new Facebook\Facebook($config);
        //$token=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');
        $token = env('APP_FB_TOKEN_3');

        $parametros = '/posts?fields=message,story,id,full_picture,created_time,attachments{type,url,title,description}&limit=100';
        $post = $fb->get('/' . $request->pagina . $parametros . '', $token);
        $post = $post->getGraphEdge();
        $postArray = $post->asArray();
        $count = count($postArray);

        for ($i = 0; $i < $count; $i++) {
            $post_id = $postArray[$i]['id'];
            if (array_key_exists('source', $postArray[$i])) {
                $source = $postArray[$i]['source'];
            } else {
                $source = null;
            }

            if (array_key_exists('message', $postArray[$i])) {
                $content = $postArray[$i]['message'];
            } else if (array_key_exists('story', $postArray[$i])) {
                $content = $postArray[$i]['story'];
            }
            if (array_key_exists('full_picture', $postArray[$i])) {
                $full_picture = $postArray[$i]['full_picture'];
            } else {
                $full_picture = null;
            }

            $fecha = $postArray[$i]['created_time'];
            date_timezone_set($fecha, timezone_open('America/Costa_Rica'));

            if (array_key_exists('attachments', $postArray[$i])) {
                $type = $postArray[$i]['attachments'][0]['type'];
                $url = $postArray[$i]['attachments'][0]['url'];
                if (array_key_exists('title', $postArray[$i]['attachments'][0])) {
                    $title = $postArray[$i]['attachments'][0]['title'];

                } else {
                    if (array_key_exists('description', $postArray[$i]['attachments'][0])) {
                        $title = $postArray[$i]['attachments'][0]['description'];
                    } else {
                        $title = null;
                    }
                }

            } else {
                $type = null;
                $url = null;
                $title2 = null;
            }

            $postSQL = Post::where('post_id', '=', $post_id)->first();
            $adjuntoSQL = Attachment::where('post_id', '=', $post_id)->first();

            if ($adjuntoSQL == null) {
                $con = 0;
                if ($full_picture !== null) {

                    if ($type == 'share') {
                        Attachment::create(['post_id' => $post_id,
                            'url' => $url,
                            'picture' => $full_picture,
                            'title' => $title,
                            'page_id' => $request->pagina,
                            'created_time' => $fecha]);

                        if ($postSQL == null) {
                            Post::create(['page_id' => $request->pagina,
                                'page_name' => $request->name,
                                'post_id' => $post_id,
                                'content' => $content,
                                'created_time' => $fecha,
                                'type' => 'Link']);
                        } else {
                            Post::where('post_id', '=', $post_id)->update(['type' => 'Link']);
                        }
                        $con = 1;

                    }
                    if ($con == 0) {
                        if ($source !== null) {
                            Attachment::create(['post_id' => $post_id,
                                'video' => $source,
                                'page_id' => $request->pagina,
                                'created_time' => $fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->pagina,
                                    'page_name' => $request->name,
                                    'post_id' => $post_id,
                                    'content' => $content,
                                    'created_time' => $fecha,
                                    'type' => 'Video']);

                            } else {
                                Post::where('post_id', '=', $post_id)->update(['type' => 'Video']);
                            }

                        } else {
                            Attachment::create(['post_id' => $post_id,
                                'picture' => $full_picture,
                                'page_id' => $request->pagina,
                                'created_time' => $fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->pagina,
                                    'page_name' => $request->name,
                                    'post_id' => $post_id,
                                    'content' => $content,
                                    'created_time' => $fecha,
                                    'type' => 'Photo']);
                            } else {
                                Post::where('post_id', '=', $post_id)->update(['type' => 'Photo']);
                            }
                        }
                    }
                }
            } else {
                $con = 0;
                if ($full_picture !== null) {
                    if ($type == 'share') {
                        if ($postSQL == null) {
                            Post::create(['page_id' => $request->pagina,
                                'page_name' => $request->name,
                                'post_id' => $post_id,
                                'content' => $content,
                                'created_time' => $fecha,
                                'type' => 'Link']);
                        } else {
                            Post::where('post_id', '=', $post_id)->update(['type' => 'Link']);
                        }
                        $con = 1;
                    }
                    if ($con == 0) {
                        if ($source !== null) {

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->pagina,
                                    'page_name' => $request->name,
                                    'post_id' => $post_id,
                                    'content' => $content,
                                    'created_time' => $fecha,
                                    'type' => 'Video']);
                            } else {
                                Post::where('post_id', '=', $post_id)->update(['type' => 'Video']);
                            }
                        } else {

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->pagina,
                                    'page_name' => $request->name,
                                    'post_id' => $post_id,
                                    'content' => $content,
                                    'created_time' => $fecha,
                                    'type' => 'Photo']);
                            } else {
                                Post::where('post_id', '=', $post_id)->update(['type' => 'Photo']);
                            }
                        }
                    }
                }
            }
        }
        $fb2 = new Facebook\Facebook($config);
        $post = Post::where('page_id', '=', $request->pagina)
            ->orderby('created_time', 'DESC')
            ->take(100)
            ->get();

        foreach ($post as $index) {

            $estado = "True";
            $post_id = $index->post_id;
            $p_id = $index->post_id;
            $parametros = '/comments?fields=created_time,from,message,comments,parent.fields(message,created_time,from)&limit=4000';

            try {
                $comentarios = $fb2->get('/' . $post_id . $parametros . '', $token);

            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $postEliminado = Post::where('post_id', '=', $post_id)->first();
                $postEliminado->update(['status' => 'Eliminado']);
                $estado = "False";
                return 'Graph returned an error: ' . $e->getMessage();

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                return 'Facebook SDK returned an error: ' . $e->getMessage();
                //exit;
            }

            if ($estado != "False") {

                $comentarios = $comentarios->getGraphEdge();
                $comentariosArray = $comentarios->asArray();
                $count = count($comentariosArray);

                for ($i = 0; $i < $count; $i++) {

                    $comment_id = $comentariosArray[$i]['id'];

                    if (array_key_exists('from', $comentariosArray[$i])) {
                        $commented_from = $comentariosArray[$i]['from']['name'];
                        $author_id = $comentariosArray[$i]['from']['id'];
                    } else {
                        $commented_from = "Sin";
                        $author_id = "Sin";
                    }

                    if (array_key_exists('comments', $comentariosArray[$i])) {

                        $countRespuesta = count($comentariosArray[$i]['comments']);
                        for ($k = 0; $k < $countRespuesta; $k++) {
                            $comment_id = $comentariosArray[$i]['comments'][$k]['id'];

                            if (array_key_exists('from', $comentariosArray[$i]['comments'][$k])) {
                                $commented_from = $comentariosArray[$i]['comments'][$k]['from']['name'];
                                $author_id = $comentariosArray[$i]['comments'][$k]['from']['id'];
                            } else {
                                $commented_from = "Res";
                                $author_id = "Res";
                            }

                            $comment = $comentariosArray[$i]['comments'][$k]['message'];
                            $fecha = $comentariosArray[$i]['comments'][$k]['created_time'];
                            date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                            $comment_content = htmlspecialchars($comment);

                            if ($comment_content == null) {
                                $comment_content = 'Image/Emoji';
                            }

                            $comment = $comment_content;
                            $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();

                            if ($commentSQL == null) {
                                Comment::create(['post_id' => $post_id, 'page_id' => $request->pagina, 'comment_id' => $comment_id, 'author_id' => $author_id,
                                    'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);
                            } else {
                                //$commentSQL->update($request->all());
                                //$commentSQL->update($request->except(['comment_id']));
                            }
                        }
                    }

                    $comment = $comentariosArray[$i]['message'];
                    $fecha = $comentariosArray[$i]['created_time'];
                    date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                    $comment_content = htmlspecialchars($comment);

                    if ($comment_content == null) {
                        $comment_content = 'Image/Emoji';
                    }

                    $comment = $comment_content;
                    $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();

                    if ($commentSQL == null) {
                        Comment::create(['post_id' => $post_id, 'page_id' => $request->pagina, 'comment_id' => $comment_id, 'author_id' => $author_id,
                            'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);
                    } else {
                        //$commentSQL->update($request->all());
                        //$commentSQL->update($request->except(['comment_id']));
                    }
                }

                $next = $fb2->next($comentarios);
                if ($next) {
                    $x = 1;
                }
                while ($next != "" && $x == 1) {
                    $nextArry = $next->asArray();
                    $next = $fb->next($next);
                    $count2 = count($nextArry);

                    for ($j = 0; $j < $count2; $j++) {

                        $comment_id = $nextArry[$j]['id'];

                        if (array_key_exists('from', $nextArry[$j])) {
                            $commented_from = $nextArry[$j]['from']['name'];
                            $author_id = $nextArry[$j]['from']['id'];
                        } else {
                            $commented_from = "Sin";
                            $author_id = "Sin";
                        }

                        if (array_key_exists('comments', $nextArry[$j])) {

                            $countRespuesta2 = count($nextArry[$j]['comments']);

                            for ($l = 0; $l < $countRespuesta2; $l++) {

                                $comment_id = $nextArry[$j]['comments'][$l]['id'];

                                if (array_key_exists('from', $nextArry[$j]['comments'][$l])) {
                                    $commented_from = $nextArry[$j]['comments'][$l]['from']['name'];
                                    $author_id = $nextArry[$j]['comments'][$l]['from']['id'];
                                } else {
                                    $commented_from = "Res";
                                    $author_id = "Res";
                                }

                                $comment = $nextArry[$j]['comments'][$l]['message'];
                                $fecha = $nextArry[$j]['comments'][$l]['created_time'];
                                date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                                $comment_content = htmlspecialchars($comment);

                                if ($comment_content == null) {
                                    $comment_content = 'Image/Emoji';
                                }

                                $comment = $comment_content;
                                $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();

                                if ($commentSQL == null) {
                                    Comment::create(['post_id' => $post_id, 'page_id' => $request->pagina, 'comment_id' => $comment_id, 'author_id' => $author_id,
                                        'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);
                                } else {
                                    //$commentSQL->update($request->all());
                                    //$commentSQL->update($request->except(['comment_id']));
                                }
                            }
                        }

                        $comment = $nextArry[$j]['message'];
                        $fecha = $nextArry[$j]['created_time'];
                        date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                        $comment_content = htmlspecialchars($comment);

                        if ($comment_content == null) {
                            $comment_content = 'Image/Emoji';
                        }

                        $comment = $comment_content;
                        $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();

                        if ($commentSQL == null) {
                            Comment::create(['post_id' => $post_id, 'page_id' => $request->pagina, 'comment_id' => $comment_id, 'author_id' => $author_id,
                                'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);
                        } else {
                            //$commentSQL->update($request->all());
                            //$commentSQL->update($request->except(['comment_id']));
                        }
                    }
                    if ($next) {

                    } else {
                        $x = 0;
                    }
                }
            }
        }

        $fb3 = new Facebook\Facebook($config);
        //Reacciones que se encuentra en los comentarios del post
        foreach ($post as $index) {

            $post_id = $index->post_id;
            $page_id = $index->page_id;
            $estado = "True";
            $parametros = '/insights?metric=post_reactions_by_type_total';

            try {
                $reacciones = $fb3->get('/' . $post_id . '' . $parametros . '', $token);
                $reacciones = $reacciones->getGraphEdge();
                $next = $fb->next($reacciones);
                $reaccionesArray = $reacciones->asArray();
                json_encode($reaccionesArray);

            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $postEliminado = Post::where('post_id', '=', $post_id)->first();
                $postEliminado->update(['status' => 'Eliminado']);
                $estado = "False";
                //return 'Graph returned an error: ' . $e->getMessage();

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                //return 'Facebook SDK returned an error: ' . $e->getMessage();
                //exit;
            }

            if ($estado == "True") {
                $count = count($reaccionesArray);
                $postReactionSQL = Reaction::where('post_id', '=', $post_id)->first();
                $ClasificacionReaction = json_encode($reaccionesArray[0]['values'][0]['value']);

                if ($postReactionSQL == null) {
                    $reaccion = Reaction::create(['post_id' => $post_id, 'page_id' => $page_id, 'reacciones' => $ClasificacionReaction]);

                } else {
                    $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['reacciones' => $ClasificacionReaction]);
                }
            }
        }
        return "ok";
    }

    public function page(Request $request){
        $company_id=session('company_id');
        $cron = Cron::/*where('company_id', $company_id )->*/pluck('page_id');
        $paginas = Scraps::/*where('scraps.company_id', $company_id )
            ->*/whereNotIn('page_id',$cron)
            ->groupBy('page_id')
            ->get();
        return $paginas;
    }
}
