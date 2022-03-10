<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Page;
use App\Models\Sentiment_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSentimentController extends Controller
{
    public function index(Request $request){
        $user_id=Auth::id();
        $sentiment_user=Sentiment_User::paginate();

        return view('Cornelio.Classification.AdminSentiment.index', compact('sentiment_user','user_id'));
    }

    public function create(){
        $user_id=Auth::id();
        $compain=Company::where('slug', session('company'))->first();
        $companies=$compain->id;
        $pages= Page::where('user_id',$user_id)->pluck('page_name','page_id');
        $sentiment_user=Sentiment_User::where('user_id',$user_id)->first();
        return view('Cornelio.Classification.AdminSentiment.create', compact('pages', 'sentiment_user', 'company'));
    }
    
    public function store(Request $request){
       $user_id=Auth::id();
        $company = session('company');
        $compain=Company::where('slug',$company)->first();
        $companies=$compain->id;
        $sentiment_user=Sentiment_User::create(['sentiment'=>$request->sentiment, 'sentiment_detail'=>$request->sentiment_detail, 'page_id'=>$request->page_name, 'user_id'=>$user_id ]);
        return redirect()->route('AdminSentiment_User.index',[$sentiment_user->id])->with('info','Registro guardado con éxito');
    }

    public function edit(Sentiment_User $sentiment_user){
        $user_id=Auth::id();
        $company = session('company');
        $compain=Company::where('slug',$company)->first();
        $companies=$compain->id;
        $pages= Page::where('user_id',$user_id)->pluck('page_name','page_id');
        $sentiment_user=Sentiment_User::where('user_id',$user_id)->first();
        return view('Cornelio.Classification.AdminSentiment.edit', compact('pages', 'sentiment_user'));
    }

    public function update(Request $request, Sentiment_User $sentiment_user){
        $sentiment_user->update($request->all());
        return redirect()->route('AdminSentiment_User.index',[$sentiment_user->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(Sentiment_User $sentiment_user){
        $sentiment_user->delete();
        return back()->with('info', 'Eliminada correctamente');
    }

    public function search(Request $request){
        $take=20;
        if($request->search){
            $sentiment_user = Sentiment_User::where('sentiment','like','%' . $request->search . '%')
                ->orWhere('sentiment_detail','LIKE','%' .$request->search. '%')
                ->take($take)
                ->get();
        }else{
            $sentiment_user = Sentiment_User::take($take)->get();
        }
        return $sentiment_user;
    }
}
