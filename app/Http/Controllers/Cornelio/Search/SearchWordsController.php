<?php

namespace App\Http\Controllers\Cornelio\Search;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchWordsController extends Controller
{
    public function index($company){
        return view('Cornelio.Search.SearchWords.index',compact('company'));
    }

    public function search(Request $request){
        $take=20;
        if($request->palabra){
            $posts = Post::where('page_name','like','%' . $request->palabra . '%')
                        ->orWhere('content','LIKE','%' .$request->palabra. '%')
                ->take($take)
                ->get();
        }else{
            $posts = Post::take($take)->get();
        }
        return $posts;
    }
}
