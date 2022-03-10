<?php

namespace App\Http\Controllers\Cornelio\AnalysisTop;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Top;
use App\Models\Reaction;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalysisTopController extends Controller
{

    function analysisLink($id){
        $idTopics=base64_decode($id);
        return view('Cornelio.AnalysisTop.NotificationAnalysis', compact('idTopics'));
    }

    function analysisCloud(Request $request){
        $start_time = Carbon::now()->subDays(1);
        $end_time = Carbon::now()->addDay(1);

        //El objeto data por medio de un array obtiene los datos que se desea almacenar
        $data=array();

        //Cuenta la cantidad de veces que realizo consulta
        $i = 0;
        $temas = Subcategory::where('id', $request->idTopics)->get();
        foreach($temas as $topic) {
            $post = Classification_Category::where('subcategoria_id',$request->idTopics)
                ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
                ->join('comments', 'comments.post_id', '=', 'posts.post_id')
                ->select('comments.comment')
                ->orderBy('posts.created_time', 'desc')
                ->get()
                ->toArray();
            $data['Tema'][$i]['comment']= $post;
            $data['Tema'][$i]['name']= $topic->name;
            $i++;
        }
        return $data;
    }

    function analysisFeeling(Request $request){
        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);

        //El objeto data por medio de un array obtiene los datos que se desea almacenar
        $data=array();
        //Cuenta la cantidad de veces que realizo consulta
        $i = 0;

        //Se llaman todos los temas que se encuentra registrado en la compania
        $temas = Subcategory::where('id', $request->idTopics)->get();

        //Se realiza un recorrido por cada uno de los temas que se ha encontrado en el objeto temas
        foreach($temas as $topic){
            //Se llama las publicaciones clasificadas cuando el tema sea igual al objeto topic
            $rating = Comment::where('subcategoria_id', $topic->id)
                ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('classification_category', 'classification_category.post_id', '=', 'comments.post_id')
                ->with('sentiment')
                ->select('comment_id')
                ->distinct('classification_category.post_id')
                ->get();

            $interation = 0;
            $positivo = 0;
            $negativo = 0;
            $neutral = 0;

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
            $new['positivo'] =  $interation ? round((($positivo / $interation) * 100),2) : $interation;
            $new['negativo'] = $interation ? round((($negativo / $interation) * 100),2) : $interation;;
            $new['neutral'] = $interation ? round((($neutral / $interation) * 100),2) : $interation;

            $data['Tema'][$i]['Tema']=$topic->name;
            $data['Tema'][$i]['interation']=$new;
            $data['Tema'][$i]['negativo']=$interation ? round((($negativo / $interation) * 100),2) : $interation;
            $data['Tema'][$i]['neutral']=$interation ? round((($neutral / $interation) * 100),2) : $interation;
            $data['Tema'][$i]['positivo']=$interation ? round((($positivo / $interation) * 100),2) : $interation;
            $i++;
        }
        return $data;
    }

    /******************************************************************************************************************/

    public function topicsComparator($sub,$start,$end){
        $sub=base64_decode($sub);
        $start=base64_decode($start);
        $end=base64_decode($end);
        $fechas=array('start'=>$start,'end'=>$end);
        $sub_info=Subcategory::where('id','=',$sub)->select('id','name')->first();
        $items=[];
        $i=0;
        $start_time = ($start != "") ? $start : Carbon::now()->subDays(7);
        $end_time = ($end != "") ? $end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);

        $posts=Classification_Category::where('classification_category.subcategoria_id','=',$sub)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title','subcategory.name')
            ->orderBy('posts.created_time', 'desc')
            ->distinct()
            ->get()
            ->toArray();
        $i=0;
        foreach ($posts as $post){
            $post_id=$post['post_id'];

            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?
                $total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:
                $total=0;

            $coments=Comment::where('post_id','=',$post_id)->count();
            $image="";
            $imagen="";
            $video="";
            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;

            if($reacciones){
                $posts[$i]['likes']=$reacciones['likes'];
                $posts[$i]['love']=$reacciones['love'];
                $posts[$i]['haha']=$reacciones['haha'];
                $posts[$i]['wow']=$reacciones['wow'];
                $posts[$i]['sad']=$reacciones['sad'];
                $posts[$i]['angry']=$reacciones['angry'];
                $posts[$i]['shared']=$reacciones['shared'];
            }

            $i++;
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
        }
        $data=array('posts'=>$posts,'sub'=>$sub_info,'fechas'=>$fechas);
        return view('Cornelio.AnalysisTop.topicsComparator',$data);
    }

    function topicsComparatorCloud(Request $request){
        $start_time = Carbon::now()->subHour(72);
        $end_time = Carbon::now()->addDay(1);

        //El objeto data por medio de un array obtiene los datos que se desea almacenar
        $data=array();

        //Cuenta la cantidad de veces que realizo consulta
        $i = 0;
        $temas = Subcategory::where('id', $request->idTopics)->get();
        foreach($temas as $topic) {
            $post = Classification_Category::where('subcategoria_id',$request->idTopics)
                ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
                ->join('comments', 'comments.post_id', '=', 'posts.post_id')
                ->select('comments.comment')
                ->orderBy('posts.created_time', 'desc')
                ->get()
                ->toArray();
            $data['Tema'][$i]['comment']= $post;
            $data['Tema'][$i]['name']= $topic->name;
            $i++;
        }
        return $data;
    }

    /******************************************************************************************************************/

    public function BublesContent($comp, $contents_encode){
        $idCompanie=base64_decode($comp);
        $idContents = base64_decode($contents_encode);
        return view('Cornelio.AnalysisTop.BublesContent', compact('idCompanie', 'idContents'));
    }

    public function wordBublesContent(Request $request){
        $company_id=$request->idCompanie;
        $contents_id = json_decode($request->idContents);
        $infoContent=array();
        $topContenido = Top::where('tops.company_id', $company_id)
            ->where('tops.created_at', Carbon::today()->format("Y-m-d"))
            ->whereIn('type', $contents_id)
            ->join('category', 'category.id', 'tops.type')
            ->get();
        $j=0;
        $response=[];
        $chartBubbles = [];
        foreach ($topContenido as $content) {
            $category = $content->name;
            $interaccionesC = \GuzzleHttp\json_decode($content->interaction);
            $k = 0;
            foreach ($interaccionesC as $interact) {
                foreach ($interact as $valor) {
                    foreach ($valor as $key => $value) {
                        $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                            "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",
                            "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https", '🇨🇷' );
                        $result_replace = str_ireplace($array_replace_words,"", $value->word);
                        if($result_replace != "" &&  strlen($result_replace)>=4){
                            $infoContent[$k]['word'] = $value->word;
                            $infoContent[$k]['count'] = $value->count * 400;
                            $k++;
                        }
                        else{
                            continue;
                        }
                    }
                }
            }
            $cont = array_unique(array_column($infoContent, 'word'));
            $infoContent = array_intersect_key($infoContent, $cont);
            $keysContent = array_column($infoContent,'count');
            array_multisort($keysContent, SORT_DESC, $infoContent);
            $response['category'][$j]['datos'] = array_slice($infoContent, 0, 10);
            $h = 0;
            foreach ($response['category'][$j]['datos'] as $value){
                $bubbles_data[$h] = ['name' => $value['word'], 'value' => $value['count']];
                $h++;
            }
            $chartBubbles[$j] = ['name' => $category, 'data' => $bubbles_data];
            $j++;
        }
        return $chartBubbles;
    }
}
