<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Post;
// use App\Models\Scrap\Attachment;
// use App\Models\Scrap\Comment;
// use App\Models\Scrap\Page;
// use App\Models\Scrap\Post;
use App\Models\Social\Raise_case;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index(){
        $isAgencia=false;
        $user_id=Auth::id();
        if(auth()->user()->can('answers.index')){
            $isAgencia=true;
        }else{
            $isAgencia=false;
        }
        return view('Facebook.Comment.index',compact('isAgencia','user_id'));
    }
    function getPages(){
        $response['pages']=Page::where('company_id',session('company_id'))->where('status',1)->get();
        $response['answers']=Answer::where('company_id',session('company_id'))->get();
        return $response;
    }
    function getComments(Request $request){
        $skip=0;
        $take=30;

        if($request->current_pagination){
            $skip=$take*($request->current_pagination-1);
        }
        if($request->search){
            $comments=Comment::where('comment_from','LIKE',"%$request->search%")
                ->where('page_id',$request->page['page_id'])
                ->where(function($query) use ($request)  {
                    $query->whereNull('status');
                    $query->orWhere('status',5);
                })
                ->where('author_id','!=',$request->page['page_id'])
                ->orderBy('created_time','ASC')
                ->take($take)
                ->skip($skip)
                ->get();
            $total=Comment::where('comment_from','LIKE',"%$request->search%")
                ->where('page_id',$request->page['page_id'])
                ->where('author_id','!=',$request->page['page_id'])
                ->where(function($query) use ($request)  {
                    $query->whereNull('status');
                    $query->orWhere('status',5);
                })
                ->count();

        }else{
            $comments=Comment::where('page_id',$request->page['page_id'])
                ->take($take)
                ->skip($skip)
                ->orderBy('created_time','ASC')
                ->where('author_id','!=',$request->page['page_id'])
                ->where(function($query) use ($request)  {
                    $query->whereNull('status');
                    $query->orWhere('status',5);
                })
                ->get();
            $total=Comment::where('page_id',$request->page['page_id'])
                ->where(function($query) use ($request)  {
                    $query->whereNull('status');
                    $query->orWhere('status',5);
                })
                ->where('author_id','!=',$request->page['page_id'])
                ->count();
        }
        $response['comments']=$comments;
        $response['total']=$total;
        $response['per_page']=$take;
        return $response;
    }
    function getPost(Request $request){
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        if ($comment->attend_by==null){
            $comment->update(['attend_by'=>Auth::id()]);
        }
        $post=Post::where('post_id',$request->post_id)->first();
        $attachment=Attachment::where('post_id',$request->post_id)->first();

        $comments=Comment::where(function($query) use ($request)  {
            $query->where('comment_id',$request->comment_id);
            $query->orWhere('parent_id',$request->comment_id);
            if($request->parent_id!=null){
                $query->orWhere('parent_id',$request->parent_id);
            }
        })
            ->orderBy('created_time', 'ASC')
            ->get();
        $response['post']=$post;
        $response['attachment']=$attachment;
        $response['comments']=$comments;

        return $response ;
    }
    function Search(Request  $request){
        $comments=[];
        if($request->search){
            $comments=Comment::where('comment_from','LIKE',"%$request->search%")
                ->where('page_id',$request->page['page_id'])
                ->get()->take(30);
        }else{
            $comments=Comment::where('page_id',$request->page['page_id'])->take(30)->get();
        }
        return $comments;
    }
    function checkComment(Request $request){
        $comment=Comment::where('comment_id',$request->comment_id)->whereIn('status',[1,3,4])->first();
        $response['check']=true;
        if($comment){
            $response['check']=false;
        }
        return $response;
    }
    function newComment(Request  $request){
        $data['user_id']=Auth::id();
        $data['created_time']=Carbon::now()->format('Y-m-d H:i:s');
        $parent=Comment::where('comment_id',$request->parent_id)->first();
        $history['user_id']=Auth::id();
        $history['status']=1;
        $history['created_at']=$data['created_time'];
        if($parent->history==null){
            $json[] = $history;
        }else{
            $parent_history=$parent->history;
            $json=json_decode($parent_history,true);
            $json[]=$history;
        }
        $parent->update(['status'=>1,'history'=>$json,'user_id'=>Auth::id()]);
        Comment::create(array_merge($request->all(), $data));

        $raise=Raise_case::where('comment_id',$request->comment_id)->first();
        if($raise){
            $raise->update(['message'=>$request->comment]);
        }
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        return $comment;
    }
    function hiddenComment(Request  $request){
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        $history['user_id']=Auth::id();
        $history['status']=3;
        $history['created_at']=Carbon::now()->format('Y-m-d H:i:s');
        if($comment->history==null){
            $json[] = $history;
        }else{
            $parent_history=$comment->history;
            $json=json_decode($parent_history,true);
            $json[]=$history;
        }
        $comment->update(['status'=>3,'user_id'=>Auth::id(),'history'=>$json]);
    }
    function deleteComment(Request $request){
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        $history['user_id']=Auth::id();
        $history['status']=4;
        $history['created_at']=Carbon::now()->format('Y-m-d H:i:s');
        if($comment->history==null){
            $json[] = $history;
        }else{
            $parent_history=$comment->history;
            $json=json_decode($parent_history,true);
            $json[]=$history;
        }
        $comment->update(['status'=>4,'user_id'=>Auth::id(),'history'=>$json]);

    }
    function readComment(Request $request){
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        $history['user_id']=Auth::id();
        $history['status']=2;
        $history['created_at']=Carbon::now()->format('Y-m-d H:i:s');
        if($comment->history==null){
            $json[] = $history;
        }else{
            $parent_history=$comment->history;
            $json=json_decode($parent_history,true);
            $json[]=$history;
        }
        $comment->update(['status'=>2,'user_id'=>Auth::id(),'history'=>$json]);
    }
    function unreadComment(Request $request){
        $comment=Comment::where('comment_id',$request->comment_id)->first();
        $history['user_id']=Auth::id();
        $history['status']=null;
        $history['created_at']=Carbon::now()->format('Y-m-d H:i:s');
        if($comment->history==null){
            $json[] = $history;
        }else{
            $parent_history=$comment->history;
            $json=json_decode($parent_history,true);
            $json[]=$history;
        }
        if($comment->status==2){
            $comment->update(['status'=>null,'user_id'=>Auth::id(),'history'=>$json,'attend_by'=>null]);
        }else{
            $comment->update(['user_id'=>Auth::id(),'history'=>$json,'attend_by'=>null]);
        }
    }

    function GetCommentDB(Request $request){
        $comment = Comment::where('post_id',$request->post_id)->get();
        $consulta = array(
            'comments'=> $comment
        );
        return $consulta;
    }
    
}
