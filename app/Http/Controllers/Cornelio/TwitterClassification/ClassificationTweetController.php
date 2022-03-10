<?php

namespace App\Http\Controllers\Cornelio\TwitterClassification;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TweetAttachmet;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterScrap;
use App\Traits\TopicCountTrait;
use Illuminate\Http\Request;

class ClassificationTweetController extends Controller
{
    use TopicCountTrait;
    public function ManagementTweet($com,$tweet,$sub)
    {
        $tweet=base64_decode($tweet);
        $sub=base64_decode($sub);
        $com=base64_decode($com);
        $subcategorias = Subcategory::where('company_id',$com)->get();
        $tweet = Tweet::where('tweets.id_tweet', $tweet)->first();
        $attachment = TweetAttachmet::where('id_tweet', $tweet)->first();
        $data=array('tweet'=>$tweet,'attachment'=>$attachment,'subcategoria'=>$sub,'compania'=>$com,'subcategories'=>$subcategorias);
        return view('Twitter.ClassificationTweet.ManagementTweet',$data);
    }

    public function ToDisableTwitter(Request $request)
    {
        $clasificacion= TwitterClassification::where('id_tweet','=',$request->id)
            ->where('subcategoria_id','=',$request->sub)
            ->first();
        if($clasificacion == null){
        }else{
            return 'eliminado';
        }
    }

    public function Telegram_sendTwitter(Request $request){
        $sub           = Subcategory::where('id', $request->sub)->first();
        $channel       = $sub['channel'];
        $tema          = $sub['name'];
        $token         = env('TELEGRAM_TOKEN');
        $url           = "https://api.telegram.org/bot$token/sendMessage";
        $username      = Tweet::where('id_tweet', $request->tweet_id)
                            ->join('twitter_scraps', 'twitter_scraps.page_id', 'tweets.author_id')
                            ->select('username')
                            ->pluck('username')
                            ->first();
        $find = stripos($channel, "-100");
        if($find === false){
            //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
            $canal="-100";
            $chat_id =$canal.$channel;
        }else{
            $chat_id =$channel;
        }

        $data = array(
            "chat_id" => $chat_id,
            "text" => "Hola! tengo la siguiente alerta, relacionada con: ".$tema.", acá te dejo el link para que la veas: https://twitter.com/$username/status/$request->tweet_id"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
    }

    public function Whatsapp_sendTwitter(Request $request)
    {
        $data      = [];
        $sub       = Subcategory::where('id', '=', $request->sub)->first();
        $sub_name  = $sub['name'];
        $comp      = Company::where('id', $sub->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $username  = Tweet::where('id_tweet', $request->tweet_id)
                        ->join('twitter_scraps', 'twitter_scraps.page_id', 'tweets.author_id')
                        ->select('username')
                        ->pluck('username')
                        ->first();

        if ($request->phone == 'todos') {
            $contactos = NumberWhatsapp::where('subcategory_id', '=', $request->sub)->pluck('numeroTelefono');
        } else {
            $destination = $request->phone;
            $contactos = array($destination);
        }

        foreach ($contactos as $contacto) {
            if($comp){
                $client_id = $comp->client_id;
                $instance  = $comp->instance;
            }
            $nameSub    = $this->eliminar_acentos(str_replace(' ', '+', $sub_name));
            $namePage   = $this->eliminar_acentos(str_replace(' ', '+', $request->pagina));
            $message    = "%C2%A1Hola%21+Tengo+la+siguiente+alerta+relacionada+con+$nameSub+la+encontr%C3%A9+en+$namePage%2C+ac%C3%A1+te+dejo+el+link+para+que+la+veas%3A+https://twitter.com/$username/status/$request->tweet_id";

            BitacoraMessageFb::create([
                'type'        => $contacto != 0 ? 'phone'   : 'group',
                'number'      => $contacto,
                'typeMessage' =>'text',
                'report'      => '99',
                'message'     => $message,
                'status'      => 0,
            ]);
            return 200;

            /*$urlMessage = 'https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
            $result     = file_get_contents($urlMessage);
            $data       = json_decode($result);
            if($data->status == true){
                //Se envia correctamente el mensaje con la instancia de la compañia
                return 200;
            }
            elseif ($data->status == false){
                //Se encuentra desconectada la instancia de la compañia, por lo tanto se llama la instancia de Cornelio
                $client_id  = env('CLIENT_ID');
                $instance   = env('INSTANCE');
                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                $result     = file_get_contents($urlMessage);
                $data       = json_decode($result);
                if ($data->status == false && $data->message == "Device not connected "){
                    //Se indica que la instancia de Cornelio se encuentra desconectada
                    return 500;
                }
                elseif ($data->status == false){
                    //Se encontro algun problema al enviar mnensaje con la instancia de Cornelio
                    return 400;
                }
            }
            elseif ($data->status == false){
                //Se encuentra algun error en la instancia que tiene la compañia, se llama la instancia que tiene cornelio
                $client_id  = env('CLIENT_ID');
                $instance   = env('INSTANCE');
                $urlMessage = 'https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                $result     = file_get_contents($urlMessage);
                $data       = json_decode($result);
                if ($data->status == false && $data->message == "Device not connected "){
                    //Se indica que la instancia de cornelio se encuentre desconectada
                    return 500;
                }
                elseif ($data->status == false){
                    //Se indica que se encontro un problema al enviar el mensaje
                    return 400;
                }
            }*/
        }
    }

    public function DeclassifyTweet(Request $request){
        $clasificacion = TwitterClassification::where('id_tweet',$request->id)
                        ->where('subcategoria_id',$request->sub)
                        ->first();
        if($clasificacion == null){
            return back()->with('info', 'El tweet ya se encuentra desclasificado');
        }else{
            $clasificacion->delete();
            return 'eliminado';
        }
    }

    public function ReclassifyTwitter(Request $request){
        $recla =TwitterClassification::where('id_tweet', $request->tweet_id)->first();
        if($recla != null){
            $recla->update(['subcategoria_id'=>$request->subcategoria, 'company_id'=>$request->compania]);
            return $recla;
        }
        else{
            $scrap = TwitterScrap::where('name', $request->page)->first();
            $recla = TwitterClassification::create([
                'page_id'         => $scrap->page_id,
                'id_tweet'        => $request->tweet_id,
                'subcategoria_id' => $request->subcategoria,
                'company_id'      => $request->compania,
            ]);
        }
        return $recla;
    }
}
