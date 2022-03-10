<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facebook;
use App\Traits\ScrapTrait;

class LastCategoryController extends Controller
{
    use ScrapTrait;
    public function scrapCategory(){

        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $categories = Category::join('scraps','scraps.categoria_id', 'category.id')
                                ->where('category.company_id', $companies)
                                ->pluck('category.name', 'category.id');

        return view('Cornelio.Scraps.LastCategory.scrapCategory', compact( 'categories'));
    }

    public function updateReaction(Request $request){
        $paginas = Scraps::where('categoria_id', '=', $request->categoria_id)->select('page_id')->distinct()->get();
        $this->reactions($paginas);
        return "Éxito";
    }

    public function updateReactionJson(Request $request){
        $paginas = Scraps::where('categoria_id', '=', $request->categoria_id)->select('page_id')->get();
        $post = Post::where('page_id', $paginas)->get();
        $estado = "True";
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );

        $fb = new Facebook\Facebook($config);
        //$token = env('APP_FB_TOKEN_2');
        $token = $request->pageAccessToken;

        $estado = True;
        foreach ($paginas as $pag) {
            $post = Post::where('page_id', '=', $pag['page_id'])->orderby('created_time', 'DESC')->take(100)->get();


            foreach ($post as $index) {
                $post_id = $index->post_id;
                $page_id = $index->page_id;
                $estado = True;
                $parametros = '/insights?metric=post_reactions_by_type_total';
                $reacciones = $fb->get('/' . $post_id . '' . $parametros . '', $token);
                try {
                    $reacciones = $reacciones->getGraphEdge();
                    $next = $fb->next($reacciones);
                    $reaccionesArray = $reacciones->asArray();
                    json_encode($reaccionesArray);
                    $estado = True;
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    //return 'Graph returned an error: ' . $e->getMessage();
                    $estado = False;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    //return 'Facebook SDK returned an error: ' . $e->getMessage();
                    //exit;
                    $postEliminado = Post::where('post_id', '=', $post_id)->first();
                    $postEliminado->update(['status' => 'Eliminado']);
                    $estado = False;
                }

                if ($estado == True) {
                    $postReactionSQL = Reaction::where('post_id', '=', $post_id)->first();
                    $ClasificacionReaction = json_encode($reaccionesArray[0]['values'][0]['value']);
                    if ($postReactionSQL == null) {
                        $reaccion = Reaction::create(['post_id' => $post_id, 'page_id' => $page_id, 'reacciones' => $ClasificacionReaction]);
                    } else {
                        $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['reacciones' => $ClasificacionReaction]);
                    }
                }
            }
        }
        return Response()->json($estado);
        // return 'Actualizo';
    }

    public function pageCategory(Request $request){
        $paginas = Scraps::where('categoria_id', '=', $request->categoria_id)
            ->distinct()
            ->select('page_id', 'page_name')
            ->get();
        $this->PostFB($paginas);
        $this->CommentsFB($paginas);

        return $paginas;

    }

    public function postCategory(Request $request){
        $posteos = $request->response;
        $page_id = $request->pagina_id;
        $page_name = $request->name;
        $publications = $posteos['data'];
        foreach ($publications as $post) {
            if (array_key_exists('id', $post)) {
                $post_id = $post['id'];
            } else {
            }

            if (array_key_exists('source', $post)) {
                $source = $post['source'];
            } else {
                $source = null;
            }

            if (array_key_exists('message', $post)) {
                $content = $post['message'];
            } else if (array_key_exists('story', $post)) {
                $content = $post['story'];
            } else {
                $content = '';
            }
            if (array_key_exists('full_picture', $post)) {
                $full_picture = $post['full_picture'];
            } else {
                $full_picture = null;
            }

            $time = $post['created_time'];
            $parte1 = substr($time, 0, 10);
            $parte2 = substr($time, 11, 8);
            $time = $parte1 . " " . $parte2;
            $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $time);
            $fecha = $fecha->subHours(6);

            if (array_key_exists('attachments', $post)) {
                $type = $post['attachments']['data'][0]['type'];

                if (array_key_exists('title', $post['attachments']['data'][0])) {
                    $title = $post['attachments']['data'][0]['title'];
                    if (array_key_exists('url', $post['attachments']['data'][0])) {
                        $url = $post['attachments']['data'][0]['url'];
                    } else {
                        $url = null;
                    }

                } else {
                    if (array_key_exists('description', $post['attachments']['data'][0])) {
                        $title = $post['attachments']['data'][0]['description'];
                        $url = $post['attachments']['data'][0]['url'];
                    } else {
                        $title = null;
                        $url = null;
                    }
                }
            } else {
                $type = null;
                $url = null;
                $title = null;
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
                            'page_id' => $page_id,
                            'created_time' => $fecha]);

                        if ($postSQL == null) {
                            Post::create(['page_id' => $page_id,
                                'page_name' => $page_name,
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
                                'page_id' => $page_id,
                                'created_time' => $fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $page_id,
                                    'page_name' => $page_name,
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
                                'page_id' => $page_id,
                                'created_time' => $fecha]);

                            if ($postSQL == null) {
                                Post::create(['page_id' => $page_id,
                                    'page_name' => $page_name,
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
                            Post::create(['page_id' => $page_id,
                                'page_name' => $page_name,
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
                                Post::create(['page_id' => $page_id,
                                    'page_name' => $page_name,
                                    'post_id' => $post_id,
                                    'content' => $content,
                                    'created_time' => $fecha,
                                    'type' => 'Video']);
                            } else {
                                Post::where('post_id', '=', $post_id)->update(['type' => 'Video']);
                            }
                        } else {

                            if ($postSQL == null) {
                                Post::create(['page_id' => $page_id,
                                    'page_name' => $page_name,
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
        return $response['status'] = 200;
    }

    public function comments(Request $request){
        $comments = $request->data;
        $page_id = $request->post_id;

        if (array_key_exists('data', $comments)) {
            $comentarios = $comments['data'];
        } else {
            return "";
        }

        foreach ($comentarios as $comentario) {
            if (array_key_exists('id', $comentario)) {
                $post = $comentario['id'];
            }
            if (array_key_exists('comments', $comentario)) {
                $comentariosRespuesta = $comentario['comments']['data'];
                foreach ($comentariosRespuesta as $comentarioRespuesta) {
                    $comment_id = $comentarioRespuesta['id'];
                    if (array_key_exists('from', $comentarioRespuesta)) {
                        $author_id = $comentarioRespuesta['from']['id'];
                        $commented_from = $comentarioRespuesta['from']['name'];
                    } else {
                        $author_id = 'Sin';
                        $commented_from = 'Sin';
                    }

                    $comment = $comentarioRespuesta['message'];
                    $time = $comentarioRespuesta['created_time'];
                    $parte1 = substr($time, 0, 10);
                    $parte2 = substr($time, 11, 8);
                    $time = $parte1 . " " . $parte2;
                    $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $time);
                    $fecha = $fecha->subHours(6);
                    $comment_content = htmlspecialchars($comment);

                    if ($comment_content == null) {
                        $comment_content = 'Image/Emoji';
                    }

                    $comment = $comment_content;
                    $commentSQL = Comment::where('comment_id', '=', $comment_id)->first();
                    if ($commentSQL == null) {
                        Comment::create(['page_id' => $page_id, 'post_id' => $post, 'comment_id' => $comment_id, 'author_id' => $author_id,
                            'commented_from' => $commented_from, 'comment' => $comment_content, 'created_time' => $fecha]);
                    } else {
                        //$commentSQL->update($request->all());
                        //$commentSQL->update($request->except(['comment_id']));
                    }
                }
            }
        }
        return $status['status'] = 201;
    }
}
