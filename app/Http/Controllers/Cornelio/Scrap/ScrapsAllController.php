<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Fan;
use App\Models\Info_page;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facebook;

class ScrapsAllController extends Controller
{

    public function selectPage(){
        $company=session('company');
        $user_id=Auth::id();
        $compain=Company::where('slug',$company)->first();
        $companies=$compain->id;
        $categories=Category::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Scraps.ScrapsAll.scrapAll',compact('categories'));

    }

    public function scrapAll(){
        return view('Cornelio.Scraps.ScrapsAll.scrapAll');
    }

    //Muestra la información que se encuentra en la pág
    public function informationPage(Request $request){

        /*Se llamara al modelo 'info_page' y
         preguntara si la pagina es igual al que se encuentra a la pág que se encuentra en el modelo*/
        $information=Info_page::where('page_id','=',$request->page_id)->first();
        if ($information == null) {
            $information = Info_page::create($request->all());
        } else {
            $information->update($request->all());
        }
        return $information;
        echo "Guardado Information";
    }

    public function getPost(Request $request){
        $limit=($request->limit) ? $request->limit : 10;
        $limit=$limit*1;
        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );
        $fb = new Facebook\Facebook($config);
//        $token=env('APP_FB_TOKEN_2');
        $token=$request->pageAccessToken;
//         $token=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');
        $parametros='/posts?fields=message,story,id,full_picture,created_time,attachments{type,url,title,description}&limit='.$limit;

//        $fb = new Facebook\Facebook($config);
        $estado = False;

        try {
            $post=$fb->get('/'.$request->page_id.$parametros.'',$token);
            $estado = True;
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $estado = False;
//            return 'Graph returned an error: ' . $e->getMessage();

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $estado = False;
//            return 'Facebook SDK returned an error: ' . $e->getMessage();
            //exit;
        }
        $post=$post->getGraphEdge();
        $postArray=$post->asArray();
        $count = count($postArray);
        $content='';
        for ($i = 0; $i < $count; $i++) {
            $post_id=$postArray[$i]['id'];
            if(array_key_exists('source', $postArray[$i])){
                $source=$postArray[$i]['source'];

            }
            else{
                $source=Null;
            }

            if(array_key_exists('message', $postArray[$i])){
                $content=$postArray[$i]['message'];
            }else  if(array_key_exists('story', $postArray[$i])){
                $content=$postArray[$i]['story'];
            }
            if(array_key_exists('full_picture', $postArray[$i])){
                $full_picture= $postArray[$i]['full_picture'];
            }else{
                $full_picture=Null;
            }

            $fecha=$postArray[$i]['created_time'];
            date_timezone_set($fecha, timezone_open('America/Costa_Rica'));
            if(array_key_exists('attachments', $postArray[$i])){
                $type=$postArray[$i]['attachments'][0]['type'];
                $url=$postArray[$i]['attachments'][0]['url'];
                if(array_key_exists('title', $postArray[$i]['attachments'][0])){
                    $title=$postArray[$i]['attachments'][0]['title'];
                }
                else{
                    if(array_key_exists('description', $postArray[$i]['attachments'][0])){
                        $title=$postArray[$i]['attachments'][0]['description'];
                    }else{
                        $title=NULL;
                    }
                }
            }
            else{
                $type=NULL;
                $url=NULL;
                $title2=NULL;
            }

            $postSQL = Post::where('post_id', '=', $post_id)->first();
            $adjuntoSQL=Attachment::where('post_id','=',$post_id)->first();

            if ($adjuntoSQL == null) {
                $con=0;
                if($full_picture!==Null){
                    if($type=='share'){
                        Attachment::create(['post_id'=>$post_id,
                            'url'=>$url,
                            'picture'=>$full_picture,
                            'title'=>$title,
                            'page_id'=>$request->page_id,
                            'created_time'=>$fecha]);

                        if ($postSQL == null) {
                            $p = Post::create([  'page_id' => $request->page_id,
                                'page_name' => $request->name,
                                'post_id'=>$post_id,
                                'content'=>$content,
                                'created_time'=>$fecha,
                                'type'=>'Link']);
                        }
                        else{
                            Post::where('post_id', '=', $post_id)->update(['type'=>'Link']);
                        }
                        $con=1;

                    }
                    if($con==0){
                        if($source!==Null){
                            Attachment::create(['post_id'=>$post_id,
                                'video'=>$source,
                                'page_id'=>$request->page_id,
                                'created_time'=>$fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->page_id,
                                    'page_name' => $request->name,
                                    'post_id'=>$post_id,
                                    'content'=>$content,
                                    'created_time'=>$fecha,
                                    'type'=>'Video']);
                            }
                            else{
                                Post::where('post_id', '=', $post_id)->update(['type'=>'Video']);
                            }
                        }
                        else{

                            Attachment::create(['post_id'=>$post_id,
                                'picture'=>$full_picture,
                                'page_id'=>$request->page_id,
                                'created_time'=>$fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->page_id,
                                    'page_name' => $request->name,
                                    'post_id'=>$post_id,
                                    'content'=>$content,
                                    'created_time'=>$fecha,
                                    'type'=>'Photo']);
                            }
                            else{
                                Post::where('post_id', '=', $post_id)->update(['type'=>'Photo']);
                            }
                        }
                    }
                }
            }
            else {

                $con=0;
                if($full_picture!==Null){
                    if($type=='share'){
                        if ($postSQL == null) {
                            Post::create(['page_id' => $request->page_id,
                                'page_name' => $request->name,
                                'post_id'=>$post_id,
                                'content'=>$content,
                                'created_time'=>$fecha,'type'=>'Link']);
                        }else{
                            Post::where('post_id', '=', $post_id)->update(['type'=>'Link']);
                        }
                        $con=1;
                    }

                    if($con==0){
                        if($source!==Null){
                            if ($postSQL == null) {
                                Post::create(['page_id' => $request->page_id,
                                    'page_name' => $request->name,
                                    'post_id'=>$post_id,
                                    'content'=>$content,
                                    'created_time'=>$fecha,
                                    'type'=>'Video']);
                            }else{
                                Post::where('post_id', '=', $post_id)->update(['type'=>'Video']);
                            }
                        }
                        else{

                            if ($postSQL == null) {
                                $p =  Post::create(['page_id' => $request->page_id,
                                    'page_name' => $request->name,
                                    'post_id'=>$post_id,
                                    'content'=>$content,
                                    'created_time'=>$fecha,
                                    'type'=>'Photo']);
                            }else{
                                Post::where('post_id', '=', $post_id)->update(['type'=>'Photo']);
                            }
                        }
                    }
                }
            }
        }
        $next=$fb->next($post);
        if($next){
            $x=1;
            //$veces=0;
        }
        $post=Post::where('post_id','=',$request->page_id)->first();
        if($post!=null){
            $post->destroy;
        }
        // return "Completed..";
        return Response()->json($estado);
    }

    public function commentsAll($company){

        $user_id=Auth::id();
        $compain=Company::where('slug',$company)->first();
        $companies=$compain->id;
        $categories=Category::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Scraps.ScrapsAll.commentsAll');
    }

    public function getComments (Request $request){
        $limit=($request->limit) ? $request->limit : 10;
        $posts=Post::where('page_id','=',$request->page_id)->orderBy('created_at','DESC')->take($limit)->get();
        $page_id = $request->page_id;
        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );
        $fb = new Facebook\Facebook($config);
        $token=$request->pageAccessToken;
        $estado = False;
        foreach ($posts as $post){
            $post_id= $post->post_id;
            $parametros="/comments?filter=stream&fields=parent.fields(id, message),message,is_hidden,from,created_time&limit=$limit";
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
            }
        }
        return Response()->json($estado);
    }

    //Reacciones presentadas en las publicaciones
    //Scrap de las reacciones
    public function ScrapReaction(Request $request){
        $limit=($request->limit) ? $request->limit : 10;

        $posts = Post::where('page_id', '=', $request->page_id)->orderBy('created_at','DESC')->take($limit)->get();
        $response = new \stdClass();
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );

        $fb = new Facebook\Facebook($config);
        $token=$request->pageAccessToken;
        $parametros='?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';

        foreach ($posts as $post){
            $post_id=$post['post_id'];
            $page_id=$post['page_id'];
            $estado='True';

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
                if (!$reaccion) {
                    $reaccion = Reaction::create(['post_id'=>$post_id,'likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share,'page_id'=>$page_id]);
                } else {
                    //$reaccion->update(['post_id'=>$post_id,'likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share,'page_id'=>$page_id]);
                    $reaccion->update(['likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share]);

                }
                $response->reacciones=$reaccion;
                //dd($request->all());

            }
        }

        return Response()->json($response);
    }

    public function ScrapReactionJson(Request $request){
        $post = Post::where('page_id', '=', $request->page_id)->get();
        $response = new \stdClass();
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );
        $fb = new Facebook\Facebook($config);
        $token=$request->pageAccessToken;
        //$token3=env('APP_FB_ID_2')."|".env('APP_FB_SECRET_2');
        //$token=env('APP_FB_TOKEN');
        //dd($toke2, $token3, $token);

        //$token="285183274843495|fda8f3123eababe95aa45e20e5d51d5f";


        foreach ($post as $index) {
            $post_id = $index->post_id;
            $page_id = $index->page_id;
            $parametros = '/insights?metric=post_reactions_by_type_total';
            $reacciones = $fb->get('/' . $post_id .'/insights?metric=post_reactions_by_type_total&access_token=' .  $token);
            try {
                $reacciones = $reacciones->getGraphEdge();
                $next = $fb->next($reacciones);
                $reaccionesArray = $reacciones->asArray();
                if(empty($reaccionesArray)){
                    $response->estado='sin reacciones';
                    continue;}
                json_encode($reaccionesArray);
                $response->estado = True;
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $response->estado = 'Graph returned an error: ' . $e->getMessage();
                return $response;

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                $response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
                $postEliminado = Post::where('post_id', '=', $post_id)->first();
                $postEliminado->update(['status' => 'Eliminado']);
                return $response;
            }

            if ($response->estado == True) {
                $postReactionSQL = Reaction::where('post_id', '=', $post_id)->first();
                $ClasificacionReaction = json_encode($reaccionesArray[0]['values'][0]['value']);
                if ($postReactionSQL == null) {
                    $reaccion = Reaction::create(['post_id' => $post_id, 'page_id' => $page_id, 'reacciones' => $ClasificacionReaction]);

                } else {
                    $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['reacciones' => $ClasificacionReaction]);
                }
                $response->reacciones=$reaccion;
            }
        }
        return Response()->json($response);
    }


}
