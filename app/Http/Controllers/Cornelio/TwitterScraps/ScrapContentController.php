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

class ScrapContentController extends Controller
{
    use ScrapTweetTrait;
    public function index()
    {
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $categories = TwitterContent::join('twitter_scraps','twitter_scraps.categoria_id', 'twitter_contents.id')
            ->where('twitter_contents.company_id', $companies)
            ->pluck('twitter_contents.name', 'twitter_contents.id');
        return view('Twitter.ScrapContent.index',compact('categories'));
    }

    public function scrapTweetContent(Request $request){
        $paginas = TwitterScrap::where('categoria_id', '=', $request->categoria_id)
            ->distinct()
            ->select('page_id as id', 'username', 'name')
            ->get();
        foreach($paginas as $pagina){
            $this->get_Tweets($pagina, 100);
            $tweets = Tweet::where('author_id', $pagina->id)->orderBy('created_at', 'DESC')->take(100)->get();
            foreach ($tweets as $tweet){
                $this->get_Comments($tweet, 100);
            }
        }
    }

    public function scrapTweetReaction(Request $request){
        $paginas = TwitterScrap::where('categoria_id', '=', $request->categoria_id)
            ->distinct()
            ->select('page_id as id', 'username', 'name')
            ->get();
        foreach($paginas as $pagina){
            $tweets = Tweet::where('author_id', $pagina->id)->take(100)->orderBy('created_at', 'DESC')->get();
            foreach ($tweets as $tweet){
                $this->get_reaction($tweet->id_tweet);
            }
        }

    }


}
