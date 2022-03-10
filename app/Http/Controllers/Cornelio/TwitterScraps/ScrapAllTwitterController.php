<?php

namespace App\Http\Controllers\Cornelio\TwitterScraps;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Company;
use App\Models\Compare;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterScrap;
use App\Models\Word;
use App\Traits\ScrapTweetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScrapAllTwitterController extends Controller
{
    use ScrapTweetTrait;
    public function index()
    {
        $company=session('company');
        $user_id=Auth::id();
        $compain=Company::where('slug',$company)->first();
        $companies=$compain->id;
        $categories= TwitterContent::where('company_id',$companies)->pluck('name','id');
        return view('Twitter.ScrapTwitter.ScrapAllTwitter',compact('categories', 'user_id'));
    }

    public function validateUser(Request $request)
    {
        $username = $this->get_username($request->username);
        return $username;
    }

    public function scrapTweet(Request $request)
    {
        $scrap = $this->get_Tweets($request, $request->limit);
        return $scrap;
    }

    public function commentTweet(Request $request)
    {
        try{
            $tweets = Tweet::where('author_id', $request->id)->take($request->limit)->orderBy('created_at', 'DESC')->get();
            foreach ($tweets as $tweet){
                $this->get_Comments($tweet, $request->limit);
            }
            return 200;
        }catch (\Exception $e){
            return 500;
        }
    }

    public function reactionTweet(Request $request)
    {
        try{
            $tweets = Tweet::where('author_id', $request->id)->take($request->limit)->orderBy('created_at', 'DESC')->get();
            foreach ($tweets as $tweet){
                $scrap = $this->get_reaction($tweet->id_tweet);
            }
            return 200;
        }catch (\Exception $e){
            return 500;
        }


    }

}
