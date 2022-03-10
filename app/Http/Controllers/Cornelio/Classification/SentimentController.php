<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Page;
use App\Models\Post;
use App\Models\Sentiment;
use App\Models\Sentiment_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentimentController extends Controller
{
    public function pageSentiment()
    {
//        $user_id = Auth::id();
        $page = Page::where('company_id', session('company_id'))->orderBy('page_name')->pluck('page_name', 'page_id');
        return view('Cornelio.Classification.Sentiment.pageSentiment',compact('page'));
    }

    public function page(Request $request)
    {
        $company= session('company');
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $paginas = Page::where('company_id', $companies)->get();
        return $paginas;
    }

    public function selectSentiment(Request $request)
    {
        $posts = Post::where('page_id',$request->page_id)
            ->with(['attachment','comments','reactions'])
            ->orderby('created_time', 'DESC')
            ->paginate();
        return view('Cornelio.Classification.Sentiment.SelectSentiment',compact('posts'));
    }

    public function SentimentComment(Request $request)
    {
        $comentarios = Comment::Where('comments.post_id', '=', $request->post_id)
            ->join('sentiments', 'sentiments.comment_id', '=', 'comments.comment_id')
            ->Where('sentiments.user_id', '=', $request->user)
            ->select('sentiments.*')
            ->distinct('comments.comment_id')
            ->get();

        return $comentarios;
    }

    public function personalizedFeeling(Request $request)
    {
        $sentimiento = Sentiment_User::where('sentiment', '!=', '')
            ->Where('page_id', '=', $request->page_id)
            ->Where('user_id', '=', $request->user)
            ->get();
        if ($sentimiento != null) {
            return $sentimiento;
        }
    }

    public function Sentiment(Request $request){
        if($request->post_id){
            $comentarios=Comment::Where('comments.post_id','=',$request->post_id)
                //->with('sentiment')
                ->join('sentiments','sentiments.comment_id','=','comments.comment_id')
                ->select('sentiments.*')
                ->distinct('comments.comment_id')
                ->get();
        }
        elseif ($request->page_id){
            $comentarios=Comment::where('page_id', $request->page_id)
                ->with(['sentiment'=>function($q){
                    $q->select('sentiment', 'comment_id');
                }])
                ->select('comment_id')
                ->get();

        }
        elseif ($request->tema){
            $comentarios=Comment::where('classification_category.subcategoria_id', $request->tema)
                ->join('posts', 'posts.post_id', 'comments.post_id')
                ->join('classification_category', 'classification_category.post_id', 'posts.post_id')
                ->with(['sentiment'=>function($q){
                    $q->select('sentiment', 'comment_id');
                }])
                ->select('comment_id')
                ->get();
        }
        else{
            $comentarios=Comment::join('sentiments','sentiments.comment_id','=','comments.comment_id')
                ->select('sentiments.*')
                ->distinct('comments.comment_id')->get();
        }
        return $comentarios;
    }

    public function updateSentiment(Request $request)
    {
        $sentimiento = Sentiment::Where('comment_id', $request->sentimiento)
            ->Where('user_id', '=', $request->user)
            ->first();
        if ($sentimiento == null) {
           $datos = Sentiment::create(['comment_id'=>$request->sentimiento, 'sentiment'=>$request->sen, 'user_id'=>$request->user]);

        } else {
            $datos =Sentiment::Where('comment_id', '=', $request->sentimiento)
                        ->update(['sentiment' => $request->sen, 'user_id'=>$request->user, ]);
        }
        return $datos;
    }

    public function statusSentiment(Request $request)
    {
        $sentimiento = Sentiment::Where('comment_id', '=', $request->id)
            ->Where('user_id', '=', $request->user)
            ->first();
        if ($sentimiento != null) {
            $sentimiento->Where('comment_id', '=', $request->id)->update(['estado' => $request->estado]);
        } else {
            Sentiment::Create(['comment_id' => $request->id, 'estado' => $request->estado, 'user_id' => $request->user]);
        }
        return "Guardado con Exito";
    }

    public function check(Request $request){
        $status = Sentiment::get();
        return $status;
    }
}
