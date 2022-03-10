<?php


namespace App\Traits;

use App\Models\App_Fb;
use App\Models\Attachment;
use App\Models\Checkup;
use App\Models\Cron;
use App\Models\Info_page;
use App\Models\Post;
use App\Models\Reaction;
use Facebook;
use App\Models\Comment;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Carbon\Carbon;


trait ScrapTrait
{
    public function PostFB($pages){
        foreach ($pages as $page){
            //Primero se llama la aplicacion y el cron
            $app = Cron::where('page_id', $page->page_id)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appPost')
                ->first();
            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );
            $fb = new Facebook\Facebook($config);
            $pagina=$page->page_id;
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            $parametros='/posts?fields=message,story,id,full_picture,created_time,attachments{type,url,title,description}&limit=15';
            $estado=1;
            try {
                $post=$fb->get('/'.$pagina.$parametros.'',$token);
            }
            catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $statusCron = App_Fb::where('id', $app->id_appPost)->update(['status'=> 0, 'error'=> $e->getMessage()]);

                // Se registra un control general
                $register = Checkup::where('page_id', $page->page_id)->first();
                if(!$register){
                    $register = Checkup::create([
                        'page_id' => $pagina,
                        'page_name' => $page->page_name,
                        'id_appPost' => $app->id_appPost,
                        'statusPost' => 'Erros ! No se puede realizar scrap',
                        'updated_Post' => Carbon::now()
                    ]);
                }
                else{
                    $register = Checkup::where('page_id', $page->page_id)
                        ->update(['id_appPost' => $app->id_appPost,
                            'statusPost' => 'Erros ! No se puede realizar scrap',
                            'updated_Post' => Carbon::now()]);
                }


                //Envia mensaje por medio de Whatsapp
                $contactos = [$app->number_one, $app->number];
                foreach ($contactos as $contactoArray) {
                    $contacto = $contactoArray;
                    $message = "Hola! Tengo la siguiente alerta, relacionada con la aplicación de " . $app->name_app . ", el cual se indica que no se puede extraer las publicaciones de la página $page->page_name por motivos de: " . $e->getMessage();
                    $data = [
                        'phone' => $contacto,
                        'body' => $message,
                    ];
                    $json = json_encode($data); // Encode data to JSON
                    $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                    $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                    ]);
                    $result = file_get_contents($url, false, $options);
                    continue;
                }

                $estado=0;
                //return 'Graph returned an error: ' . $e->getMessage();

            }
            catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                $estado=0;
                //return 'Facebook SDK returned an error: ' . $e->getMessage();
                //exit;
            }
            if ($estado!=0){
                //Resgista la actividad
                $appFb = App_Fb::where('id', $app->id_appPost)->update(['status'=> $estado, 'error'=> 'Exito !! No se ha encontrado error']);

                //Actualiza la ultima vez que realizo scrap
                $cron = Cron::where('page_id', $pagina)->update(['updated_at'=> Carbon::now()]);

                // Se registra un control general
                $register = Checkup::where('page_name',$page->page_name)->first();
                if(!$register){
                    $register = Checkup::create([
                        'page_id' => $pagina,
                        'page_name' => $page->page_name,
                        'id_appPost' => $app->id_appPost,
                        'statusPost' => 'Exito ! Se realizo el scrap correctamente',
                        'updated_Post' => Carbon::now()
                    ]);
                }
                else{
                    $register = Checkup::where('page_name',$page->page_name)
                        ->update(['id_appPost' => $app->id_appPost,
                            'statusPost' => 'Exito ! Se realizo el scrap correctamente',
                            'updated_Post' => Carbon::now()]);
                }

                //Se obtiene las publicaciones de FB
                $post=$post->getGraphEdge();
                $postArray=$post->asArray();
                json_encode($postArray);
                foreach ($postArray as $post){
                    $content='';
                    if(array_key_exists('message', $post)){
                        $content= $post['message'];
                    }else  if(array_key_exists('story', $post)){
                        $content=$post['story'];
                    }
                    if(array_key_exists('full_picture', $post)){
                        $full_picture= $post['full_picture'];
                    }else{
                        $full_picture=Null;
                    }
                    $fecha=$post['created_time'];
                    date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                    $type=NULL;
                    $url=NULL;
                    $title=NULL;

                    if(array_key_exists('attachments', $post)){
                        $type=$post['attachments'][0]['type'];
                        if(array_key_exists('url', $post['attachments'][0])){
                            $url=$post['attachments'][0]['url'];
                        }
                        if(array_key_exists('title', $post['attachments'][0])){
                            $title=$post['attachments'][0]['title'];

                        }else{
                            if(array_key_exists('description', $post['attachments'][0])){
                                $title=$post['attachments'][0]['description'];
                            }else{
                                $title=NULL;
                            }
                        }
                    }else{
                        $type=NULL;
                        $url=NULL;
                        $title=NULL;
                    }
                    $post_id=$post['id'];
                    $postQuery=Post::where('post_id',$post_id)->first();
                    if(!$postQuery){
                        Post::create([
                            'page_id' =>$page->page_id,
                            'page_name' =>$page->page_name,
                            'post_id' =>$post_id,
                            'content' =>$content,
                            'type' =>$type,
                            'status' =>$estado,
                            'created_time'=>$fecha
                        ]);
                    }
                    $attachmentQuery=Attachment::where('post_id',$post_id)->first();
                    switch ($type){
                        case 'photo':   $url=$full_picture;
                            break;
                        case 'album':   $url=$full_picture;
                            break;
                    }
                    if (!$attachmentQuery){
                        Attachment::create([
                            'post_id'=>$post_id,
                            'picture'=>$full_picture,
                            'type'=>$type,
                            'url'=>$url,
                            'title'=>$title,
                            'page_id'=>$page->page_id,
                            'created_time'=>$fecha
                        ]);
                    }
                }
            }
        }
    }
    public function Post($pages){
        foreach ($pages as $page){
            //Primero se llama la aplicacion y el cron
            $app = Cron::where('page_id', $page->page_id)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appPost')
                ->first();
            //$limite = $app->limit;
            $limite =  ($app->limit != "") ? $app->limit : 15;
            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );
            $fb = new Facebook\Facebook($config);
            $pagina=$page->page_id;
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            $parametros='/posts?fields=message,story,id,full_picture,created_time,attachments{type,url,title,description}&limit='.$limite;
            $estado=1;
            try {
                $post=$fb->get('/'.$pagina.$parametros.'',$token);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $statusCron = App_Fb::where('id', $app->id_appPost)->update(['status'=> 0, 'error'=> $e->getMessage()]);

                //Envia mensaje por medio de Whatsapp
                $contactos = [$app->number_one, $app->number];
                foreach ($contactos as $contactoArray) {
                    $contacto = $contactoArray;
                    $message = "Hola! Tengo la siguiente alerta, relacionada con la aplicación de " . $app->name_app . ", el cual se indica que no se puede extraer las publicaciones de la página $page->page_name por motivos de: " . $e->getMessage();
                    $data = [
                        'phone' => $contacto,
                        'body' => $message,
                    ];
                    $json = json_encode($data); // Encode data to JSON
                    $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                    $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                    ]);
                    $result = file_get_contents($url, false, $options);
                    continue;
                }

                $estado=0;
                //return 'Graph returned an error: ' . $e->getMessage();

            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                $estado=0;
                //return 'Facebook SDK returned an error: ' . $e->getMessage();
                //exit;
            }
            if ($estado!=0){

                //Se obtiene las publicaciones de FB
                $post=$post->getGraphEdge();
                $postArray=$post->asArray();
                json_encode($postArray);
                foreach ($postArray as $post){
                    $content='';
                    if(array_key_exists('message', $post)){
                        $content= $post['message'];
                    }else  if(array_key_exists('story', $post)){
                        $content=$post['story'];
                    }
                    if(array_key_exists('full_picture', $post)){
                        $full_picture= $post['full_picture'];
                    }else{
                        $full_picture=Null;
                    }
                    $fecha=$post['created_time'];
                    date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                    $type=NULL;
                    $url=NULL;
                    $title=NULL;

                    if(array_key_exists('attachments', $post)){
                        $type=$post['attachments'][0]['type'];
                        if(array_key_exists('url', $post['attachments'][0])){
                            $url=$post['attachments'][0]['url'];
                        }
                        if(array_key_exists('title', $post['attachments'][0])){
                            $title=$post['attachments'][0]['title'];

                        }else{
                            if(array_key_exists('description', $post['attachments'][0])){
                                $title=$post['attachments'][0]['description'];
                            }else{
                                $title=NULL;
                            }
                        }
                    }else{
                        $type=NULL;
                        $url=NULL;
                        $title=NULL;
                    }
                    $post_id=$post['id'];
                    $postQuery=Post::where('post_id',$post_id)->first();
                    if(!$postQuery){
                        Post::create([
                            'page_id' =>$page->page_id,
                            'page_name' =>$page->page_name,
                            'post_id' =>$post_id,
                            'content' =>$content,
                            'type' =>$type,
                            'status' =>$estado,
                            'created_time'=>$fecha
                        ]);
                    }
                    $attachmentQuery=Attachment::where('post_id',$post_id)->first();
                    switch ($type){
                        case 'photo':   $url=$full_picture;
                            break;
                        case 'album':   $url=$full_picture;
                            break;
                    }
                    if (!$attachmentQuery){
                        Attachment::create([
                            'post_id'=>$post_id,
                            'picture'=>$full_picture,
                            'type'=>$type,
                            'url'=>$url,
                            'title'=>$title,
                            'page_id'=>$page->page_id,
                            'created_time'=>$fecha
                        ]);
                    }
                }


            }
        }
    }
    public function CommentsFB($pages){

        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );
        $fb = new Facebook\Facebook($config);
        $parametros='/comments?filter=stream&fields=parent.fields(id, message),message,is_hidden,from,created_time&limit=100';
        $token=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');

        foreach ($pages as $page){
            $posts=Post::where('page_id',$page->page_id)
                ->orderBy('created_time','desc')
                ->take(15)
                ->get();

            foreach ($posts as $post){
                $post_id=$post->post_id;
                $estado=0;
                try {
                    $estado=0;
                    $comentarios=$fb->get('/'.$post_id.$parametros.'',$token);

                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    $estado=1;
                    //return 'Graph returned an error: ' . $e->getMessage();

                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    //return 'Facebook SDK returned an error: ' . $e->getMessage();
                    //exit;
                    $estado=1;
                }
                if ($estado==0) {
                    $comentarios = $comentarios->getGraphEdge();
                    $next = $fb->next($comentarios);
                    $comentariosArray = $comentarios->asArray();
                    json_encode($comentariosArray);
                    foreach ($comentariosArray as $commet){
                        $id = explode( '_', $post_id);
                        $id_post = $id['1'];
                        $comment_id = (array_key_exists('id', $commet)) ? $commet['id'] : $id_post."_";
                        //$comment_id = $commet['id'];
                        if(array_key_exists('from', $commet)){
                            $commented_from = $commet['from']['name'];
                            $author_id = $commet['from']['id'];
                        }else{
                            $commented_from = 'Ver Facebook';
                            $author_id = 'Ver Facebook';
                        }

                        $comment = $commet['message'];

                        $fecha = $commet['created_time'];
                        date_timezone_set($fecha, timezone_open('America/Costa_Rica'));

                        $comment_content = htmlspecialchars($comment);

                        if ($comment_content == null) {
                            $comment_content = 'Image/Emoji';
                        }
                        $comment = $comment_content;

                        //$commentQuery=Comment::where('comment_id',$comment_id)->first();

                        /*if(!$commentQuery){
                            Comment::create([
                                'post_id' =>$post_id,
                                'comment_id'=>$comment_id,
                                'author_id' =>$author_id,
                                'commented_from'=>$commented_from,
                                'comment'=>$comment,
                                'created_time'=>$fecha,
                                'page_id'=>$page->page_id
                            ]);
                        }*/
                        $commentQuery=Comment::where('comment',$comment)->where('created_time',$fecha)->first();
                        if(!$commentQuery) {
                            $query = Comment::create([
                                'post_id' => $post_id,
                                'comment_id' => $comment_id,
                                'author_id' => $author_id,
                                'commented_from' => $commented_from,
                                'comment' => $comment,
                                'created_time' => $fecha,
                                'page_id' => $post->page_id
                            ]);
                            $update = Comment::where('id', $query->id)->update(['comment_id' => $comment_id . $query->id]);
                        }

                    }
                }else{
                    $next = null;
                }

                //$next = $fb->next($comentarios);
                if ($next) {
                    $x = 1;
                }

                while ($next != "" && $x == 1) {
                    $nextArray = $next->asArray();
                    $next = $fb->next($next);

                    foreach ($nextArray as $comentario){
                        $id = explode( '_', $post_id);
                        $id_post = $id['1'];
                        $comment_id = (array_key_exists('id', $commet)) ? $commet['id'] : $id_post."_";
                        //$comment_id = $commet['id'];
                        if(array_key_exists('from', $comentario)){
                            $commented_from = $comentario['from']['name'];
                            $author_id = $comentario['from']['id'];
                        }else{
                            $commented_from = 'Ver Facebook';
                            $author_id = 'Ver Facebook';
                        }
                        $comment = $comentario['message'];
                        $fecha = $comentario['created_time'];
                        date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                        // while $next tenga algo realice una consult
                        $comment_content = htmlspecialchars($comment);
                        if ($comment_content == null) {
                            $comment_content = 'Image/Emoji';
                        }
                        $comment = $comment_content;
                        //$commentSQL = Comment::where('comment_id',$comment_id)->first();

                        /*if (!$commentSQL) {
                            Comment::create([
                                'post_id' => $post_id,
                                'comment_id' => $comment_id,
                                'author_id' => $author_id,
                                'commented_from' => $commented_from,
                                'comment' => $comment,
                                'created_time' => $fecha,
                                'page_id'=>$page->page_id
                            ]);
                        }*/
                        $commentSQL=Comment::where('comment',$comment)->where('created_time',$fecha)->first();
                        if(!$commentSQL) {
                            $query = Comment::create([
                                'post_id' => $post_id,
                                'comment_id' => $comment_id,
                                'author_id' => $author_id,
                                'commented_from' => $commented_from,
                                'comment' => $comment,
                                'created_time' => $fecha,
                                'page_id' => $post->page_id
                            ]);
                            $update = Comment::where('id', $query->id)->update(['comment_id' => $comment_id . $query->id]);
                        }
                    }
                }
            }
        }
    }
    public function reactions($pages){

        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );

        $fb = new Facebook\Facebook($config);
        $token=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');
        $parametros = '?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';

        foreach ($pages as $page) {
            $posts = Post::where('page_id', $page->page_id)
                ->take(15)
                ->orderBy('created_time','desc')
                ->get();
            foreach ($posts as $post) {
                $post_id = $post['post_id'];
                $page_id = $post['page_id'];

                try {
                    $reacciones = $fb->get('/' . $post_id . '' . $parametros . '', $token);
                    $estado = True;
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    //$estado = 'Graph returned an error: ' . $e->getMessage();
                    //return $estado;
                    $estado = False;

                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    //$response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
                    $estado = False;
                }

                if ($estado == True) {

                    $reacciones = $reacciones->getDecodedBody();
                    $like = $reacciones['like']['summary']['total_count'];
                    $haha = $reacciones['haha']['summary']['total_count'];
                    $wow = $reacciones['wow']['summary']['total_count'];
                    $love = $reacciones['love']['summary']['total_count'];
                    $angry = $reacciones['angry']['summary']['total_count'];
                    $sad = $reacciones['sad']['summary']['total_count'];
                    if (array_key_exists('shares', $reacciones)) {
                        $share = $reacciones['shares']['count'];
                    } else {
                        $share = 0;
                    }
                    $reaccion = Reaction::where('post_id', '=', $post_id)->first();
                    if ($reaccion == null) {
                        $conversation = Reaction::create(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);
                    } else {
                        $reaccion->update(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);

                    }
                }
            }
        }
    }
    public function CommentsPosts($posts){
        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );
        $fb = new Facebook\Facebook($config);
        $parametros='/comments?filter=stream&fields=parent.fields(id, message),message,is_hidden,from,created_time&limit=100';
        //$token="285183274843495|fda8f3123eababe95aa45e20e5d51d5f";
        $token=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');
        //$token="263081807947329|6881dd87dcb6a6a4681d7ad2e7287b50";
        foreach ($posts as $post){
            $post_id=$post->post_id;
            $comentarios = "";
            $estado=0;
            try {
                $estado=0;
                $comentarios=$fb->get("/$post_id"."$parametros",$token);

            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $estado=1;
                //return 'Graph returned an error: ' . $e->getMessage();

            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                //return 'Facebook SDK returned an error: ' . $e->getMessage();
                //exit;
                $estado=1;
            }
            if ($estado==0) {
                $comentarios = $comentarios->getGraphEdge();
                $next = $fb->next($comentarios);
                $comentariosArray = $comentarios->asArray();
                json_encode($comentariosArray);
                foreach ($comentariosArray as $commet){
                    $id = explode( '_', $post_id);
                    $id_post = $id['1'];
                    $comment_id = (array_key_exists('id', $commet)) ? $commet['id'] : $id_post."_";
                    //$comment_id = $commet['id'];
                    if(array_key_exists('from', $commet)){
                        $commented_from = $commet['from']['name'];
                        $author_id = $commet['from']['id'];
                    }else{
                        $commented_from = 'Ver Facebook';
                        $author_id = 'Ver Facebook';
                    }

                    $comment = $commet['message'];

                    $fecha = $commet['created_time'];
                    date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                    $comment_content = htmlspecialchars($comment);

                    if ($comment_content == null) {
                        $comment_content = 'Image/Emoji';
                    }
                    $comment = $comment_content;

                    //$commentQuery=Comment::where('comment_id',$comment_id)->first();

                    /*if(!$commentQuery){
                        Comment::create([
                            'post_id' =>$post_id,
                            'comment_id'=>$comment_id,
                            'author_id' =>$author_id,
                            'commented_from'=>$commented_from,
                            'comment'=>$comment,
                            'created_time'=>$fecha,
                            'page_id'=>$post->page_id
                        ]);
                    }*/
                    $commentQuery=Comment::where('comment',$comment)->where('created_time',$fecha)->first();
                    if(!$commentQuery) {
                        $query = Comment::create([
                            'post_id' => $post_id,
                            'comment_id' => $comment_id,
                            'author_id' => $author_id,
                            'commented_from' => $commented_from,
                            'comment' => $comment,
                            'created_time' => $fecha,
                            'page_id' => $post->page_id
                        ]);
                        $update = Comment::where('id', $query->id)->update(['comment_id' => $comment_id . $query->id]);
                    }
                }
            }else{
                $next = null;
            }

            //$next = $fb->next($comentarios);
            if ($next) {
                $x = 1;
            }

            while ($next != "" && $x == 1) {
                $nextArray = $next->asArray();
                try { $estado=0; $next = $fb->next($next);
                } catch(Facebook\Exceptions\FacebookResponseException $e) { $estado=1;
                } catch(Facebook\Exceptions\FacebookSDKException $e) { $estado=1; }
                if($estado == 0) {
                    foreach ($nextArray as $comentario) {
                        $id = explode('_', $post_id);
                        $id_post = $id['1'];
                        $comment_id = (array_key_exists('id', $commet)) ? $commet['id'] : $id_post . "_";
                        //$comment_id = $comentario['id'];
                        if (array_key_exists('from', $comentario)) {
                            $commented_from = $comentario['from']['name'];
                            $author_id = $comentario['from']['id'];
                        } else {
                            $commented_from = 'Ver Facebook';
                            $author_id = 'Ver Facebook';
                        }
                        $comment = $comentario['message'];
                        $fecha = $comentario['created_time'];
                        date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
                        // while $next tenga algo realice una consult
                        $comment_content = htmlspecialchars($comment);
                        if ($comment_content == null) {
                            $comment_content = 'Image/Emoji';
                        }
                        $comment = $comment_content;
                        //$commentSQL = Comment::where('comment_id',$comment_id)->first();

                        /*if (!$commentSQL) {
                            Comment::create([
                                'post_id' => $post_id,
                                'comment_id' => $comment_id,
                                'author_id' => $author_id,
                                'commented_from' => $commented_from,
                                'comment' => $comment,
                                'created_time' => $fecha,
                                'page_id'=>$post->page_id
                            ]);
                        }*/
                        $commentSQL = Comment::where('comment', $comment)->where('created_time', $fecha)->first();
                        if (!$commentSQL) {
                            $query = Comment::create([
                                'post_id' => $post_id,
                                'comment_id' => $comment_id,
                                'author_id' => $author_id,
                                'commented_from' => $commented_from,
                                'comment' => $comment,
                                'created_time' => $fecha,
                                'page_id' => $post->page_id
                            ]);
                            $update = Comment::where('id', $query->id)->update(['comment_id' => $comment_id . $query->id]);
                        }
                    }
                }
            }
        }
    }
    public function ReactionsPosts($posts){
        foreach ($posts as $post) {
            $post_id = $post['post_id'];
            $page_id = $post['page_id'];
            $app = Cron::where('page_id', $page_id)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appReaction')
                ->first();

            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );
            $parametros = '?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';
            $fb = new Facebook\Facebook($config);
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            $estado=True;
            try {
                $reacciones = $fb->get('/' . $post_id . '' . $parametros . '', $token);
                $estado = True;
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $statusCron = App_Fb::where('id', $app->id_appReaction)->update(['status'=> 0, 'error'=> $e->getMessage()]);

                // Se registra un control general
                $register = Checkup::where('page_id', $page_id)->first();
                if(!$register){
                    $register = Checkup::create([
                        'page_id' => $page_id,
                        'page_name' => $post['page_name'],
                        'id_appReaction' => $app->id_appReaction,
                        'statusReaction' => 'Erros ! No se puede realizar scrap',
                        'updated_Reaction' => Carbon::now()
                    ]);
                }
                else{
                    $register = Checkup::where('page_id', $page_id)
                        ->update(['id_appReaction' => $app->id_appReaction,
                            'statusReaction' => 'Erros ! No se puede realizar scrap',
                            'updated_Reaction' => Carbon::now()]);
                }

                //Envia mensaje por medio de Whatsapp
                $contactos = [$app->number_one, $app->number];
                foreach ($contactos as $contactoArray) {
                    $contacto = $contactoArray;
                    $message = "Hola! Tengo la siguiente alerta, relacionada con la aplicación de " . $app->name_app . ", el cual se indica que no se puede extraer las reaccciones de la página". $post['page_name'] ."por motivos de: " . $e->getMessage();

                    $data = [
                        'phone' => $contacto,
                        'body' => $message,
                    ];
                    $json = json_encode($data); // Encode data to JSON
                    $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                    $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                    ]);
                    $result = file_get_contents($url, false, $options);
                    //continue;
                }
                exit;
                $estado = False;

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                //$response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
                $estado = False;
            }
            if ($estado == True) {
                $cron = App_Fb::where('id', $app->id_appReaction)->update(['status'=> $estado, 'error'=> 'Exito !! No se ha encontrado error']);

                $register = Checkup::where('page_name',$post['page_name'])->first();
                if(!$register){
                    $register = Checkup::create([
                        'page_id' => $page_id,
                        'page_name' => $post['page_name'],
                        'id_appReaction' => $app->id_appReaction,
                        'statusReaction' => 'Exito ! Se realizo scrap correctamente',
                        'updated_Reaction' => Carbon::now()
                    ]);
                }
                else{
                    $register = Checkup::where('page_name',$post['page_name'])
                        ->update(['id_appReaction' => $app->id_appReaction,
                            'statusReaction' => 'Exito ! Se realizo scrap correctamente',
                            'updated_Reaction' => Carbon::now()]);
                }

                $reacciones = $reacciones->getDecodedBody();
                $like = $reacciones['like']['summary']['total_count'];
                $haha = $reacciones['haha']['summary']['total_count'];
                $wow = $reacciones['wow']['summary']['total_count'];
                $love = $reacciones['love']['summary']['total_count'];
                $angry = $reacciones['angry']['summary']['total_count'];
                $sad = $reacciones['sad']['summary']['total_count'];
                if (array_key_exists('shares', $reacciones)) {
                    $share = $reacciones['shares']['count'];
                } else {
                    $share = 0;
                }
                $reaccion = Reaction::where('post_id', '=', $post_id)->first();
                if ($reaccion == null) {
                    $conversation = Reaction::create(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);
                } else {
                    $reaccion->update(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);
                }
            }
        }
    }
    public function ReactionsPostsGeneral($posts){
        foreach ($posts as $post) {
            $post_id = $post['post_id'];
            $page_id = $post['page_id'];
            $app = Cron::where('page_id', $page_id)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appReaction')
                ->first();

//            $app = Cron::where('apps_fb.id',16)
//                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appReaction')
//                ->first();

            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );
            $parametros = '?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';
            $fb = new Facebook\Facebook($config);
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            $estado=True;
            try {
                $reacciones = $fb->get('/' . $post_id . '' . $parametros . '', $token);
                $estado = True;
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $statusCron = App_Fb::where('id', $app->id_appReaction)->update(['status'=> 0, 'error'=> $e->getMessage()]);

                //Envia mensaje por medio de Whatsapp
                $contactos = [$app->number_one, $app->number];
                foreach ($contactos as $contactoArray) {
                    $contacto = $contactoArray;
                    $message = "Hola! Tengo la siguiente alerta, relacionada con la aplicación de " . $app->name_app . ", el cual se indica que no se puede extraer las reaccciones de la página". $post['page_name'] ."por motivos de: " . $e->getMessage();

                    $data = [
                        'phone' => $contacto,
                        'body' => $message,
                    ];
                    $json = json_encode($data); // Encode data to JSON
                    $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                    $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                    ]);
                    //$result = file_get_contents($url, false, $options);
                    //continue;
                }
                $estado = False;

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                //$response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
                $estado = False;
            }
            if ($estado == True) {
                $cron = App_Fb::where('id', $app->id_appReaction)->update(['status'=> $estado, 'error'=> 'Exito !! No se ha encontrado error']);

                $reacciones = $reacciones->getDecodedBody();
                $like = $reacciones['like']['summary']['total_count'];
                $haha = $reacciones['haha']['summary']['total_count'];
                $wow = $reacciones['wow']['summary']['total_count'];
                $love = $reacciones['love']['summary']['total_count'];
                $angry = $reacciones['angry']['summary']['total_count'];
                $sad = $reacciones['sad']['summary']['total_count'];
                if (array_key_exists('shares', $reacciones)) {
                    $share = $reacciones['shares']['count'];
                } else {
                    $share = 0;
                }
                $reaccion = Reaction::where('post_id', '=', $post_id)->first();
                if ($reaccion == null) {
                    $conversation = Reaction::create(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);
                } else {
                    $reaccion->update(['post_id' => $post_id, 'likes' => $like, 'haha' => $haha, 'wow' => $wow, 'love' => $love, 'angry' => $angry, 'sad' => $sad, 'shared' => $share, 'page_id' => $page_id]);
                }
            }
        }
    }
    public function InformationPage($pages){
        foreach ($pages as $page){
            $page_id = $page;
            $app = Cron::where('page_id', $page)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appPost')
                ->first();
            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );

            $parametros='?fields=picture,fan_count,category,about,company_overview,location,phone,emails,talking_about_count,name';
            $fb = new Facebook\Facebook($config);
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            try {
                $infoPage= $fb->get('/' . $page_id . '' . $parametros . '', $token);
                $estado = True;
            }
            catch (Facebook\Exceptions\FacebookResponseException $e){
                $estado=False;
            }
            catch(Facebook\Exceptions\FacebookSDKException $e) {
                $estado=False;
            }
            if ($estado == True){
                $data = $infoPage->getDecodedBody();
                $page_name = $data['name'];
                $picture = $data['picture']['data']['url'];
                $fan_count = $data['fan_count'];
                $category = $data['category'];
                $about = $data['about'];
                $location = $data['location']['city'];
                $talking = $data['talking_about_count'];
                if (array_key_exists('emails', $data)) {
                    $emails = $data['emails'][0];
                } else {
                    $emails = '';
                }
                if (array_key_exists('phone', $data)) {
                    $phone = $data['phone'];
                } else {
                    $phone = 0;
                }
                if (array_key_exists('company_overview', $data)) {
                    $company_overview = $data['company_overview'];
                } else {
                    $company_overview = '';
                }
                $info = Info_page::where('page_id', '=', $page_id)->first();
                if (!$info) {
                    $info = Reaction::create(['page_id' =>$page_id, 'page_name' => $page_name, 'fan_count' => $fan_count,
                        'category' =>$category, 'about' => $about, 'company_overview' => $company_overview, 'location' => $location,
                        'phone' => $phone, 'emails' => $emails, 'talking' => $talking, 'picture' => $picture]);
                } else {
                    $info->update(['fan_count' => $fan_count, 'category' =>$category, 'about' => $about, 'company_overview' => $company_overview,
                        'location' => $location,'phone' => $phone, 'emails' => $emails, 'talking' => $talking, 'picture' => $picture]);
                }
            }
        }
    }

}