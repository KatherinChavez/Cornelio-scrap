<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Scraps;
use App\Models\Sentiment_conversation;
use Illuminate\Console\Command;
use Facebook;

class ScrapInbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapInbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatizacion inbox de una pagina en especifico';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // aca va la funcion
        //$page_id='107355765954206';
        $page_id='107043160966613';

        $config=array(
            'app_id' => env('APP_FB_ID_2'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );

        $fb = new Facebook\Facebook($config);
        $token=env('APP_FB_TOKEN');

        /*$fb = new Facebook\Facebook([
            'app_id' => '285183274843495',
            'app_secret' => 'fda8f3123eababe95aa45e20e5d51d5f',
            'default_graph_version' => 'v2.10'
        ]);*/
        //$extend=Scraps::where('page_id','=',$page_id)->where('user_id','=','10')->first();
        //$token=$extend['token'];
        //$token = 'EAAD8SXrHZCQQBAJXZCWmiSTf4qLIGA6vgRZBehH7vEUlGJeN79jamGFAsOYPKa9QD7pdU6jtX2tnsb6l6dGZBmx8NJDOi4x2hbSX2O3oAkROScLrkA6YBSRrmZApqe7kQkwgDQjXkj2Wt9iZB9QG5fBwULi1yvLqri306lXuOQbZAyz6nU5CvncREzFneP085G2KlBUkHej6QZDZD';
        /*$response = $fb->get(
            '/'.$page_id.'?fields=access_token,name',
            ''.$token.''
        );


         try {
            // Returns a `FacebookFacebookResponse` object
            
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return 'Graph returned an error: ' . $e->getMessage();
            //exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphNode();
        $page=$graphNode->asArray();
        $token=$page['access_token'];
        $page_name=$page['name'];
        */
        $user_id="2";
        // $parametros='/conversations?fields=id,messages{message.limit(100),created_time,from,id,}&link&limit=50';
        $parametros='?fields=name,conversations.limit(100){messages.limit(200){from,message,created_time}}';
        
        

        try {
            // $conversaciones=$fb->get('/'.$page_id.$parametros,$token);
            $response = $fb->get(
                '/'.$page_id.$parametros.'',
                ''.$token.''
            );

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            return 'Graph returned an error: ' . $e->getMessage();

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            return 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $conversaciones = $response->getGraphNode();
        $conversacionesArray=$conversaciones->asArray();
        $page_name=$conversacionesArray['name'];
        $conv=$conversacionesArray['conversations'];
        $count = count($conv);

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
                        'author'=>$author,'author_id'=>$author_id,'message'=>$menssage,'created_time'=>$created_time]);
                }
                if ($sentimentSQL == null) {
                    Sentiment_conversation::create(['conv_id'=>$conv_id,'msg_id'=>$msg_id,'estado'=>'0','user_id'=>$user_id]);
                }
            }
        }
    }
}
