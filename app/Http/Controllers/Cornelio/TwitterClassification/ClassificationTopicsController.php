<?php

namespace App\Http\Controllers\Cornelio\TwitterClassification;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Page;
use App\Models\Subcategory;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TweetAttachmet;
use App\Models\Twitter\TweetComment;
use App\Models\Twitter\TweetReaction;
use App\Models\Twitter\Twitter_info;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterScrap;
use App\Models\Twitter\TwitterSentiment;
use App\Traits\ScrapTweetTrait;
use App\Traits\TopicCountTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassificationTopicsController extends Controller
{
    use ScrapTweetTrait;

    public function indexTopics()
    {
        $companies = session('company_id');
        $topics    = Subcategory::where('company_id', $companies)->orderBy('name')->pluck('name', 'id') ;
        return view('Twitter.ClassificationTopics.selectTopics', compact('topics'));
    }

    public function getTopics(Request $request)
    {
        $topics_id  = base64_decode($request->topics_id);
        $start_time = base64_decode($request->inicio);
        $end_time   = base64_decode($request->final);
        $topics     = Subcategory::find($topics_id);

        $tweets = TwitterClassification::where('subcategoria_id',$topics_id)
            ->whereBetween('created_at',[$start_time , $end_time])
            ->with(['tweet'=>function($q){
                $q->orderBy('created_time', 'desc');
            },'attachment','comments','reactions', 'page', 'topics'])
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return view('Twitter.ClassificationTopics.getTopics', compact('topics', 'tweets'));
    }

    public function updateReaction(Request $request)
    {
        $this->get_reaction($request->id_tweet);
        $reaction = TweetReaction::where('id_tweet', $request->id_tweet)->first();
        return $reaction;
    }

    public function updateComment(Request $request)
    {
        $tweet = Tweet::where('id_tweet', $request->id_tweet)->first();
        $this->get_Comments($tweet, $request->limit);
        $comment = TweetComment::where('id_tweet', $request->id_tweet)->get();
        return $comment;
    }

    public function getSentimentTweet(Request $request){
        $comment   = TweetComment::where('id_tweet', $request->id_tweet)->count();
        $sentiment = TweetComment::where('id_tweet', $request->id_tweet)
                                ->join('twitter_sentiments', 'twitter_sentiments.comment_id', 'tweets_comments.comment_id')
                                ->get();
        return response()->json(['count' => $comment, 'feeling' => $sentiment]);
    }

    public function tweetComment(Request $request)
    {
        $comentarios = TweetComment::Where('id_tweet', $request->id_tweet)->distinct('comment_id')->get();
        return $comentarios;
    }

    public function getInformation(Request $request)
    {
        // se inicializan variables
        $data                 = [];
        $comparar             = [];
        $compararComments     = [];
        $paginaComment        = [];
        $followers_count      = 0;
        $talkfriends_counting = 0;
        $reacciones           = 0;
        $comentarios          = 0;

        // se obtienen las paginas de la subcategria
        $paginas = TwitterClassification::where('subcategoria_id',$request->topics)->whereBetween('twitter_classifications.created_at',[$request->start, $request->end])->select('page_id')->groupBy('page_id')->get()->pluck('page_id');
        // por cada pagina se hace un ciclo
        foreach ($paginas as $pagina){
            $tem              = [];

            $info = Twitter_info::where('id_page', $pagina)->first();
            //obtener informacion de cada una de las paginas
            $followers_count      += $info->followers_count;
            $talkfriends_counting += $info->talkfriends_counting;

            // numero de publicaciones clasificados en la subcategoria
            $postCount = TwitterClassification::where('page_id', $pagina)->where('subcategoria_id', $request->topics)->whereBetween('created_at',[$request->start, $request->end])->count();

            $tem['page_id']   = $pagina;
            $tem['postCount'] = $postCount;
            // se agrega el array $tem a el array $comparar
            array_push($comparar,$tem);

            // publicaciones de la pagina clasificadas en la subcategoria
            $tweets = TwitterClassification::where('page_id', $pagina)->where('subcategoria_id', $request->topics)->whereBetween('created_at',[$request->start, $request->end])->get();

            // para cada publicacion de la pagina
            foreach ($tweets as $tweet){
                $temComments = [];
                //comentarios de la publicacion
                $comments = TweetComment::where('id_tweet', $tweet->id_tweet)->count();
                $temComments['comments']=$comments;

                //inclusion del array $temComments a el array $compararComments
                array_push($compararComments,$temComments);

            }
            // se agrega a array $paginaComment en el index del [$page_id] de la pagina
            // la variable del numero de comentarios de cada uno de las publicaciones de la pagina
            $paginaComment[$pagina]=$compararComments;
            // para cada index de $paginaComment
            foreach ($paginaComment as $paginaPC){
                $countPC=0;

                // entra a cada count de comentarios de la pagina
                foreach ($paginaPC as $comentarioPC) {
                    if(isset($comentarioPC['comments'])){
                        $contador=$comentarioPC['comments'];
                        $countPC=$countPC+$contador;
                    }
                    $paginaComment[$pagina]['count']=$countPC;
                }
            }
        }

        $variable      = 0;
        $variableMenor = PHP_INT_MAX;
        $temPag        = [];
        $temPagMenor   = [];

        foreach ($paginaComment as $indexPagina=>$key){
            if($paginaComment[$indexPagina]['count'] > $variable){
                $variable = $paginaComment[$indexPagina]['count'];
                $temPag['count'] = $paginaComment[$indexPagina]['count'];
                $temPag['page']  = $indexPagina;
            }
            if($paginaComment[$indexPagina]['count'] < $variableMenor){
                $variableMenor = $paginaComment[$indexPagina]['count'];
                $temPagMenor['count'] = $paginaComment[$indexPagina]['count'];
                $temPagMenor['page']  = $indexPagina;
            }
        }
        $data['mayorComment']['comments'] = ($temPag != []) ? $temPag['count'] : 0;
        $idParaname= ($temPag != []) ? $temPag['page'] : '' ;

        $nameMayorComment = TwitterScrap::where('page_id', $idParaname)->first();
        $data['mayorComment']['name']     = ($nameMayorComment) ? $nameMayorComment['name'] : '' ;
        $data['menorComment']['comments'] = ($nameMayorComment) ? $temPagMenor['count'] : 0 ;

        $nameMenorComment = TwitterScrap::where('page_id', $temPagMenor['page'])->first();
        $data['menorComment']['name'] = $nameMenorComment['name'];
        $data['mayorPost']            = array_reduce($comparar, function ($a, $b) {
            return @$a['postCount'] > $b['postCount'] ? $a : $b;
        });

        $nameMayorPost = TwitterScrap::where('page_id', $data['mayorPost'])->first();
        $data['mayorPost']['name'] = $nameMayorPost['name'];

        $min = PHP_INT_MAX;
        $idx = null;

        foreach ($comparar as $key => $value) {
            if($min > $comparar[$key]['postCount'])
            {
                $min = $comparar[$key]['postCount'];
                $idx = $key;
            }
        }
        $data['menorPost'] = $comparar[$idx];
        $post =TwitterClassification::where('subcategoria_id',$request->topics)
                                    ->join('tweets','tweets.id_tweet', 'twitter_classifications.id_tweet')
                                    ->select('tweets.*')
                                    ->get();

        foreach ($post as $posteo){
            $comments     = TweetComment::where('id_tweet', $posteo['id_tweet'])->count();
            $comentarios += $comments;
            $reactions    = TweetReaction::where('id_tweet', $posteo['id_tweet'])->first();
            $reacciones   = ($reactions) ? $reacciones += $reactions['favorite_count'] + $reactions['retweet_count'] : 0;
        }

        $publicaciones = TwitterClassification::where('subcategoria_id', $request->topics)
                                                ->join('tweets','tweets.id_tweet', 'twitter_classifications.id_tweet')
                                                ->select('tweets.*')
                                                ->count();

        $paginasCount  = TwitterClassification::where('subcategoria_id', $request->topics)->whereBetween('created_at',[$request->start, $request->end])->groupBy('page_id')->get();
        $sub           = Subcategory::where('id', $request->topics)->first();

        $data['talking']       = $talkfriends_counting;
        $data['followers']     = $followers_count;
        $data['comentarios']   = $comentarios;
        $data['reacciones']    = $reacciones;
        $data['publicaciones'] = $publicaciones;
        $data['paginas']       = count($paginasCount);
        $data['topics_name']   = $sub['name'];
        return $data;

    }

    public function cloudInformation(Request $request){
        $posts = TwitterClassification::where('subcategoria_id', $request->topics)->whereBetween('created_at',[$request->start, $request->end])->get();
        $data=[];
        foreach($posts as $post){
            $comments = TweetComment::where('id_tweet', $post->id_tweet)->get();
            array_push($data,$comments);
        }
        return $data;
    }

    public function commentInformation(Request $request){
        $count = TwitterClassification::where('subcategoria_id', $request->topics)
            ->whereBetween('twitter_classifications.created_at',[$request->start, $request->end])
            ->join('tweets_comments','tweets_comments.id_tweet', 'twitter_classifications.id_tweet')
            ->count();

        $comment = TwitterClassification::where('subcategoria_id', $request->topics)
            ->whereBetween('twitter_classifications.created_at',[$request->start, $request->end])
            ->join('tweets_comments','tweets_comments.id_tweet', 'twitter_classifications.id_tweet')
            ->join('twitter_sentiments','twitter_sentiments.comment_id', 'tweets_comments.comment_id')
            ->select('tweets_comments.*','twitter_sentiments.*')
            ->get();

        return response()->json(['count' => $count, 'comment' => $comment]);
    }

    public function tweetClassification(Request $request){
        $tweets = TwitterClassification::where('subcategoria_id', $request->topics)
            ->whereBetween('twitter_classifications.created_at',[$request->start, $request->end])
            ->join('tweets','tweets.id_tweet', 'twitter_classifications.id_tweet')
            ->with(['comments', 'page'])
            ->get();
        return $tweets;
    }

}
