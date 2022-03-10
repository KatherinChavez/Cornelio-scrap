<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Compare;
use App\Models\Page;
use App\Models\Post;
use App\Models\Sentiment;
use App\Models\TagsComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoClassifyController extends Controller
{
    public function Auto_Classification(){
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $page = Page::where('company_id', $companies)->pluck('page_name', 'id') ;
        return view('Cornelio.Classification.AutoClassify.Auto_Classification',compact('page'));
    }

    public function classificationSentiment(Request $request){
        $start_time = ($request->start_time != "") ? $request->start_time : Carbon::now()->subMonth(1);
        $end_time = ($request->end_time != "") ? $request->end_time : Carbon::now();
        $tipo=$request->tipo;
        $user=$request->user_id;
        $palabras=Compare::where('prioridad','=',$tipo)
            ->where('user_id','=',$request->user_id)
            ->get();
        foreach($palabras as $select){
            $SelectPalabra = $select['palabra'];
            $comments=Comment::where('comment',$SelectPalabra)
                            ->whereBetween('created_time',[$start_time , $end_time])
                            ->get();
            foreach($comments as $id){
                $comment_id = $id['comment_id'];
                if($tipo=="1"){
                    $SentimientoTipo="Negativo";
                }
                else if($tipo=="3"){
                    $SentimientoTipo="Positivo";
                }
            
                $sentimiento=Sentiment::Where('comment_id','=',$comment_id)
                                ->Where('user_id','=',$request->user_id)
                                ->first();
                $data = [
                    'comment_id' => $comment_id,
                    'sentiment' => $SentimientoTipo,
                    'user_id' => $user
                        ];
                if($sentimiento == null){
                    $sentimiento=Sentiment::create($data);
                }else{
                    $sentimiento->update(['sentiment'=>$SentimientoTipo]);
                }
            }
        }
    }

    public function classificationSentiment2(Request $request){
        $start_time = ($request->start_time != "") ? $request->start_time : Carbon::now()->subMonth(1);
        $end_time = ($request->end_time != "") ? $request->end_time : Carbon::now();
        $tipo=$request->tipo;
        
        $palabras=Compare::where('prioridad','=',$tipo)
            ->where('user_id','=',$request->user_id)
            ->get();

        $posteos=Post::whereBetween('created_time',[$start_time , $end_time])->get();
        $longitud=count($palabras);
        $user=$request->user_id;
        foreach ($posteos as $post){
            $comments=Comment::where('post_id','=',$post['post_id'])->get();
            foreach ($comments as $comment){
                for ($i=0;$i<$longitud;){
                    $posicion_coincidencia = strpos($comment['comment_id'], $palabras[$i]['palabra']);
                    if ($posicion_coincidencia == false) {
                        //return "NO se ha encontrado la palabra deseada!!!!";
                    } else {
                        $comment_id=$comment['comment_id'];

                        //Pregunta nuevamente cual la prioridad que encontro
                        if($tipo=="1"){
                            $SentimientoTipo="Negativo";
                        }
                        else if($tipo=="3"){
                            $SentimientoTipo="Positivo";
                        }
                        $sentimiento=Sentiment::Where('comment_id','=',$comment_id)
                            ->Where('user_id','=',$request->user_id)
                            ->first();
                        $data = [
                            'comment_id' => $comment_id,
                            'sentiment' => $SentimientoTipo,
                            'user_id' => $user
                             ];
                        if($sentimiento == null){
                            $sentimiento=Sentiment::create($data);
                            dd($sentimiento, 'crear');
                        }else{
                            $sentimiento->update(['sentiment'=>$SentimientoTipo]);
                            dd($sentimiento, 'actu');
                        }
                    }
                    $i++;
                }
            }
        }
        return "Clasificación de sentimientos completa";
    }
}
