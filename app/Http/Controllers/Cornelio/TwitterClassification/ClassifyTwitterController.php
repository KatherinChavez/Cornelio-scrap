<?php

namespace App\Http\Controllers\Cornelio\TwitterClassification;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Compare;
use App\Models\Megacategory;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Scraps;
use App\Models\Subcategory;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterNotification;
use App\Models\Twitter\TwitterScrap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\CodeCoverage\TestFixture\C;
use Spipu\Html2Pdf\Tag\Html\Sub;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ClassifyTwitterController extends Controller
{
    public function index(Request $request)
    {
        $star_time = Carbon::now()->subDay(1);
        $end_time  = Carbon::now();
        if($request->search){
            $classifications = TwitterClassification::whereBetween('twitter_classifications.created_at',[$star_time , $end_time])
                ->with('page')
                ->where('twitter_classifications.company_id', session('company_id'))
                ->join('subcategory', 'subcategory.id', 'twitter_classifications.subcategoria_id')
                ->join('tweets', 'tweets.id_tweet', 'twitter_classifications.id_tweet')
                ->join('twitter_scraps', 'twitter_scraps.page_id', 'twitter_classifications.page_id')
                ->where('twitter_scraps.name', 'like', '%' . $request->search . '%')
                ->orWhere('subcategory.name', 'like', '%' . $request->search . '%')
                ->orWhere('tweets.content', 'like', '%' . $request->search . '%')
                ->orderBy('twitter_classifications.created_at', 'DESC')
                ->select('tweets.content', 'subcategory.name', 'twitter_classifications.*')
                ->groupBy('twitter_classifications.id_tweet')
                ->get();
        }
        else{
            $classifications = TwitterClassification::whereBetween('twitter_classifications.created_at',[$star_time , $end_time])
                ->with('page')
                ->where('twitter_classifications.company_id', session('company_id'))
                ->join('subcategory', 'subcategory.id', 'twitter_classifications.subcategoria_id')
                ->join('tweets', 'tweets.id_tweet', 'twitter_classifications.id_tweet')
                ->orderBy('twitter_classifications.created_at', 'DESC')
                ->select('tweets.content', 'subcategory.name', 'twitter_classifications.*')
                ->get();
        }
        $topics = Subcategory::where('company_id', session('company_id'))->pluck('name','id');
        return view('Twitter.ClassifyTwitter.index', compact('classifications', 'topics'));
    }

    public function classify(Request $request){
        $start      = $request->start;
        $end        = $request->end;
        $topics     = $request->subcategory_id;
        $company_id = session('company_id');
        $company    = Company::where('id', session('company_id'))->first();

        $start_time_for_query = Carbon::parse($start)->format('Y-m-d');
        $end_time_for_query   = Carbon::parse($end)->addDay()->format('Y-m-d');

        $contenido = Category::where('company_id', $company_id)->get();
        $scraps    = TwitterScrap::where('company_id',$company_id)->groupBy('page_id')->get()->pluck('page_id');
        $tweets    = Tweet::whereIn('tweets.author_id',$scraps)
                            ->whereBetween('tweets.created_at',[$start_time_for_query , $end_time_for_query])
                            ->join('twitter_scraps', 'twitter_scraps.page_id', 'tweets.author_id')
                            ->select('tweets.*', 'twitter_scraps.username as username')
                            ->get();
        foreach ($contenido as $contenidos){
            //Se llama los temas que tiene el contenido
            $sub                 = Subcategory::where('id', $topics)->first();
            $subcategoria_id     = $topics;
            $subcategoria_nombre = $sub->name;

            //consulta los numeros de whatsApp asignados a la subcategoria
            $contactos=NumberWhatsapp::where('subcategory_id', $subcategoria_id)->get();

            //consulta las palabras que se deben de usar para clasificar los post en la subcategoria
            $palabras=Compare::where('subcategoria_id', $subcategoria_id)->orderBy('prioridad', 'asc')->get();

            //consulta si el tema/subcategoria debe de ser alertada o no
            $alerta=Alert::where('subcategory_id', $subcategoria_id)->first();
            $alerta??$alerta['notification']=0;

            //recorre cada palabra para comparar en los post
            foreach ($palabras as $palabra){
                $comp       = $palabra['palabra'];
                $palabra_id = $palabra['id'];
                $prioridad  = $palabra['prioridad'];

                //recorre cada post
                foreach ($tweets as $tweet){
                    $content  = $tweet['content'];
                    $tweet_id = $tweet['id_tweet'];
                    $page_id  = $tweet['author_id'];

                    //busca la palabra dentro de la publicacion
                    $find = stripos($content, $comp);

                    // si se encuentra la palabra dentro de la publicacion
                    //if($find == True){
                    if($find == true || $find !== false ){
                        // se consulta si la publicacion ya se notifico con la palabra encontrada
                        $notificacion = TwitterNotification::where('id_tweet', $tweet_id)
                                                            ->where('word', $palabra_id)
                                                            ->where('subcategory_id', $subcategoria_id)
                                                            ->first();

                        //si no se a notificado la publicacion con la palabra encontrada
                        if($notificacion == null){
                            //consulta si ya se clasifico el post
                            $clasificacion = TwitterClassification::where('id_tweet', $tweet_id)
                                ->where('subcategoria_id',$subcategoria_id)
                                ->first();

                            // si no se a clasificado se procede a clasificar
                            if($clasificacion == null){
                                TwitterClassification::create([
                                    'page_id'         => $page_id,
                                    'id_tweet'        => $tweet_id,
                                    'subcategoria_id' => $subcategoria_id,
                                    'company_id'      => $company_id
                                ]);
                            }

                            $estado='0';
                            /* segun la prioridad de la plabra encontrada se procede a
                            0 => 3 no notificar por ningun medio
                            1 => 2 notificar canal de telegram de la agencia
                            2 => 1 notificar al canal respectivo de la palabra
                            */
                            switch ($prioridad)
                            {
                                //No se notifica
                                case 3: $estado=0;
                                    break;
                                case 2: $estado=2;
                                    // Se revisa si se debe de alertar o no, de ser así notifica al canal de la agencia
                                    if ($alerta['notification'] == 1) {
                                        $encP  = base64_encode($tweet_id);
                                        $encS  = base64_encode($topics);
                                        $encC  = base64_encode($company_id);
                                        $token = env('TELEGRAM_TOKEN');
                                        $url   = "https://api.telegram.org/bot$token/sendMessage";
                                        try {
                                            $telegram = Company::where('id', $company_id)->first();
                                            $channel  = $telegram->channel;
                                            $findNum  = stripos($channel, "-100");
                                            $chat_id  = ($findNum === false) ? "-100" . $channel : $channel;
                                            $url_app  = env('APP_URL');
                                            $data     = array(
                                                "chat_id" => $chat_id,
                                                "text"    =>
                                                    "Hola! tengo la siguiente alerta, para la palabra: $comp y fue clasificada en: $subcategoria_nombre, ".
                                                    "acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id.".
                                                    " En el siguiente link lo podrás administrar => {$url_app}ManagementTweet/$encC/$encP/$encS"
                                            );
                                            $client = new client();
                                            $res    = $client->get($url, array(RequestOptions::JSON => $data));
                                        }catch (\exception $e){
                                            $phones = [$company->phone, $company->phoneOptional];
                                            foreach ($phones as $contactoArray) {
                                                $contacto=$contactoArray;
                                                $message = "Hola! tengo la siguiente alerta, para la palabra: $comp y fue clasificada en: $subcategoria_nombre, ".
                                                    "acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id.".
                                                    " En el siguiente link lo podrás administrar => {$url_app}ManagementTweet/$encC/$encP/$encS";
                                                $data = [
                                                    'phone' => $contacto,
                                                    'body' => $message,
                                                ];
                                                $json = json_encode($data); // Encode data to JSON
                                                $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                                                $options = stream_context_create(['http' => [
                                                    'method'  => 'POST',
                                                    'header'  => 'Content-type: application/json',
                                                    'content' => $json
                                                ]
                                                ]);
                                                $result = file_get_contents($url, false, $options);
                                                continue;
                                            }
                                            //return"La palabra no puede ser notificada";
                                        }catch (\Exception $e){
                                            $datos=array(
                                                'link'=>"{$url_app}ManagementTweet/$encC/$encP/$encS",
                                            );
                                            $email = $telegram['emailCompanies'];
                                            $this->subject="Clasificación del tema de $subcategoria_nombre ";
                                            Mail::send('Notification.Month', $datos, function ($message) use ($email) {
                                                $message->to($email)->subject($this->subject);
                                                $message->cc('hosmara@publicidadweb.cr')->subject($this->subject);
                                            });
                                            //continue;
                                        }

                                    }
                                    break;
                                case 1: $estado=1;
                                    if ($alerta['notification'] == 1) {
                                        //Envia mensaje por medio de telegram
                                        $token = env('TELEGRAM_TOKEN');
                                        $url   = "https://api.telegram.org/bot$token/sendMessage";

                                        try {
                                            $channel = $sub['channel'];
                                            $findNum = stripos($channel, "-100");
                                            $chat_id = ($findNum === false) ? "-100" . $channel : $channel;
                                            $data    = array(
                                                "chat_id" => $chat_id,
                                                "text" => "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id");

                                            $client = new client();
                                            $res = $client->get($url, array(RequestOptions::JSON => $data));

                                            //Envia mensaje por medio de Whatsapp
                                            foreach ($contactos as $contactoArray) {
                                                $contacto = $contactoArray['numeroTelefono'];
                                                $message  = "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id";
                                                $data = [
                                                    'phone' => $contacto,
                                                    'body'  => $message,
                                                ];
                                                $json = json_encode($data); // Encode data to JSON
                                                $url = env('WHA_API_URL').env('WHA_API_TOKEN');
                                                $options = stream_context_create(['http' => [
                                                    'method'  => 'POST',
                                                    'header'  => 'Content-type: application/json',
                                                    'content' => $json
                                                ]
                                                ]);
                                                $result = file_get_contents($url, false, $options);

                                                //Envia mensajes normales
                                                $key = $company->key;
                                                if($key){
                                                    $this->sendSms($contacto, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                                }

                                                continue;
                                            }
                                        }catch (\exception $e) {
                                            return "La palabra no puede ser notificada";
                                        }
                                    }
                                    break;
                            }

                            //Se guarda la notificacion
                            TwitterNotification::create([
                                'id_tweet'       => $tweet_id,
                                'subcategory_id' => $subcategoria_id,
                                'word'           => $palabra_id,
                                'status'         => $estado,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function edit(Request $request)
    {
        $classification = TwitterClassification::where('twitter_classifications.id', $request->id_classification)
            ->join('twitter_scraps', 'twitter_scraps.page_id', 'twitter_classifications.page_id')
            ->join('subcategory', 'subcategory.id', 'twitter_classifications.subcategoria_id')
            ->join('tweets', 'tweets.id_tweet', 'twitter_classifications.id_tweet')
            ->get();
        return $classification;
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_classification' => 'required',
            'subcategory' => 'required',
        ]);
        $classification = TwitterClassification::where('id', $request->id_classification)->update(['subcategoria_id' => $request->subcategory]);
        return redirect()->route('ClassifyTopics.index')->with('info','Se ha actualizado exitosamente');
    }

    public function destroy(TwitterClassification $twitterClassification)
    {
        $twitterClassification->delete();
        return redirect()->route('ClassifyTopics.index')->with('info', 'Eliminada correctamente');
    }
}
