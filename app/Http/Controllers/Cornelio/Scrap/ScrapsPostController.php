<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Sentiment;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facebook;

class ScrapsPostController extends Controller
{
    public function index()
    {
        return view('Cornelio.Scraps.ScrapsPost.index');
    }

    public function getPost(Request $request){

        $pageId = $request->pageId;
        $pageName = $request->pageName;
        $postId = str_replace('/','', $request->postId);

        $validate = Post::where('post_id', $postId)->first();
        try{
        if(!$validate){
            $post = $this->scrapPost($pageId, $pageName, $postId);
        }

        $reaction = $this->scrapReaction($pageId, $postId);
        $comment = $this->scrapsComments($pageId, $postId);
        $publication = Post::where('post_id', $postId)
            ->with(['attachment', 'reactions', 'comments'=>function($q){
                $q->take(10);}])
            ->first();
        }catch(\exception $e) {
            return 500;
        }
        return $publication;
    }

    public function scrapPost($pageId, $pageName, $postId)
    {
        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );

        $fb = new Facebook\Facebook($config);
        $token= (env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2'));
        $parametros='/?fields=message,story,id,full_picture,created_time,attachments{type,url,title,description}';
        $estado=1;
        try {
            $post=$fb->get('/'.$postId.$parametros.'',$token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $estado=0;
            return 'Graph returned an error: ' . $e->getMessage();

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $estado=0;
            return 'Facebook SDK returned an error: ' . $e->getMessage();
            //exit;
        }
        if ($estado!=0){
            //Se obtiene las publicaciones de FB
            $postArray=$post->getGraphObject()->asArray();
            json_encode($postArray);
            $content='';
            if(array_key_exists('message', $postArray)){
                $content= $postArray['message'];
            }else  if(array_key_exists('story', $postArray)){
                $content=$postArray['story'];
            }
            if(array_key_exists('full_picture', $postArray)){
                $full_picture= $postArray['full_picture'];
            }else{
                $full_picture=Null;
            }
            $fecha=$postArray['created_time'];
            date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
            $type=NULL;
            $url=NULL;
            $title=NULL;

            if(array_key_exists('attachments', $postArray)){
                $type=$postArray['attachments'][0]['type'];
                if(array_key_exists('url', $postArray['attachments'][0])){
                    $url=$postArray['attachments'][0]['url'];
                }
                if(array_key_exists('title', $postArray['attachments'][0])){
                    $title=$postArray['attachments'][0]['title'];

                }else{
                    if(array_key_exists('description', $postArray['attachments'][0])){
                        $title=$postArray['attachments'][0]['description'];
                    }else{
                        $title=NULL;
                    }
                }
            }else{
                $type=NULL;
                $url=NULL;
                $title=NULL;
            }

            $post_id=$postArray['id'];
            $postQuery=Post::where('post_id',$post_id)->first();

            if(!$postQuery){
                Post::create([
                    'page_id' =>$pageId,
                    'page_name' =>$pageName,
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
                    'page_id'=>$pageId,
                    'created_time'=>$fecha
                ]);
            }
        }
    }

    public function scrapReaction($pageId, $postId){
        $post_id = $postId;
        $page_id = $pageId;
        $response = new \stdClass();
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );
        $fb = new Facebook\Facebook($config);
        $token= (env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2'));
        $parametros='?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';

        try {
            $reacciones=$fb->get('/'.$post_id.''.$parametros.'',$token);
            $response->estado = True;
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $response->estado = 'Graph returned an error: ' . $e->getMessage();
            return $response;

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
            $postEliminado = Post::where('post_id', '=', $post_id)->first();
            $postEliminado->update(['status' => 'Eliminado']);
            return $response;
        }

        if ($response->estado == True) {

            $reacciones=$reacciones->getDecodedBody();
            $like=$reacciones['like']['summary']['total_count'];
            $haha=$reacciones['haha']['summary']['total_count'];
            $wow=$reacciones['wow']['summary']['total_count'];
            $love=$reacciones['love']['summary']['total_count'];
            $angry=$reacciones['angry']['summary']['total_count'];
            $sad=$reacciones['sad']['summary']['total_count'];
            if(array_key_exists('shares', $reacciones)){
                $share=$reacciones['shares']['count'];
            }else{
                $share=0;
            }

            $reaccion=Reaction::where('post_id','=',$post_id)->first();
            if ($reaccion == null) {
                $reaccion = Reaction::create(['post_id'=>$post_id, 'page_id'=>$page_id, 'likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share]);
            } else {
                $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share]);
            }
            $response->reacciones=$reaccion;
        }
        return Response()->json($response);
    }

    public function scrapsComments($pageId, $postId){

        $post_id = $postId;
        $page_id = $pageId;

        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );
        $fb = new Facebook\Facebook($config);
        $token= (env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2'));
        $estado = False;
        $parametros='/comments?fields=created_time,from,message,comments,parent.fields(message,created_time,from)';
        try {
            $comentarios=$fb->get('/'.$post_id.$parametros,$token);
            $estado= True;
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $postEliminado=Post::where('post_id','=',$post_id)->first();
            $postEliminado->update(['status'=>'Eliminado']);
            $estado = False;
            return 'Facebook returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $estado = False;
            exit;
            return 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        if ($estado == True) {
            $comentarios = $comentarios->getGraphEdge();
            $comentariosArray = $comentarios->asArray();
            $count = count($comentariosArray);
            for ($i = 0; $i < $count; $i++) {

                $comment_id = $comentariosArray[$i]['id'];
                if(array_key_exists('from', $comentariosArray[$i])){
                    $commented_from = $comentariosArray[$i]['from']['name'];
                    $author_id = $comentariosArray[$i]['from']['id'];
                }else{
                    $commented_from = "Sin";
                    $author_id = "Sin";
                }

                if(array_key_exists('comments', $comentariosArray[$i])){
                    $countRespuesta = count($comentariosArray[$i]['comments']);
                    for ($k = 0; $k < $countRespuesta; $k++) {

                        $comment_id = $comentariosArray[$i]['comments'][$k]['id'];

                        if(array_key_exists('from', $comentariosArray[$i]['comments'][$k])){
                            $commented_from = $comentariosArray[$i]['comments'][$k]['from']['name'];
                            $author_id = $comentariosArray[$i]['comments'][$k]['from']['id'];
                        }else{
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
                            Comment::create(['post_id' => $post_id, 'page_id' => $page_id,'comment_id' => $comment_id, 'author_id' => $author_id,
                                'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);

                        }

                    }
                }
                $comment = $comentariosArray[$i]['message'];
                $fecha = $comentariosArray[$i]['created_time'];
                date_timezone_set($fecha, timezone_open('America/Costa_Rica'));

                // while $next tenga algo realice una consulta
                $comment_content = htmlspecialchars($comment);
                if ($comment_content == null) {
                    $comment_content = 'Image/Emoji';
                }
                $comment = $comment_content;

                $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();

                if ($commentSQL == null) {
                    $p =Comment::create(['post_id' => $post_id, 'page_id' => $page_id,'comment_id' => $comment_id, 'author_id' => $author_id,
                        'commented_from' => $commented_from, 'comment' => $comment, 'created_time' => $fecha]);
                } else {
                    //$commentSQL->update($request->all());
                    //$commentSQL->update($request->except(['comment_id']));
                }
            }
        }
        return Response()->json($estado);
    }

    public function wordClassification(Request $request)
    {
        //Se llama las palabras
        $word = Word::get();

        //Se llama los comentarios que se encuentra clasificado
        $comments = Comment::where('post_id',  str_replace('/','', $request->post))
            ->orderby('created_at', 'DESC')
            ->get();

        //recorre cada palabra para comparar en los post
        foreach ($word as $palabra){
            $comp=$palabra['word'];
            $sentimiento=$palabra['sentiment'];

            //recorre cada uno de los comentarios que se encuentra clasifcado
            foreach ($comments as $comment){
                if(isset($comment->comment)){
                    $content=$comment->comment;
                    $comment_id = $comment->comment_id;
                    $find = stripos($content, $comp);
                    if($find == true){
                        $consulta = Sentiment::where('comment_id', $comment_id)->first();
                        if(!$consulta){
                            $getSentiment = Sentiment::create([
                                'comment_id' => $comment_id,
                                'sentiment' => $sentimiento,
                                'estado' => '0',
                            ]);
                        }
                        continue;
                    }
                }
            }
        }

        // Se debera consultar por los comemtarios que no se encuentra como positivo ni negativo
        foreach ($comments as $comentario){
            if(isset($comentario->comment)){
                $id_comment = $comentario->comment_id;
                $consulta = Sentiment::where('comment_id', $id_comment)->first();
                if(!$consulta){
                    $getSentiment = Sentiment::create([
                        'comment_id' => $id_comment,
                        'sentiment' => 'Neutral',
                        'estado' => '0',
                    ]);
                }
            }
        }
        $select = $this->commentsFeeling($request->post);
        return $select;
    }

    public function commentsFeeling($post_id){

        $rating = Comment::where('post_id',str_replace('/','', $post_id))
            ->with('sentiment')
            ->select('comment_id')
            ->get();
        $interation = 0;
        $positivo = 0;
        $negativo = 0;
        $neutral = 0;
        $i = 0;

        foreach ($rating as $comment){
            if(isset($comment['sentiment'])){
                if($comment['sentiment']->sentiment == 'Positivo'){
                    $positivo++;
                }
                elseif ($comment['sentiment']->sentiment == 'Negativo'){
                    $negativo++;
                }
                elseif ($comment['sentiment']->sentiment == 'Neutral'){
                    $neutral++;
                }
                $interation++;
            }
        }

        $data['Tema'][$i]['negativo']=$interation ? round((($negativo / $interation) * 100),2) : $interation;
        $data['Tema'][$i]['neutral']=$interation ? round((($neutral / $interation) * 100),2) : $interation;
        $data['Tema'][$i]['positivo']=$interation ? round((($positivo / $interation) * 100),2) : $interation;
        $i++;

        return $data;
    }


}
