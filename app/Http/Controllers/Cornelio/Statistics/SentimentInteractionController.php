<?php

namespace App\Http\Controllers\Cornelio\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Sentiment;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentimentInteractionController extends Controller
{
    public function SelectTopics(){
        $companies= session('company_id');
        $topics = Subcategory::where('company_id', $companies)->orderBy('name')->pluck('name', 'id') ;
        return view('Cornelio.Statistics.SentimentInteraction',compact('topics'));
    }

    public function getTopics(Request $request){
        try {
            $start_time = Carbon::parse($request->start);
            $end_time = Carbon::parse($request->end);

            $name = Subcategory::where('id', $request->topics)->pluck('name')->first();

            $posts = Classification_Category::where('subcategoria_id', $request->topics)
                //->whereBetween('created_at',[$start_time , $end_time])
                ->with('sentimentPost')
                ->get();
            //dd($posts, [$start_time , $end_time]);
            $data = [];

            $interation_post = 0;
            $positivo_post = 0;
            $negativo_post = 0;
            $neutral_post = 0;
            $mixto_post = 0;

            $i = 0;
            $j = 0;

            foreach ($posts as $post) {
                if (isset($post->sentimentPost)) {
                    if($post->sentimentPost->sentiment == 'Positivo'){
                        $positivo_post++;
                    }
                    elseif ($post->sentimentPost->sentiment == 'Negativo'){
                        $negativo_post++;
                    }
                    elseif ($post->sentimentPost->sentiment == 'Neutral'){
                        $neutral_post++;
                    }
                    elseif ($post->sentimentPost->sentiment == 'Mixto'){
                        $mixto_post++;
                    }
                    $interation_post++;
                }
            }
            $data['Post'][$i]['negativo']=$interation_post ? round((($negativo_post / $interation_post) * 100),2) : $interation_post;
            $data['Post'][$i]['neutral']=$interation_post ? round((($neutral_post / $interation_post) * 100),2) : $interation_post;
            $data['Post'][$i]['positivo']=$interation_post ? round((($positivo_post / $interation_post) * 100),2) : $interation_post;
            $data['Post'][$i]['mixto']=$interation_post ? round((($mixto_post / $interation_post) * 100),2) : $interation_post;
            $i++;


            $comments = Comment::where('subcategoria_id', $request->topics)
                //->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('classification_category', 'classification_category.post_id', 'comments.post_id')
                ->with('sentiment')
                ->select('comment_id')
                ->distinct('classification_category.post_id')
                ->get();

            $interation = 0;
            $positivo = 0;
            $negativo = 0;
            $neutral = 0;
            $mixto = 0;
            foreach ($comments as $comment){
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
                    elseif ($comment['sentiment']->sentiment == 'Mixto'){
                        $mixto++;
                    }
                    $interation++;
                }
            }
            $data['Comment'][$j]['negativo']=$interation ? round((($negativo / $interation) * 100),2) : $interation;
            $data['Comment'][$j]['neutral']=$interation ? round((($neutral / $interation) * 100),2) : $interation;
            $data['Comment'][$j]['positivo']=$interation ? round((($positivo / $interation) * 100),2) : $interation;
            $data['Comment'][$j]['mixto']=$interation ? round((($mixto / $interation) * 100),2) : $interation;
            $data['tema'] = $name;
            $j++;

            return $data;
        }catch (\Exception $e){
            return 500;
        }
    }

}
