<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Conversation;
use App\Models\Page;
use App\Models\Sentiment_conversation;
use Illuminate\Http\Request;
use Facebook;
use Illuminate\Support\Facades\Auth;

class ScrapInboxController extends Controller
{
    public function selectInbox(){
        return view('Cornelio.Scraps.ScrapInbox.selectInbox');

    }

    public function scrapInbox(Request $request){
        $token = $request->access_token;
        $page_id=$request->page;
        $page_name=$request->page_name;
        $company_id=session('company_id');

        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );

        $fb = new Facebook\Facebook($config);

        //$token=env('APP_FB_TOKEN');

        $parametros='/?fields=conversations.limit(50){messages.limit(100){from,message,created_time}}';
        $estado = False;


        try {
            $response = $fb->get(
                '/'.$page_id.$parametros.'',
                ''.$token.''
            );
            $estado = True;
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            return 'Graph returned an error: ' . $e->getMessage();
            $estado = False;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            return 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
            $estado = False;
        }
        $conversaciones = $response->getGraphNode();
        $conversacionesArray=$conversaciones->asArray();
        $conv=$conversacionesArray['conversations'];
        $count = count($conv);
        if($count<1){
            return Response()->json("No tiene conversaciones");
        }
        for ($i = 0; $i < $count; $i++) {
            $conversacion=$conv[$i];
            $conv_id=$conversacion['id'];
            $mensajes=$conversacion['messages'];
            $count_mensajes=count($mensajes);

            for ($j=0; $j < $count_mensajes; $j++){
                $mensaje=$mensajes[$j];
                $msg_id=$mensaje['id'];
                $menssage=$mensaje['message'];
                $author=$mensaje['from']['name'];
                $author_id=$mensaje['from']['id'];
                $created_time=$mensaje['created_time'];
                $created_time=$created_time->format('Y-m-d G:i:s');
                $created_time=strtotime ( '-6 hour' , strtotime ( $created_time ) ) ;
                $created_time= date ( 'Y-m-d G:i:s' , $created_time );

                $convSQL=Conversation::Where('msg_id','=',$msg_id)->first();
                $sentimentSQL=Sentiment_conversation::Where('msg_id','=',$msg_id)->first();
                if ($convSQL == null) {
                    Conversation::create(['conv_id'=>$conv_id,'msg_id'=>$msg_id,'page_id'=>$page_id,'page_name'=>$page_name,
                        'author'=>$author,'author_id'=>$author_id,'message'=>$menssage,'sentiment'=>'Neutral','created_time'=>$created_time]);
                }
                if ($sentimentSQL == null) {
                    Sentiment_conversation::create(['conv_id'=>$conv_id,'msg_id'=>$msg_id,'estado'=>'0','company_id'=>$company_id]);
                }
            }
        }
        return Response()->json($estado);
        // return "Scrap Terminado";
    }
}
