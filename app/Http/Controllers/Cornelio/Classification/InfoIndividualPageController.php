<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Megacategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Scraps;
use App\Models\Sentiment;
use App\Models\TagsComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoIndividualPageController extends Controller
{
    public function SelectInfoPage(){
        $company = session('company');
        $user_id = Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $page = Page::where('company_id', $companies)->orderBy('page_name')->pluck('page_name', 'page_id') ;
        $categories = Category::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        return view('Cornelio.Classification.InfoIndividualPage.SelectInfoPage',compact('company', 'page','categories'));
    }

    public function InfoPage(Request $request){
        $company = session('company');
        $take=20;
        $user_id = Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $start_time =  Carbon::parse($request->inicio)->subDays(0);
        $end_time =  Carbon::parse($request->final);
        $posts = Post::where('page_id',$request->id)
            ->whereBetween('created_time',[$start_time , $end_time])
            ->with(['attachment','classification_category'=>function($q) use($companies){
                $q->where('classification_category.company_id',$companies);
            }])
            ->orderBy('posts.created_time', 'desc')
            ->paginate();
        return view('Cornelio.Classification.InfoIndividualPage.InfoPage',compact('posts'));
    }

    public function getPage(Request $request){
        $company = session('company');
        $take=20;
        $user_id = Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;

        $start_time =  Carbon::parse($request->inicio)->subDays(1);
        $end_time =  Carbon::parse($request->final);
        $post = Post::where('posts.page_id','=',$request->page_id)
            ->whereBetween('posts.created_time',[$start_time , $end_time])
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*', 'attachments.*')
            ->orderBy('posts.created_time', 'desc')
            ->distinct('posts.post_id')
           // ->take()
            ->get();

        return $post;
    }

    public function comparatorClassification(Request $request){
        $classification=Classification_Category::where('classification_category.post_id','=',$request->idPost)
            ->where('classification_category.user_id','=',$request->user)
            ->get();
        return $classification;

    }

}
