<?php

namespace App\Console\Commands\Twitter;

use App\Models\Category;
use App\Models\Company;
use App\Models\Scraps;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterNotification;
use App\Models\Twitter\TwitterScrap;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Compare;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ClassificationContentTwitter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClassificationTwitter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clasificacion automatica de posteos de Twitter de un contenido(categoria) que se encuentra clasificado';

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
        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time   =  Carbon::now();


        // convierte las fechas para usarlas en consultas
        $start_time_for_query = Carbon::parse($start_time)->format('Y-m-d');
        $end_time_for_query   = Carbon::parse($end_time)->addDay()->format('Y-m-d');

        // carga companies
        $companies=Company::where('status',1)->get();

        //recorre las companies
        foreach ($companies as $company){
            //se llama todos los contenidos de company
            $contenido = TwitterContent::where('company_id',$company->id)->get();
            $scraps    = TwitterScrap::select('page_id')->where('company_id',$company->id)->groupBy('page_id')->get()->pluck('page_id');

            /*
             * obtiene las publicaciones de la tabla post y el titulo de los adjuntos de la tabla adjunto entre un rango de fechas
             * ademas de solo publicaciones de las paginas que tengo agrupadas a los contenidos de la company
             */

            $tweets = Tweet::whereIn('tweets.author_id',$scraps)
                ->whereBetween('tweets.created_at',[$start_time_for_query , $end_time_for_query])
                ->join('twitter_scraps', 'twitter_scraps.page_id', 'tweets.author_id')
                ->select('tweets.*', 'twitter_scraps.username as username')
                ->get();

            //Recorre cada unos de los contenido(categoria)
            foreach ($contenido as $contenidos){
                //Se llama los temas que tiene el contenido
                $tema = Subcategory::where('company_id',$company->id)->where('status', 1)->get();
                //Recorre los temas(subcategorias) encontrado
                foreach ($tema as $subcategoria){
                    //carga el id nombre y megacategoria de cada subcategoria en variables mas faciles para el uso
                    $subcategoria_id=$subcategoria['id'];
                    $subcategoria_nombre=$subcategoria['name'];
                    $categoria_id=$contenidos->id;
                    $company_id=$subcategoria['company_id'];
                    //consulta los numeros de whatsApp asignados a la subcategoria
                    $contactos=NumberWhatsapp::where('subcategory_id','=',$subcategoria_id)->get();
                    //consulta las palabras que se deben de usar para clasificar los post en la subcategoria
                    $palabras=Compare::where('subcategoria_id','=',$subcategoria_id)
                        ->orderBy('prioridad', 'asc')
                        ->get();
                    //consulta si el tema/subcategoria debe de ser alertada o no
                    $alerta=Alert::where('subcategory_id','=',$subcategoria_id)->first();
                    $alerta??$alerta['notification']=0;
                    //recorre cada palabra para comparar en los post
                    foreach ($palabras as $palabra){
                        $comp=$palabra['palabra'];
                        $palabra_id=$palabra['id'];
                        $prioridad=$palabra['prioridad'];

                        //recorre cada post
                        foreach ($tweets as $tweet){
                            $content  = $tweet['content'];
                            $tweet_id = $tweet['id_tweet'];
                            $page_id  = $tweet['author_id'];
                            //$title=$post['title'];

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
                                            'company_id'      => $company->id
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
                                        case 3: $estado=0;
                                            break;
                                        case 2: $estado=2;
                                            //Se notifica al canal de la agencia
                                            // revisa si se debe de alertar o no
                                            if ($alerta['notification'] == 1) {
                                                $subcategoria_id = $subcategoria['id'];
                                                $encP = base64_encode($tweet_id);
                                                $encS = base64_encode($subcategoria_id);
                                                $encC = base64_encode($company_id);

                                                $token = env('TELEGRAM_TOKEN');
                                                $url = "https://api.telegram.org/bot$token/sendMessage";
                                                $canal = "-100";
                                                $canal_agencia = env('TELEGRAM_CANAL');
                                                try {
                                                    $telegram = Company::where('id', $company_id)->first();
                                                    $channel = $company->channel;
                                                    $findNum = stripos($channel, "-100");
                                                    if ($findNum === false) {
                                                        //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                                                        $canal = "-100";
                                                        $chat_id = $canal . $channel;
                                                    } else {
                                                        $chat_id = $channel;
                                                    }
                                                    $grupo_agencia = "@ADCR_BOT";

                                                    $url_app = env('APP_URL');
                                                    $data = array(
                                                        "chat_id" => $chat_id,
                                                        "text"    =>
                                                            "Hola! tengo la siguiente alerta, para la palabra: $comp y fue clasificada en: $subcategoria_nombre, ".
                                                            "acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id.".
                                                            " En el siguiente link lo podrás administrar => {$url_app}ManagementTweet/$encC/$encP/$encS"
                                                    );
                                                    $client = new client();
                                                    $res = $client->get($url, array(RequestOptions::JSON => $data));
                                                }catch (\exception $e){
                                                    //$phones = [$telegram['phone'], $telegram['phoneOptional']];
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
                                                $url = "https://api.telegram.org/bot$token/sendMessage";

                                                try {
                                                    $channel = $subcategoria['channel'];

                                                    $findNum = stripos($channel, "-100");
                                                    if ($findNum === false) {
                                                        //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                                                        $canal = "-100";
                                                        $chat_id = $canal . $channel;
                                                    } else {
                                                        $chat_id = $channel;
                                                    }
                                                    $data = array(
                                                        "chat_id" => $chat_id,
                                                        "text" => "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id");

                                                    $client = new client();
                                                    $res = $client->get($url, array(RequestOptions::JSON => $data));


                                                    //Envia mensaje por medio de Whatsapp
                                                    foreach ($contactos as $contactoArray) {
                                                        $contacto = $contactoArray['numeroTelefono'];
                                                        $message = "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://twitter.com/$tweet->username/status/$tweet_id";
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
        }
    }
}
