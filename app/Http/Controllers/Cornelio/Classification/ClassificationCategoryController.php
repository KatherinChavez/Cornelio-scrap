<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Info_page;
use App\Models\Megacategory;
use App\Models\NumberWhatsapp;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use App\Models\Sentiment;
use App\Models\Subcategory;
use App\Models\Word;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassificationCategoryController extends Controller
{
    public function indexCategory(){
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $categories = Category::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        return view('Cornelio.Classification.Category.indexCategory',compact('categories'));
    }


    public function PostCategory(Request $request)
    {
        $company = session('company');
        $compain = Company::where('slug', $company)->first();

        $start = Carbon::parse($request->inicio);
        $end = Carbon::parse($request->final);

        $pages=Scraps::select('page_id')->where('categoria_id',$request->id)->pluck('page_id');
        $posts=Post::whereBetween('created_at',[$start , $end])
            ->whereIn('posts.page_id',$pages)
            ->with(['attachment','classification_category' =>function($q) use($compain){
                $q->where('classification_category.company_id',$compain->id);
            }])
            ->orderBy('created_time','DESC')
            ->paginate();
        return view('Cornelio.Classification.InfoIndividualPage.PostCategoria',compact('posts'));
    }

    public function Category(Request $request){
        $categoria=Category::all();
        if($categoria!=null){
            return $categoria;
        }
    }

    public function CountSelectCategory(Request $request){
        $take=5;
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $post=Scraps::where('scraps.categoria_id','=',$request->categoria)
            ->whereBetween('posts.created_time',[$request->inicio , $request->final])
            ->join('posts','posts.page_id','=','scraps.page_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*', 'attachments.*')
            ->orderBy('posts.created_time', 'desc')
            ->distinct('posts.post_id')
            ->get()
            ->count();
        return $post;
    }

    public function SelectCategory(Request $request){
        $take=5;
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $post=Scraps::where('scraps.categoria_id','=',$request->categoria)
            ->whereBetween('posts.created_time',[$request->inicio , $request->final])
            ->join('posts','posts.page_id','=','scraps.page_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*', 'attachments.*')
            ->orderBy('posts.created_time', 'desc')
            ->distinct('posts.post_id')
//            ->skip($request->desde)
//            ->take($take)
            ->get();
        return $post;
    }

    public function subcategory(Request $request){
        //$sub=Subcategory::where('megacategory_id','=',$request->Megacategoria)->get();
        $company_id = session('company_id');
        $sub=Subcategory::where('company_id', $company_id)->get();
        return $sub;
    }

    public function SelectMegacategory(Request $request){

        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $categoria=Megacategory::where('company_id',$companies)->get();

        if ($categoria == null) {
            return $categoria;
        } else {
            return $categoria;
        }
    }

    public function selectSubcategory(Request $request){
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $categoria=Subcategory::where('subcategory.company_id',$companies)
            ->join('classification_category', 'classification_category.subcategoria_id', '=', 'subcategory.id')
            ->select('subcategory.*')
            ->distinct('subcategory.id', 'subcategory.name')
            ->get();
        if ($categoria == null) {
            return $categoria;
        } else {
            return $categoria;
        }
    }

    public function classification(Request $request){
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $clasificacion=Classification_Category::where('post_id','=',$request->post_id)
            //->where('user_id','=',$request->user_id)
            ->first();
        if($clasificacion==null){
            Classification_Category::create(['page_id'=>$request->page_id,
                                            'post_id'=>$request->post_id,
//                                            'megacategoria_id'=>$request->megacategoria_id,
                                            'subcategoria_id'=>$request->subcategoria_id,
                                            'company_id'=>$companies ]);
            return "guardado";
        }
        else{
            Classification_Category::where('page_id','=',$request->page_id)
                                    ->where('post_id','=',$request->post_id)
                                    ->update(['megacategoria_id'=>$request->megacategoria_id,
                                              'subcategoria_id'=>$request->subcategoria_id,
                                              'company_id'=>$companies ]);
            return("Actualizado");
        }
    }

    public function sub_Category(Request $request){
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $sub=Subcategory::where('name','=',$request->sub)
            ->where('user_id','=',$request->user_id)->first();
       if($sub==null){
            $sub=Subcategory::create(['name'=>$request->sub,
                                     'detail'=>$request->detalle,
                                     'megacategory_id'=>$request->mega,
                                     'category_id'=>$request->categoria,
                                     'user_id'=>$request->user,
                                     'channel'=>$request->channel,
                                     'company_id'=> $companies]);
            return $sub;
        }
    }

    public function getClassification(Request $request){
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $clasificacion=Classification_Category::where('post_id','=',$request->post_id)
            //->where('user_id','=',$request->user_id)
            ->first();
        if($clasificacion==null){
            //Classification_Category::create($request->all());
            Classification_Category::create(['page_id'=>$request->page_id,
                'post_id'=>$request->post_id,
                'megacategoria_id'=>$request->megacategoria_id,
                'subcategoria_id'=>$request->subcategoria_id,
                'company_id'=>$companies ]);
            return "guardado";
        }
        else{
            //Classification_Category::where('post_id','=',$request->post_id)->update(['megacategoria_id'=>$request->megacategoria_id, 'user_id'=>$request->user_id, 'subcategoria_id'=>$request->subcategoria_id]);
            Classification_Category::where('page_id','=',$request->page_id)
                ->where('post_id','=',$request->post_id)
                ->update(['megacategoria_id'=>$request->megacategoria_id,
                    'subcategoria_id'=>$request->subcategoria_id,
                    'company_id'=>$companies ]);
            return 'Actualizo';
        }
    }

    public function CategorySentiment(Request $request){
        $subcategoria=Subcategory::find($request->categoria);
        $posts=Classification_Category::where('subcategoria_id','=',$request->categoria)
            ->with(['post'=>function($q){
                $q->orderBy('created_time', 'desc');
            },'attachment','comments','reactions'])
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return view('Cornelio.Classification.Category.CategorySentiment',compact('posts','subcategoria'));
    }


    public function SelectCategorySentiment(Request $request){
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url')
            ->orderBy('posts.created_time', 'asc')
            ->distinct()
            ->get()
            ->count();
        return $post;

    }

    public function Posts(Request $request){
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url')
            ->orderBy('posts.created_time', 'desc')
            ->distinct()
            ->take(50)
            //->get();
            ->paginate();
        //dd($post);
        return $post;
    }

    public function classificationSentimentPost(Request $request){
        $tipo=$request->tipo;
        if($tipo==""){
            $tipo="Negativo";

        }
        // $palabras=Word::where('tipo','=',$tipo)
        //     ->where('user_id','=',$request->user_id)
        //     ->get();

        $palabras=Word::where('user_id','=',$request->user)
                        ->get();
        $longitud=count($palabras);
        $user=$request->user;
        $comments=Comment::where('post_id','=',$request->post_id)->get();
        foreach ($comments as $comment){
            for ($i=0;$i<$longitud;){
                $posicion_coincidencia = strpos($comment['comment'], $palabras[$i]['word']);
                if ($posicion_coincidencia === false) {
                    return "NO se ha encontrado la palabra deseada!!!!";
                } else {
                    $comment_id=$comment['comment_id'];
                    $sentiment=$palabras[$i]['tipo'];
                    $sentimiento=Sentiment::Where('comment_id','=',$comment_id)
                        ->Where('user_id','=',$request->user)
                        ->first();
                    $data = [
                        'comment_id' => $comment_id,
                        'sentiment' => $sentiment,
                        'user_id' => $user
                    ];
                    if($sentimiento === NULL){

                        $sentimiento=Sentiment::create($data);
                    }else{
                        $sentimiento->update(['sentiment'=>$sentiment]);

                    }
                }
                $i++;
            }
        }
        return "Clasificación de sentimientos completa";
    }

    public function SentimentPost(Request $request){
        $comentarios=Comment::Where('comments.post_id','=',$request->post_id)
        // ->orderBy('comments.created_time','asc')
            ->join('sentiments','sentiments.comment_id','=','comments.comment_id')
            ->Where('sentiments.user_id','=',$request->user_id)
            ->select('sentiments.*')
            ->distinct('comments.comment_id')
            ->get();
        return $comentarios;
    }

    public function countComment(Request $request){
        $comment=Comment::where('post_id','=',$request->post_id)
            ->count();
        return $comment;
    }

    public function comment(Request $request){
        $comentarios=Comment::Where('post_id','=',$request->postId)
        //->orderBy('created_time','asc')
            ->distinct('comment_id')
            ->get();
        return $comentarios;
    }

    public function WordComment(Request $request){
        $data=[];
        $comentarios=Comment::Where('post_id','=',$request->postId)
            ->distinct('comment_id')
            ->get();

        foreach ($comentarios as $cloud){
            $getComentario = $cloud['comment'];
            $arrayPalabra =array(" de ", "hola", " paso "," de ", " q ", " con ", " qué ",  " que ", "que ", " porque ", " lo ", " estas ", " del ", "¡", "!", " estos ", " la ", " este ",  "te ", " tu ",
                " Él ", " por ", "tienen", " como ", " cómo ", " y ", " un ", " una ", " más ", " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ",
                " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " es ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", "tener  ", "esta ",
                " ja", " je", " les "," buen", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ", " bien ", ", ", " tienen  ", "tiene",
                " cada ", " solo "," nada "," ellos "," ellas "," cada "," sobre "," bajo "," desde "," sin  "," sobre "," ante "," entre "," tras "," pro " , " vamos",
                " según "," segun "," hacia ", " cabe ", " tras ", " jaja ", " jeje ", " ja ", " el ", " el, ", " donde, ", " donde ", " ver ", "así ",  " así ", " Asi ",
                "buenos ", " dias " , "días",  "cuando", "saludos ", "saludos", "tener", " tener ", "varios", "¿","?" , "van", " van ", "algunas", "alguna", " han ",
                " tan ", " ya ", " y ", " no ", " si ", " sale ", " he ", " eran ", " fue ", " ve ", " nada ", " ah ", " muy ", " osea ", " sea ", " yo ", " hay ", " eran ",
                "Y ", "un ", " no ", "mi ", "q ", " nada ", " o ", "como ", " los ", " las ", " casi ", " dejan ", " unas ", " vos ", " tu ", " ni ", " a ","ya ", "....", "...");
            $reemplazar = str_ireplace($arrayPalabra," ", $getComentario);
            array_push($data,$reemplazar);
        }
        return $data;
    }

    public function cloudPost(Request $request){
        $nube=Comment::where('post_id','=',$request->post_id)->get();
        return $nube;
    }

    public function DeclassifyCategory(Request $request){
        $clasificacion=Classification_Category::where('post_id','=',$request->post_id)
            ->first();
        if($clasificacion == null){

        }else{
            //Classification::destroy($request->post_id);
            $clasificacion->delete();
            return 'eliminado';
        }
    }

    public function report(Request $request){
        // se inicializan variables
        $total=0;
        $data=[];
        $comparar=[];
        $compararComments=[];
        $paginaComment=[];
        $talking=0;
        $cr=0;
        $reacciones=0;
        $comentarios=0;

        // se obtienen las paginas de la subcategria
        $paginas=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->select('page_id')->distinct()->get();
        // por cada pagina se hace un ciclo
        foreach ($paginas as $pagina){
            $compararComments=[];
            $countPC=0;
            $tem=[];
            $page_id=$pagina['page_id'];
            //$fans=Fan::where('page_id','=',$page_id)->get();
            $info=Info_page::where('page_id','=',$page_id)->first();
            //obtener informacion de cada una de las paginas
            $talking+=$info['talking'];

            // numero de publicaciones clasificados en la subcategoria
            $postCount=Classification_Category::where('classification_category.page_id','=',$page_id)
                ->where('classification_category.subcategoria_id','=',$request->subcategoria_id)->count();
            if($postCount === null){
                $postCount=0;
            }
            $tem['page_id']=$page_id;
            $tem['postCount']=$postCount;
            // se agrega el array $tem a el array $comparar
            array_push($comparar,$tem);

            // publicaciones de la pagina clasificadas en la subcategoria
            $postPagina=Classification_Category::where('classification_category.page_id','=',$page_id)
                ->where('classification_category.subcategoria_id','=',$request->subcategoria_id)->get();

            // para cada publicacion de la pagina
            foreach ($postPagina as $post){
                $temComments=[];
                $comments='';
                //comentarios de la publicacion
                $comments=Comment::where('post_id','=',$post['post_id'])->count();
                //$temComments['page_id']=$page_id;
                //$temarray_push($compararComments,$temComments);Comments['post_id']=$post['post_id'];
                $temComments['comments']=$comments;
                //inclusion del array $temComments a el array $compararComments
                array_push($compararComments,$temComments);

            }
            // se agrega a array $paginaComment en el index del [$page_id] de la pagina
            // la variable del numero de comentarios de cada uno de las publicaciones de la pagina
            $paginaComment[$page_id]=$compararComments;
            // para cada index de $paginaComment
            foreach ($paginaComment as $paginaPC){
                $countPC=0;

                // entra a cada count de comentarios de la pagina
                foreach ($paginaPC as $comentarioPC) {
                    if(isset($comentarioPC['comments'])){
                        $contador=$comentarioPC['comments'];
                        $countPC=$countPC+$contador;
                    }
                    $paginaComment[$page_id]['count']=$countPC;
                }
            }


        }
        $variable=0;
        $variableMenor=PHP_INT_MAX;
        $temPag=[];
        $temPagMenor=[];

        foreach ($paginaComment as $indexPagina=>$key){
            if($paginaComment[$indexPagina]['count']>$variable){
                $variable=$paginaComment[$indexPagina]['count'];
                $temPag['count']=$paginaComment[$indexPagina]['count'];
                $temPag['page']=$indexPagina;
            }
            if($paginaComment[$indexPagina]['count']< $variableMenor){
                $variableMenor=$paginaComment[$indexPagina]['count'];
                $temPagMenor['count']=$paginaComment[$indexPagina]['count'];
                $temPagMenor['page']=$indexPagina;
            }
        }
        $data['mayorComment']['comments']=$temPag['count'];
        $idParaname=$temPag['page'];
        $nameMayorComment=Page::where('page_id','=',''.$idParaname.'')->first();
        $data['mayorComment']['name']=$nameMayorComment['page_name'];
        $data['menorComment']['comments']= $temPagMenor['count'];
        $nameMenorComment=Page::where('page_id','=',''.$temPagMenor['page'].'')->first();
        $data['menorComment']['name']=$nameMenorComment['page_name'];
        $data['mayorPost'] = array_reduce($comparar, function ($a, $b) {
            return @$a['postCount'] > $b['postCount'] ? $a : $b;
        });
        $nameMayorPost=Page::where('page_id','=',$data['mayorPost'])->first();
        $data['mayorPost']['name']=$nameMayorPost['page_name'];
        $min = PHP_INT_MAX;
        $idx = null;
        foreach ($comparar as $key => $value) {
            if($min > $comparar[$key]['postCount'])
            {
                $min = $comparar[$key]['postCount'];
                $idx = $key;
            }
        }
        $data['menorPost']= $comparar[$idx];
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->select('posts.*')
            ->get();
        foreach ($post as $posteo){
            $comments=Comment::where('post_id','=',$posteo['post_id'])->count();
            if($comments === null){
                $comments=0;
            }
            $comentarios+=$comments;
            $reactions=Reaction::where('post_id','=',$posteo['post_id'])->first();
            $reacciones?
                $reacciones+=$reactions['wow']+$reactions['love']+$reactions['likes']+$reactions['sad']+$reactions['angry']+$reactions['haha']+$reactions['shared']:
                $reacciones+=0;
        }
        $publiacaciones=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->select('posts.*')
            ->count();
        if($publiacaciones === null){
            $publiacaciones=0;
        }
        $paginasCount=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->select('page_id')->distinct()->get();
        $paginasCount=$paginasCount->count();
        if($paginasCount === null){
            $paginasCount=0;
        }
        $sub=Subcategory::where('id','=',$request->subcategoria_id)->first();

        $data['talking']=$talking;
        $data['CR']=$cr;
        $data['fans']=$total;
        $data['comentarios']=$comentarios;
        $data['reacciones']=$reacciones;
        $data['publicaciones']=$publiacaciones;
        $data['paginas']=$paginasCount;
        $data['sub']=$sub['name'];
        return $data;

    }

    public function cloudReport(Request $request){
        $posts=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->get();
        $data=[];

        foreach($posts as $post){
            $post_id=$post['post_id'];
            $comments=Comment::where('post_id','=',$post_id)->get();
            array_push($data,$comments);
        }
        return $data;
    }

    public function cloudReport_nuevo(Request $request){
        $posts=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->get();
        $data=[];

        foreach($posts as $post){
            $post_id=$post['post_id'];
            $comments=Comment::Where('post_id','=',$post_id)
                ->distinct('comment_id')
                ->get();

            foreach ($comments as $cloud){
                $getComentario = $cloud['comment'];
                $arrayPalabra =array(" de ", "hola", " paso "," de ", " q ", " con ", " qué ",  " que ", "que ", " porque ", " lo ", " estas ", " del ", "¡", "!", " estos ", " la ", " este ",  "te ", " tu ",
                    " Él ", " por ", "tienen", " como ", " cómo ", " y ", " un ", " una ", " más ", " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ",
                    " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " es ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", "tener  ", "esta ",
                    " ja", " je", " les "," buen", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ", " bien ", ", ", " tienen  ", "tiene",
                    " cada ", " solo "," nada "," deja "," ellos "," ellas "," cada "," sobre "," bajo "," desde "," sin  "," sobre "," ante "," entre "," tras "," pro " , " vamos",
                    " según "," segun "," hacia ", " cabe ", " tras ", " jaja ", " jeje ", " ja ", " el ", " el, ", " donde, ", " donde ", " ver ", "así ",  " así ", " Asi ",
                    "buenos ", " dias " , "días",  "cuando", " saludos ", "saludos", "tener", " tener ", "varios", "¿","?" , "van", " van ", "algunas", "alguna", " han ",
                    " tan ", " ya ", " y ", " no ", " si ", " sale ", " he ", " eran ", " fue ", " ve ", " nada ", " ah ", " muy ", " osea ", " sea ", " yo ", " hay ", " eran ",
                    "Y ", "un ", " no ", "mi ", "q ", " nada ", " o ", "como ", " los ", " las ", " casi ", " dejan ", " unas ", " vos ", " tu ", " ni ", " a ","ya ", "....", "...");
                $reemplazar = str_ireplace($arrayPalabra," ", $getComentario);
                array_push($data,$reemplazar);
            }
        }
        return $data;
    }

    public function reactionCategoryCount(Request $request){
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('comments','comments.post_id','=','classification_category.post_id')
            ->select('comments.*')
            ->distinct()
            ->count();
        return $post;
    }

    public function reactionCategory(Request $request){
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('comments','comments.post_id','=','classification_category.post_id')
            ->join('sentiments','sentiments.comment_id','=','comments.comment_id')
            ->select('comments.*','sentiments.*')
            ->get();
        return $post;
    }

    public function postPage(Request $request){
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->select('posts.*')
            ->get();
        return $post;
    }

    public function TelegramCategory(Request $request){
        $category=Category::where('id','=',$request->categoria)->pluck('id');
        $sub = Subcategory::where('id', $request->sub)->get();
        // $channel=$sub['channel'];
        foreach($sub as $sendT){
            $channel=$sendT["channel"];
            $find = stripos($channel, "-100");
            if($find === false){
                //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                $canal="-100";
                $chat_id =$canal.$channel;
            }else{
                $chat_id =$channel;
            }
            $tema=$sendT['name'];
            $token = env('TELEGRAM_TOKEN');
            $url   = "https://api.telegram.org/bot$token/sendMessage";


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


    }

    public function SendCategory(Request $request){
        $category=Category::where('id','=',$request->categoria)->pluck('id');
        $sub = Subcategory::where('id', $request->sub)->get();
        foreach($sub as $sendW){
            $sub_name=$sendW['name'];
            $numeros=NumberWhatsapp::where('subcategory_id','=',$sendW['id'])->get();
            $contactos=array();
            $contactos=[];
            foreach ($numeros as $numero){
                $phone=$numero['numeroTelefono'];
                array_push($contactos, $phone);
            }
            foreach ($contactos as $contacto){
                $message = "¡Hola! Tengo la siguiente alerta relacionada con: ".$sub_name." la encontré en ".$request->pagina.
                    ", acá te dejo el link para que la veas: https://www.facebook.com/".$request->post_id;

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
                return $result = file_get_contents($url, false, $options);
            }
        }


        // $destination =$request->phone;

        // if($request->todos){

        //     $contactos=[];
        //     foreach ($numeros as $numero){
        //         $phone=$numero['phone'];
        //         array_push($contactos, $phone);
        //     }
        // }else{
        //     $contactos=array($destination);
        // }
        // foreach ($contactos as $contacto){
        //     $message = "¡Hola! Tengo la siguiente alerta relacionada con: ".$sub_name." la encontré en ".$request->pagina.
        //         ", acá te dejo el link para que la veas: https://www.facebook.com/".$request->post_id;

        //     $data = [
        //         'phone' => $contacto,
        //         'body' => $message,
        //     ];
        //     $json = json_encode($data); // Encode data to JSON
        //     $url = env('WHA_API_URL').env('WHA_API_TOKEN');
        //     $options = stream_context_create(['http' => [
        //         'method'  => 'POST',
        //         'header'  => 'Content-type: application/json',
        //         'content' => $json
        //         ]
        //     ]);
        //     // Send a request
        //     return $result = file_get_contents($url, false, $options);
        // }
    }
}
