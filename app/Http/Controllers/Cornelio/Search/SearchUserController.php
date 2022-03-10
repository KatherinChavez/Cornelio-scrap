<?php

namespace App\Http\Controllers\Cornelio\Search;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class SearchUserController extends Controller
{
    public function indexUser($company){
        return view('Cornelio.Search.SearchUser.indexUser',compact('company'));
    }

    public function searchUser(Request $request){
        $take=20;
        if($request->users){
            $comments = Comment::where('comments.commented_from','like','%' . $request->users . '%')
                ->orWhere('comments.comment','LIKE','%' .$request->users. '%')
                ->join('posts', 'comments.id', '=', 'posts.id')
                ->select('comments.*', 'posts.page_name', 'posts.content')
                ->take($take)
                ->get();

        }else{
            $comments = Comment::join('posts', 'comments.id', '=', 'posts.id')
                ->select('comments.*', 'posts.page_name', 'posts.content')
                ->take($take)
                ->get();
        }
        return $comments;
    }
}
