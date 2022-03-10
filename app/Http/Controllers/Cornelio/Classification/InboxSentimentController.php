<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Page;
use App\Models\Sentiment_conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxSentimentController extends Controller
{
    public function pageInbox(){
        $user_id = Auth::id();
        $page = Page::where('user_id', $user_id)->pluck('page_name', 'page_id') ;
        return view('Cornelio.Classification.SentimentInbox.pageInbox',compact('page'));
    }

    public function selectInbox(){
        return view('Cornelio.Classification.SentimentInbox.selectInbox');
    }

    public function conversation(Request $request){
        $conv=Conversation::where('page_id','=',$request->page_id)->where('author_id','!=',$request->page_id)
            ->select('conv_id','author','author_id')->distinct('author')->get();
        return $conv;
    }

    public function message(Request $request){
        $mes=Conversation::where('conv_id','=',$request->conversacion)
           ->orderBy('created_time', 'asc')
           ->get();
       return $mes;
   }

    public function store(Request $request){
        $company_id=session('company_id');
        $sen=Sentiment_conversation::Where('msg_id','=',$request->msg_id)
            ->where('company_id','=',$company_id)->first();
        if($sen==null){
            Sentiment_conversation::create($request->all()+['company_id'=>$company_id]);
        }else{
            $sen = Sentiment_conversation::where('msg_id', '=', $request->msg_id)->update(['sentiment' => $request->sentimiento]);

        }
        return "guardado";
    }

    public function sentimentInbox (Request $request){
        $company_id=session('company_id');
        $sen=Sentiment_conversation::Where('conv_id','=',$request->conv_id)
            ->where('company_id','=',$company_id)->get();
        return $sen;
    }

    public function status(Request $request){
        $company_id=session('company_id');
        $sentimiento=Sentiment_conversation::Where('msg_id','=',$request->msg_id)
            ->Where('company_id','=',$company_id)
            ->first();
        if($sentimiento != null){
            $sentimiento->update(['estado' => $request->estado]);
            return "Agregado";

        }else{
            Sentiment_conversation::Create($request->all()+['company_id'=>$company_id]);
            return "Actualizado";

        }
    }
}
