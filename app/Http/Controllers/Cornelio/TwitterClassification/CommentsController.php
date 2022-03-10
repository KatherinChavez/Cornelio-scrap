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
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterScrap;
use App\Models\Twitter\TwitterSentiment;
use App\Traits\TopicCountTrait;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function indexComment()
    {
        $companies  = session('company_id');
        $page = TwitterScrap::where('company_id', $companies)->orderBy('name')->pluck('name', 'page_id') ;
        $categories = TwitterContent::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        return view('Twitter.Comment.selectPage', compact('page', 'categories'));
    }
    public function getComment(Request $request)
    {
        $page_id    = base64_decode($request->id);
        $start_time = base64_decode($request->inicio);
        $end_time   = base64_decode($request->final);

        $tweets = Tweet::where('author_id',$page_id)
                        ->whereBetween('created_time',[$start_time , $end_time])
                        ->with(['attachment', 'comments', 'reactions', 'page'])
                        ->orderBy('tweets.created_time', 'desc')
                        ->paginate();
        return view('Twitter.Comment.getPage',compact('tweets'));
    }

    public function getSentiment(Request $request){
        if($request->post_id){
            $comentarios= TweetComment::Where('comments.post_id','=',$request->post_id)
                ->with('sentiment')
                ->join('sentiments','sentiments.comment_id','=','comments.comment_id')
                ->select('sentiments.*')
                ->distinct('comments.comment_id')
                ->get();
        }
        elseif ($request->page_id){
            $comentarios=TweetComment::where('id_page', base64_decode($request->page_id))
                ->join('twitter_sentiments', 'twitter_sentiments.comment_id', 'tweets_comments.comment_id')
                ->select( 'twitter_sentiments.sentiment', 'twitter_sentiments.comment_id')
                ->get();
        }
        elseif ($request->topics_id){
            $comentarios=TweetComment::where('twitter_classifications.subcategoria_id', base64_decode($request->topics_id))
                ->join('tweets', 'tweets.id_tweet', 'tweets_comments.id_tweet')
                ->join('twitter_classifications', 'twitter_classifications.id_tweet', 'tweets.id_tweet')
                ->join('twitter_sentiments', 'twitter_sentiments.comment_id', 'tweets_comments.comment_id')
                ->select( 'twitter_sentiments.sentiment', 'twitter_sentiments.comment_id')
                ->get();
        }
        else{
            $comentarios=TweetComment::join('twitter_sentiments','twitter_sentiments.comment_id','tweets_comments.comment_id')
                ->select('twitter_sentiments.*')
                ->distinct('tweets_comments.comment_id')->get();
        }
        return $comentarios;
    }

    public function classificationSentiment(Request $request)
    {
        $sentimiento = TwitterSentiment::Where('comment_id', $request->comment)->first();
        if (!$sentimiento) {
            TwitterSentiment::create(['comment_id'=>$request->comment, 'sentiment'=>$request->sentiment, 'status'=>0]);

        } else {
            TwitterSentiment::Where('comment_id', $request->comment)->update(['sentiment' => $request->sentiment]);
        }
        return 200;
    }

    public function statusSentiment(Request $request)
    {
        $sentimiento = TwitterSentiment::Where('comment_id', '=', $request->comment_id)->first();
        if ($sentimiento) {
            $sentimiento->Where('comment_id', $request->comment_id)->update(['status' => $request->estado]);
        } else {
            TwitterSentiment::Create(['comment_id' => $request->comment_id,'sentiment' =>'Neutral', 'status' => $request->estado]);
        }
        return "Guardado con Exito";
    }
}
