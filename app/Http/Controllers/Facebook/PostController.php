<?php

namespace App\Http\Controllers\Facebook;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Facebook;

class PostController extends Controller
{
    public function index()
    {
        return view('Facebook.Post.index');
    }
    public function create( Request $request)
    {
        $idpagina=$request->Input('page');
        $token=$request->token_page;
        $mensaje=$request->Input('contenido');
        $app= env('APP_FB_ID_2');
        $key= env('APP_FB_SECRET_2');
        $version=config('services.facebook.version');
        $fb = new Facebook\Facebook([
            'app_id' =>$app,
            'app_secret' => $key,
            'default_graph_version' => $version,
            'fileUpload' => true,
        ]);
        if($request->file('files')){
            $file = $request->file('files');
            $path = $request->file('files')->storeAs('',$file->getClientOriginalName(),'imagen');

            $data = [
                'message' => "$mensaje",
                'source' =>$fb->fileToUpload($file),
            ];
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->post('/'.$idpagina.'/photos', $data, $token);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        }else{
            $data = [
                'message' => "$mensaje",
            ];
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->post('/'.$idpagina.'/feed', $data, $token);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        }
        $graphNode = $response->getGraphNode();
        $id=$graphNode['id'];

        return back()->with(['info'=>"Se ha publicado correctamente"]);

    }

    public  function GetPostDB(Request $request){
        $comment = Post::where('posts.page_id',$request->page_id)
                        ->join('attachments','attachments.post_id','=','posts.post_id')
                        ->get();
        $consulta = array(
            'posts'=> $comment
        );
        return $consulta;
    }
}