<?php

namespace App\Http\Controllers\Cornelio\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Info_page;
use App\Models\Megacategory;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Helper\Helper;
use Facebook;

class MegacategoryReviewController extends Controller
{
    public function Megacategory(){
        $company = session('company');
        $user_id = Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        //$Megacategoria = Megacategory::where('company_id', $companies)->pluck('name', 'id');
        $Category = Category::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        return view('Cornelio.Report.Review.InfoMegacategory',compact('company', 'Category'));
    }

    public function ReportWordCloud(Request $request ){
        $data=[];

        $start_time = ($request->start != "") ? Carbon::createFromFormat('d/m/Y',$request->start) : Carbon::now()->subDays(2);
        $end_time = ($request->end != "") ? Carbon::createFromFormat('d/m/Y',$request->end)  : Carbon::now()->addDay(1);

        $subcategoria=$request->sub;
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
                                        ->whereBetween('classification_category.created_at',[$start_time,$end_time])
                                        ->join('posts','posts.post_id','=','classification_category.post_id')
                                        ->join('comments','comments.post_id','=','posts.post_id')
                                        ->select('comments.comment')
                                        ->get();
        foreach($post as $comments){
            $getComentario = $comments['comment'];
            $arrayPalabra =array(" de ", "hola", " paso "," de ", " q ", " con ", " qué ",  " que ", "hasta","que ", " porque ", " lo ", " estas ", " del ", "¡", "!", " estos ", " la ", " este ",  " te ", " tu ",
                " Él ", " por ", "tienen", " como ", " cómo ", " y ", " un ", " una ", " más ", " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ",
                " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " es ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", "tener", " esta ", " ahi ", " ahí ",
                " ja", " je", " les "," buen ", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ", " bien ", ", ", " tienen  ", "tiene",
                " cada ", " solo "," nada "," ellos "," ellas "," cada "," sobre "," bajo "," desde "," sobre "," ante "," entre "," tras "," pro " , " vamos", " sus ",
                " según "," segun "," hacia ", " cabe ", " tras ", " jaja ", " jeje ", " ja ", " el ", " el, ", " donde, ", " donde ", " ver ", "así ",  " así ", " Asi ", " le ",
                " buenos ", " buenas ",  " dias " , "días",  "cuando", "saludos ", " saludos",  "tener ", " tener ", " varios", "¿","?" , " van ", " algunas", " alguna", " han ", " al ",
                " tan ", " ya ", " y ", " no ", " si ", " sale ", " he ", " eran ", " fue ", " ve ", " nada ", " ah ", " muy ", " osea ", " sea ", " yo ", " hay ", " eran ", " tantos ",
                " Y ", "un ", " no ", " mi ", "q ", " nada ", " o ", "como ", " los ", " las "," su", " casi ", " dejan ", " unas ", " vos ", " tu ", " ni ", " a ","ya ", "....", "...",
                " estos ", " va ", " pueden ", " es ", " pasa ", " pueden ", " despues ", " después ", " estén ", " esté ", " este ", " nuestro ", " quien ", " más ", " nos ");
            $reemplazar = str_ireplace($arrayPalabra," ", $getComentario);
            array_push($data,$reemplazar);
        }
        return $data;
    }

    public function ReportImpactPost(Request $request)
    {

        $start_time = ($request->start != "") ? Carbon::createFromFormat('d/m/Y',$request->start) : Carbon::now()->subDays(2);
        $end_time = ($request->end != "") ? Carbon::createFromFormat('d/m/Y',$request->end)  : Carbon::now()->addDay(1);
        $posts = Classification_Category::where('classification_category.subcategoria_id', '=', $request->sub)
            //->whereBetween('classification_category.created_at', [$start_time, $end_time])
            ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
            ->get();
        $i = 0;

        foreach ($posts as $post) {

            $total = 0;
            $post_id = $post['post_id'];


            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?$totalLinea=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:$totalLinea=0;



            /*************************************** Comentarios *******************************************/
            $coments = Comment::where('post_id', '=', $post_id)->count();
            $image = "";
            $imagen = "";
            $video = "";
            $posts[$i]['comentarios'] = $coments;
            $posts[$i]['reacciones'] = $totalLinea;
            $i++;

        }

        $j=0;
        foreach ($posts as $post){
            $value = $post['page_name'];
            $reacciones = $post['comentarios'] + $post['reacciones'];
            if (isset($contar[$value])){
                $contar[$value]['interacciones'] += $reacciones;
                $contar[$value]['publicaciones'] += 1;
                $contar[$value]['nombre'] = $value;
            }else{
                $contar[$value]['interacciones'] = $reacciones;
                $contar[$value]['publicaciones'] = 1;
                $contar[$value]['nombre'] = $value;
            }
        }
        $ordenado=[];
        foreach ($contar as $item){
            $ordenado[$j]=$item;
            $j++;
        }

        return $ordenado;

    }

    public function commentsRandom(Request $request){
        $start_time = ($request->start != "") ? Carbon::createFromFormat('d/m/Y',$request->start) : Carbon::now()->subDays(2);
        $end_time = ($request->end != "") ? Carbon::createFromFormat('d/m/Y',$request->end)  : Carbon::now()->addDay(1);

        $i=0;
        if($request->subcategoria_id){
            $posts=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
                ->get();
        }
        if($request->post_id){
            $comments=Comment::where('post_id','=',$request->post_id)->inRandomOrder()->limit(10)->get();
            $items[$i]['comentarios']=$comments;
            $items[$i]['post']=$request->post_id;
            $i++;
            return $items;
        }
        $items=[];
        foreach ($posts as $post){
            $comments=Comment::where('post_id','=',$post['post_id'])->inRandomOrder()->limit(10)->get();
            $items[$i]['comentarios']=$comments;
            $items[$i]['post']=$post['post_id'];
            $i++;
        }
        return $items;

    }

    public function MecategoryToday($id){
        $id=base64_decode($id);

        //id es la categoria/contenido
        //$subcategorias=Subcategory::where('megacategory_id','=',$id)->get();
        $subcategorias=Subcategory::get();

        $start_time = Carbon::now()->subDays(1);
        $end_time = Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts=[];
        $adjunto="";
        $publicaciones="";
        foreach ($subcategorias as $subcategoriaData){

            $subcategoria=$subcategoriaData['id'];
            $categoria=Subcategory::where('id','=',$subcategoria)->select('name')->first();
            $sub=array('sub'=>$categoria['name'],'id'=>$subcategoria);

            $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->where('scraps.categoria_id', $id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->join('scraps','scraps.page_id','=','posts.page_id')
                ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
                ->join('attachments','attachments.post_id','=','posts.post_id')
                ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title','subcategory.name')
                ->orderBy('posts.created_time', 'desc')
                ->distinct()
                ->get()
                ->toArray();

            if(array_key_exists(0, $post)){
                $post[0]=array_merge($post[0],$sub);
            }
            $posts= array_merge($posts,$post);
        }
        $i=0;
        foreach ($posts as $post){
            $total=0;
            $post_id=$post['post_id'];

            $coments=Comment::where('post_id','=',$post_id)->count();

            $imagen="";
            $image="";
            $video="";

            if($post['picture']){
                $image=str_replace("AND", "&", $post['picture']);
                $adjunto=$imagen;
                if($post['url']){

                }
            }
            if($post['video']){
                $video=
                $image=str_replace("AND", "&", $post['picture']);
            }

            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?$total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:$total=0;


            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;
            $i++;

        }
        $data=array('posts'=>$posts,'subcategorias'=>$subcategorias,'megacategoria_id'=>$id);
        return view('Cornelio.Report.Review.MecategoryToday',$data);
    }

    public function ReportToday(Request $request){
        $interval_type = "Y-m";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(7);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = ($request->interval != "") ? $request->interval : "Diario";

        if( $interval == "Diario" ) {
            $interval_type = "Y-m-d";
        } elseif( $interval == "Hourly") {
            $interval_type = "Y-m-d H";
        }


        $chart=array();
        $subs=[];
        $subcategorias =Classification_Category::where('classification_category.megacategoria_id','=',$request->megacategoria_id)
            ->whereBetween('classification_category.created_at',[$start_time , $end_time])
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->select('classification_category.subcategoria_id as id')
            ->distinct()
            ->get();
        $items = null;
        $type="Reactions";
        $response = [];
        if( count($subcategorias) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];
        }
        $i=0;
        foreach ($subcategorias as $subcategoria){
            $subs['subcategoria']=$subcategoria['id'];

            $sub=Subcategory::where('id','=',$subcategoria['id'])
                ->get();
            $subcategoria_id=$subcategoria['id'];
            $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();
            $subcategoria_name=$sub[0]->name;
            if($type === 'Reactions'){
                $response['yAxis_label'] = "Interacciones";
            }else{
                $response['yAxis_label'] = "Comments";
            }

            $response['subcategoria'][$subcategoria_name]['chart_label'] = $subcategoria_name;

            $interval_step = "+1 day";
            $response['xAxis_label'] = "$interval";

            $data_set = [];
            $new=[];
            $date_range = Helper::date_range($start_time, $end_time, $interval_step, $interval_type);
            foreach ($date_range as $date) {
                if(isset($post[$date])) {
                    $total=0;
                    foreach ($post[$date] as $posteo) {
                        $post_id= $posteo['post_id'];
                        if($type == 'Reactions'){
                            //$reactions=Reaction::where('post_id','=',$post_id)->select('reacciones')->get();
                            $reactions=Reaction::where('post_id','=',$post_id)->first();
                            $totalLinea =0;
                            if(count($reactions)==0){
                                $comentarios=Comment::where('post_id','=',$post_id)->count();
                                $total=$total+$comentarios;
                            }else{

                                $like=$reactions['0']['likes'];
                                $wow=$reactions['0']['wow'];
                                $sad=$reactions['0']['sad'];
                                $haha=$reactions['0']['haha'];
                                $angry=$reactions['0']['angry'];
                                $love=$reactions['0']['love'];
                                $shared=$reactions['0']['shared'];
                                $comentarios=Comment::where('post_id','=',$post_id)->count();
                                $total=$total+$like+$wow+$sad+$haha+$angry+$love+$shared+$comentarios;
                                //$total=$total+$comentarios;
                            }
                        }
                    }
                    $data_set[$date]=$total;
                    array_push($new,$total);


                } else {
                    $data_set[$date] = 0;
                    array_push($new,0);
                }

            }

            $response['subcategoria'][$subcategoria_name]['data_set'] = $data_set;
            $chart['series'][$i]['data']=$new;
            $chart['series'][$i]['name']=$subcategoria_name;
            $i++;

        }
        $chart['fechas']=$date_range;
        $response['status'] = "success";


        return $chart;
        return $response;
    }

    public function ValidateMegategory($id){

        $user_id = Auth::id();
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDays(2);
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts=[];
        //id es el contenido
        $category = Category::where('id', $id)->first();
        $post=Classification_Category::where('classification_category.company_id','=',$category->company_id)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->with('post','attachment', 'subcategory')
                ->get()
                ->toArray();
        $posts= array_merge($posts,$post);
        $i=0;
        foreach ($posts as $post){
            $post_id=$post['post_id'];
            $coments=Comment::where('post_id','=',$post_id)->count();

            $imagen="";
            $image="";
            $video="";
            if($post['attachment']['picture']){
                $image=str_replace("AND", "&", $post['attachment']['picture']);
                $adjunto=$imagen;
                if($post['attachment']['url']){

                }
            }
            if($post['attachment']['video']){
                $video=
                $image=str_replace("AND", "&", $post['attachment']['picture']);
            }

            $posts[$i]['comentarios']=$coments;

            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?$total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:$total=0;

            $posts[$i]['reacciones']=$total;
            $i++;
        }
        $data=array('posts'=>$posts);
        return view('Cornelio.Report.Review.ValidateMegategory',$data);
    }

    public function AllUpdateScrap(Request $request){

        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $post = Post::where('page_id', '=', $request->page_id)->get();
        $page_id = $request->page_id;
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );

        /*----------------------------------------------- Reaction ------------------------------------------------------------*/
        $fb = new Facebook\Facebook($config);
        //$token = env('APP_FB_TOKEN_2');
        $token = $request->pageAccessToken;
        $estado = "True";
        foreach ($post as $index) {
            $post_id = $index->post_id;
            $page_id = $index->page_id;
            $estado = "True";
            $parametros = '/insights?metric=post_reactions_by_type_total';
            $reacciones = $fb->get('/' . $post_id . '' . $parametros . '', $token);
            try {
                $reacciones = $reacciones->getGraphEdge();
                $next = $fb->next($reacciones);
                $reaccionesArray = $reacciones->asArray();
                json_encode($reaccionesArray);
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                //return 'Graph returned an error: ' . $e->getMessage();

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $postEliminado = Post::where('post_id', '=', $post_id)->first();
                $postEliminado->update(['status' => 'Eliminado']);
                $estado = "False";
            }

            if ($estado != "False") {
                $postReactionSQL = Reaction::where('post_id', '=', $post_id)->first();
                $ClasificacionReaction = json_encode($reaccionesArray[0]['values'][0]['value']);

                if ($postReactionSQL == null) {
                    $reaccion = Reaction::create(['post_id' => $post_id, 'page_id' => $page_id, 'reacciones' => $ClasificacionReaction]);

                } else {
                    $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['reacciones' => $ClasificacionReaction]);
                }
            }
        }

        /*----------------------------------------------- Comment ------------------------------------------------------------*/
        $fb2 = new Facebook\Facebook($config);
        $post = Post::where('page_id', '=', $request->page_id)
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
        return 'Actualizo';
    }

    public function AllUpdate(Request $request){
        $user_id = Auth::id();
        $company_id = session('company_id');
        $start_time =  Carbon::now()->subDays(2);
        $end_time =  Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $arrayComparar=[];
        $k=0;
        //$posts=Classification_Category::where('classification_category.megacategoria_id','=',$request->id)
        $posts=Classification_Category::where('classification_category.company_id','=',$company_id)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->select('posts.*')
            ->orderBy('posts.created_time', 'desc')
            ->get();
        $config = array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION'),
        );

        /*----------------------------------------------- Reaction ------------------------------------------------------------*/
        $fb = new Facebook\Facebook($config);
        //$token = env('APP_FB_TOKEN_2');
        $token = $request->pageAccessToken;
        $estado = "True";
        foreach ($posts as $index) {
            $post_id = $index->post_id;
            $page_id = $index->page_id;
            $estado = "True";
            $parametros = '/insights?metric=post_reactions_by_type_total';

            $parametros='?fields=reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(SAD).limit(0).summary(1).as(sad), reactions.type(ANGRY).limit(0).summary(1).as(angry),shares';

            try {
                $reacciones=$fb->get('/'.$post_id.''.$parametros.'',$token);
                $estado = True;
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                //$estado = 'Graph returned an error: ' . $e->getMessage();
                //return $estado;
                $estado = False;

            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                //$response->estado= 'Facebook SDK returned an error: ' . $e->getMessage();
                $postEliminado = Post::where('post_id', '=', $post_id)->first();
                $postEliminado->update(['status' => 'Eliminado']);
                $estado = False;
            }

            if ($estado == True) {

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
                    $conversation = Reaction::create(['post_id'=>$post_id,'likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share,'page_id'=>$page_id]);
                } else {
                    //$reaccion->update(['post_id'=>$post_id,'likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share,'page_id'=>$page_id]);
                    $reaccion = Reaction::where('post_id', '=', $post_id)->where('page_id', $page_id)->update(['likes'=>$like,'haha'=>$haha,'wow'=>$wow,'love'=>$love,'angry'=>$angry,'sad'=>$sad,'shared'=>$share]);

                }
                $estado=$reaccion;
                //dd($request->all());

            }
        //}

        /*----------------------------------------------- Comment ------------------------------------------------------------*/
        $fb2 = new Facebook\Facebook($config);
//        $post = Post::where('page_id', '=', $request->page_id)
//            ->orderby('created_time', 'DESC')
//            ->take(100)
//            ->get();
//         dd($request);
       // foreach ($post as $index) {
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
                                Comment::create(['post_id' => $post_id, 'page_id' => $page_id, 'comment_id' => $comment_id, 'author_id' => $author_id,
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
                        Comment::create(['post_id' => $post_id, 'page_id' => $page_id, 'comment_id' => $comment_id, 'author_id' => $author_id,
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
                                    Comment::create(['post_id' => $post_id, 'page_id' => $page_id, 'comment_id' => $comment_id, 'author_id' => $author_id,
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
                            Comment::create(['post_id' => $post_id, 'page_id' => $page_id, 'comment_id' => $comment_id, 'author_id' => $author_id,
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

        return 'Actualizo';
    }

    public function Telegram(Request $request){
        $sub=Subcategory::where('id','=',$request->sub)->first();
        $channel=$sub['channel'];
        $tema=$sub['name'];
        $token = env('TELEGRAM_TOKEN');
        $url   = "https://api.telegram.org/bot$token/sendMessage";
        $canal="-100";
        $canal_agencia="1144464904";
        $grupo_agencia="275153292";

        $find = stripos($channel, "-100");
        if($find === false){
            //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
            $canal="-100";
            $chat_id =$canal.$channel;
        }else{
            $chat_id =$channel;
        }

        //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
        //$chat_id =$canal.$channel;
        //$chat_id =$canal.$channel;
        $data = array(
            "chat_id" => $chat_id,
            "text" => "Hola! tengo la siguiente alerta, relacionada con: ".$tema.", acá te dejo el link para que la veas: https://www.facebook.com/".$request->post_id
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
    }

    public function Reclassify(Request $request){
        $recla=Classification_Category::where('post_id','=',$request->post_id)->first();
        if($recla!=null){
            //$recla->update(['megacategoria_id'=>$request->megacategoria,'subcategoria_id'=>$request->subcategoria, 'company_id'=>$request->compania]);
            $recla->update(['subcategoria_id'=>$request->subcategoria, 'company_id'=>$request->compania]);
            return $recla;
        }
        else{
            $scrap = Scraps::where('page_name', $request->page)->first();
            $recla = Classification_Category::create(['page_id'=> $scrap->page_id, 'post_id'=>$request->post_id, 'subcategoria_id'=>$request->subcategoria, 'company_id'=>$request->compania]);
        }
        return $recla;
    }

    public function ToDisable(Request $request){
        $clasificacion=Classification_Category::where('post_id','=',$request->id)
            ->where('subcategoria_id','=',$request->sub)
            ->first();
        if($clasificacion == null){

        }else{
            return 'eliminado';
        }
    }

    public function Declassify(Request $request){
        $clasificacion=Classification_Category::where('post_id','=',$request->id)
            ->where('subcategoria_id','=',$request->sub)
            ->first();
        if($clasificacion == null){

        }else{
            $clasificacion->delete();
            //return back()->with('info', 'Eliminada correctamente');
            return 'eliminado';
        }
    }

    public function ReportDetail($post,$subC){
        $post_id=base64_decode($post);
        $sub_id=base64_decode($subC);
        $post=Post::where('posts.post_id','=',$post_id)
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title')
            ->orderBy('posts.created_time', 'desc')
            ->first();
        $page_id=$post['page_id'];
        $info=Info_page::where('page_id','=',$page_id)->first();
        $sub=Subcategory::where('id','=',$sub_id)->select('id','name')->first();
        $reacciones=Reaction::where('post_id','=',$post_id)->first();
        $comments=Comment::where('post_id','=',$post_id)->count();
        $reacciones?$total=$reacciones['likes'] + $reacciones['love'] + $reacciones['haha'] + $reacciones['wow'] + $reacciones['sad'] + $reacciones['angry'] + $reacciones['shared']+$comments:$total=0;

        $data=array('post'=>$post,'sub'=>$sub,'info'=>$info,'reacciones'=>$reacciones,'comentarios'=>$comments ,'interacciones'=>$total);
        return view('Cornelio.Report.Review.ReportDetail',$data);
    }

    public function CloudPost1(Request $request){
        $nube=Comment::where('post_id','=',$request->post_id)->get();
        return $nube;
    }

    public function CloudPost(Request $request){
        $data = [];

        $nube=Comment::where('post_id','=',$request->post_id)->get();
        foreach($nube as $coments){
            $getComentario = $coments['comment'];
            $arrayPalabra =array(" de ", "hola", " paso "," de ", " q ", " con ", " qué ",  " que ", "que ", " porque ", " lo ", " estas ", " del ", "¡", "!", " estos ", " la ", " este ",  " te ", " tu ",
                " Él ", " por ", "tienen", " como ", " cómo ", " y ", " un ", " una ", " más ", " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ",
                " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " es ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", "tener", " esta ", " ahi ", " ahí ",
                " ja", " je", " les "," buen ", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ", " bien ", ", ", " tienen  ", "tiene",
                " cada ", " solo "," nada "," ellos "," ellas "," cada "," sobre "," bajo "," desde "," sobre "," ante "," entre "," tras "," pro " , " vamos", " sus ",
                " según "," segun "," hacia ", " cabe ", " tras ", " jaja ", " jeje ", " ja ", " el ", " el, ", " donde, ", " donde ", " ver ", "así ",  " así ", " Asi ", " le ",
                " buenos ", " buenas ",  " dias " , "días",  "cuando", "saludos ", " saludos",  "tener ", " tener ", " varios", "¿","?" , " van ", " algunas", " alguna", " han ", " al ",
                " tan ", " ya ", " y ", " no ", " si ", " sale ", " he ", " eran ", " fue ", " ve ", " nada ", " ah ", " muy ", " osea ", " sea ", " yo ", " hay ", " eran ", " tantos ",
                " Y ", "un ", " no ", " mi ", "q ", " nada ", " o ", "como ", " los ", " las "," su", " casi ", " dejan ", " unas ", " vos ", " tu ", " ni ", " a ","ya ", "....", "...",
                " estos ", " va ", " pueden ", " es ", " pasa ", " pueden ", " despues ", " después ", " estén ", " esté ", " este ", " nuestro ", " quien ", " más ", " nos ");
            $reemplazar = str_ireplace($arrayPalabra," ", $getComentario);
            array_push($data,$reemplazar);
        }
        return $data;
    }

}
