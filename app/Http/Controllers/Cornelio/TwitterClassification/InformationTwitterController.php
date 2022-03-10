<?php

namespace App\Http\Controllers\Cornelio\TwitterClassification;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Page;
use App\Models\Subcategory;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TweetAttachmet;
use App\Models\Twitter\TweetComment;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterScrap;
use App\Models\Twitter\TwitterSentiment;
use App\Traits\TopicCountTrait;
use Illuminate\Http\Request;

class InformationTwitterController extends Controller
{
    use TopicCountTrait;
    public function indexSelect()
    {
        $companies  = session('company_id');
        $page       = TwitterScrap::where('company_id', $companies)->orderBy('name')->pluck('name', 'page_id') ;
        $categories = TwitterContent::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        return view('Twitter.infoTwitter.select', compact('page', 'categories'));
    }

    public function pageTwitter(Request $request){
        $page_id    = base64_decode($request->id);
        $start_time = base64_decode($request->inicio);
        $end_time   = base64_decode($request->final);
        $companies  = session('company_id');
        $topics     = Subcategory::where('company_id', $companies)->orderBy('name')->pluck('name', 'id');
        $tweets     = Tweet::where('author_id',$page_id)
                            ->whereBetween('created_time',[$start_time , $end_time])
                            ->with(['attachment','classification'=>function($q) use($companies){
                                $q->where('twitter_classifications.company_id',$companies);
                            }])
                            ->orderBy('tweets.created_time', 'desc')
                            ->paginate();
        $classifications = TwitterClassification::where('page_id',$page_id)
            ->where('twitter_classifications.company_id', $companies)
            ->join('subcategory', 'subcategory.id', 'twitter_classifications.subcategoria_id')
            ->groupBy('subcategory.id')
            ->get();
        return view('Twitter.infoTwitter.PageTwitter',compact('tweets', 'classifications', 'topics'));
    }

    public function contentTwitter(Request $request){
        $category   = base64_decode($request->id);
        $start_time = base64_decode($request->inicio);
        $end_time   = base64_decode($request->final);
        $companies  = session('company_id');
        $pages      = TwitterScrap::select('page_id')->where('categoria_id', $category)->pluck('page_id');
        $tweets     = Tweet::whereIn('author_id',$pages)
                            ->whereBetween('created_time',[$start_time , $end_time])
                            ->with(['attachment','classification' =>function($q) use($companies){
                                $q->where('twitter_classifications.company_id',$companies);}])
                            ->orderBy('tweets.created_time','DESC')
                            ->paginate();

        $classifications = TwitterClassification::whereIn('page_id',$pages)
            ->where('twitter_classifications.company_id', $companies)
            ->join('subcategory', 'subcategory.id', 'twitter_classifications.subcategoria_id')
            ->groupBy('subcategory.id')
            ->get();
        return view('Twitter.infoTwitter.ContentTwitter', compact('tweets','classifications'));
    }

    public function classificationTweet(Request $request){
        $companies      = session('company_id');
        $classification = TwitterClassification::where('id_tweet', $request->id_tweet)->first();
        if($classification){
            $classification->update(['subcategoria_id' => $request->subcategoria, 'company_id' => $companies]);
            return $classification;
        }
        else{
            $classification = TwitterClassification::create([
                'page_id'         => $request->page,
                'id_tweet'        => $request->id_tweet,
                'subcategoria_id' => $request->subcategoria,
                'company_id'      => $companies,
            ]);
        }
        return $classification;
    }

    public function sendWhatsappClassification(Request $request){
        $sub       = Subcategory::where('id', '=', $request->sub)->first();
        $sub_name  = $sub['name'];
        $numbers   = NumberWhatsapp::where('subcategory_id', $sub->id)->pluck('numeroTelefono');
        $comp      = Company::where('id', $sub->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $page      = Tweet::where('id_tweet', $request->tweet_id)
            ->join('twitter_scraps', 'twitter_scraps.page_id', 'tweets.author_id')
            ->select('username', 'twitter_scraps.name')
            ->first();
        if(count($numbers) > 0){
            foreach ($numbers as $number){
                if($comp){
                    $client_id = $comp->client_id;
                    $instance  = $comp->instance;
                }
                $nameSub    = $this->eliminar_acentos(str_replace(' ', '+', $sub_name));
                $namePage   = $this->eliminar_acentos(str_replace(' ', '+', $page->name));
                $message    = "%C2%A1Hola%21+Tengo+la+siguiente+alerta+relacionada+con+$nameSub+la+encontr%C3%A9+en+$namePage%2C+ac%C3%A1+te+dejo+el+link+para+que+la+veas%3A+https://twitter.com/$page->username/status/$request->tweet_id";
                $urlMessage = 'https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$number.'&message='.$message.'&type=text&=';
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
                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$number.'&message='.$message.'&type=text&=';
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
                }
            }
        }else{
            return 403;
        }
    }
}
