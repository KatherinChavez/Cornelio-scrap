<?php

namespace App\Http\Controllers\Cornelio\TwitterMention;

use App\Http\Controllers\Controller;
use App\Models\Twitter\TweetMention;
use App\Models\Twitter\TwitterScrap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentionController extends Controller
{
    public function indexPage()
    {
        $companies  = session('company_id');
        $page       = TwitterScrap::where('company_id', $companies)->orderBy('name')->pluck('name', 'page_id') ;
        return view('Twitter.Mention.index', compact('page'));
    }
    public function getData(Request $request)
    {
        $page_id    = base64_decode($request->id);
        $start_time = base64_decode($request->inicio);
        $end_time   = base64_decode($request->final);
        $page_name  = TwitterScrap::where('page_id', $page_id)->pluck('name')->first();
        $tweets     = TweetMention::where('page_id',$page_id)
                                    ->whereBetween('created_time',[$start_time , $end_time])
                                    ->groupBy('referenced_tweets')
                                    ->orderBy('created_time', 'desc')
                                    ->paginate();
        return view('Twitter.Mention.getData', compact('tweets', 'page_name'));
    }
}
