<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Subcategory;
use App\Models\Sentiment_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentimentSubcategoryController extends Controller
{
    public function sentimentSubCategory(){

        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $subcategory = Subcategory::where('company_id', $companies)->orderBy('name')->pluck('name', 'id') ;
        return view('Cornelio.Classification.SentimentSubcategory.sentimentSubCategory',compact('subcategory'));
    }

    public function personalizedFeelingSub(Request $request)
    {
        $sentimiento = Sentiment_User::where('sentiment', '!=', '')
            ->Where('user_id', '=', $request->user)
            ->get();
        if ($sentimiento != null) {
            return $sentimiento;
        }
    }

    public function reactionPost(Request $request){
        $post=Comment::where('post_id', $request->post_id)
                    ->join('sentiments','sentiments.comment_id','=','comments.comment_id')
                    ->get();

        return $post;
    }
}
