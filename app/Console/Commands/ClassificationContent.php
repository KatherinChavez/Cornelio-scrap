<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Company;
use App\Models\Scraps;
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

class ClassificationContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClassificationContent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clasificacion automatica de posteos de un contenido(categoria)';

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
        $end_time =  Carbon::now();


        // convierte las fechas para usarlas en consultas
        $start_time_for_query = Carbon::parse($start_time)->format('Y-m-d');
        $end_time_for_query = Carbon::parse($end_time)->addDay()->format('Y-m-d');

        // carga companies
        $companies=Company::where('status',1)->get();
        //recorre las companies
        foreach ($companies as $company){
            //se llama todos los contenidos de company
            $contenido = Category::where('company_id',$company->id)->get();
            $scraps=Scraps::select('page_id')->where('company_id',$company->id)->groupBy('page_id')->get();
            $scraps=$scraps->pluck('page_id');

            /*
             * obtiene las publicaciones de la tabla post y el titulo de los adjuntos de la tabla adjunto entre un rango de fechas
             * ademas de solo publicaciones de las paginas que tengo agrupadas a los contenidos de la company
             */

            $posts = Post::whereIn('posts.page_id',$scraps)
                ->with('attachment')
                ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->get();

            /*$posts=Post::
            whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->
                whereIn('posts.page_id',$scraps)
                ->join('attachments', 'attachments.post_id', '=', 'posts.post_id')
                ->select('posts.*','attachments.title')
                ->get();*/
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
                        foreach ($posts as $post){
                            $content=$post['content'];
                            //$title=$post['title'];
                            $title=isset($post->attachment) ? $post->attachment->title : '';
                            $post_id=$post['post_id'];
                            $page_id=$post['page_id'];

                            //busca la palabra dentro de la publicacion
                            $find = stripos($content, $comp);
                            // si se encuentra la palabra dentro de la publicacion
                            //if($find == True){
                            if($find == true || $find !== false ){
                                // se consulta si la publicacion ya se notifico con la palabra encontrada
                                $notificacion=Notification::where('post_id','=',$post_id)
                                    ->where('word','=',$palabra_id)
                                    ->where('subcategory_id','=',$subcategoria_id)
                                    ->first();

                                //si no se a notificado la publicacion con la palabra encontrada
                                if($notificacion==null){
                                    //consulta si ya se clasifico el post
                                    $clasificacion=Classification_Category::where('post_id','=',$post_id)
                                        ->where('subcategoria_id','=',$subcategoria_id)
                                        ->first();
                                    // si no se a clasificado se procede a clasificar
                                    if($clasificacion==null){
                                        Classification_Category::create(['page_id'=>$page_id,
                                            'post_id'=>$post_id,'subcategoria_id'=>$subcategoria_id,
                                            'megacategoria_id'=>$categoria_id,
                                            'company_id'=>$company->id
                                        ]);
                                    }

                                    $estado='0';
                                    /* segun la prioridad de la plabra encontrada se procede a
                                    0 => 3 no notificar por ningun medio
                                    1 => 2 notificar canal de telegram de la agencia
                                    2 => 1 notificar al canal respectivo de la palabra
                                    */
                                    switch ($prioridad){
                                        //No se notifica
                                        case 3: $estado=0;
                                            break;
                                        case 2: $estado=2;
                                            //Se notifica al canal de la agencia
                                            // revisa si se debe de alertar o no
                                            if ($alerta['notification'] == 1) {
                                                $subcategoria_id = $subcategoria['id'];
                                                $encP = base64_encode($post_id);
                                                $encS = base64_encode($subcategoria_id);
                                                $encC = base64_encode($company_id);

                                                $token = env('TELEGRAM_TOKEN');
                                                $url = "https://api.telegram.org/bot$token/sendMessage";
                                                $canal = "-100";
                                                $canal_agencia = env('TELEGRAM_CANAL');
                                                try {
                                                    $telegram = Company::where('id', $company_id)->first();
                                                    //$channel = $telegram['channel'];
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
                                                        "text" => "Hola! tengo la siguiente alerta, para la palabra: $comp y fue clasificada en: " . $subcategoria_nombre .
                                                            ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                            ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS"
                                                    );
                                                    $client = new client();
                                                    $res = $client->get($url, array(RequestOptions::JSON => $data));
                                                }catch (\exception $e){
                                                    //$phones = [$telegram['phone'], $telegram['phoneOptional']];
                                                    $phones = [$company->phone, $company->phoneOptional];
                                                    foreach ($phones as $contactoArray) {
                                                        $contacto=$contactoArray;
                                                        $message = "Hola! tengo la siguiente alerta, para la palabra: " . $comp . " y fue clasificada en: " . $subcategoria_nombre .
                                                            ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                            ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS";
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
                                                }catch (\exception $e){
                                                    $datos=array(
                                                        'link'=>"{$url_app}ManagementPost/$encC/$encP/$encS",
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
                                            $alerta['notification'] = 1;
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
                                                        "text" => "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id);

                                                    $client = new client();
                                                    $res = $client->get($url, array(RequestOptions::JSON => $data));


                                                    //Envia mensaje por medio de Whatsapp
                                                    foreach ($contactos as $contactoArray) {
                                                        $contacto = $contactoArray['numeroTelefono'];
                                                        $message = "Hola! tengo la siguiente alerta, relacionada con: " . $comp . ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id;
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
                                    Notification::create(['post_id'=>$post_id,'subcategory_id'=>$subcategoria_id,'word'=>$palabra_id,'status'=>$estado]);
                                }
                            }
                            else{
                                /*si no encuentra la palabra en el contenido de la publicacion procede a buscarla en el titulo del abjunto y el resto del procedimiento
                                es el mismo a q si hubiera encontrado la palabra en el contenido de la publicacion*/
                                $find2=stripos($title, $comp);
                                //if($find2==true){
                                if($find2 == true || $find2 !== false ){
                                    $notificacion=Notification::where('post_id','=',$post_id)
                                        ->where('word','=',$palabra_id)
                                        ->where('subcategory_id','=',$subcategoria_id)
                                        ->first();
                                    if($notificacion==null){
                                        $clasificacion=Classification_Category::where('post_id','=',$post_id)->where('subcategoria_id','=',$subcategoria_id)->first();
                                        if($clasificacion==null){
                                            Classification_Category::create([
                                                'page_id'=>$page_id,'post_id'=>$post_id,
                                                'subcategoria_id'=>$subcategoria_id,
                                                //'megacategoria_id'=>$megacategori_id,
                                                'megacategoria_id'=>$categoria_id,
                                                'company_id'=>$company->id
                                            ]);
                                        }
                                        $estado='0';
                                        //Notification::create(['post_id'=>$post_id,'subcategory_id'=>$subcategoria_id,'word'=>$palabra_id,'status'=>$estado]);

                                        switch ($prioridad){
                                            //Es baja y no
                                            case 3: $estado=0;
                                                break;
                                            case 2: $estado=2;
                                                try {
                                                    // revisa si se debe de alertar o no
                                                    if ($alerta['notification'] == 1) {
                                                        $encP = base64_encode($post_id);
                                                        $encS = base64_encode($subcategoria_id);
                                                        $encC = base64_encode($company_id);

                                                        $token = env('TELEGRAM_TOKEN');
                                                        $url = "https://api.telegram.org/bot$token/sendMessage";
                                                        $canal = "-100";
                                                        $canal_agencia = env('TELEGRAM_CANAL');

                                                        $telegram = Company::where('id', $company_id)->first();
                                                        //$channel = $telegram['channel'];
                                                        $channel = $company->channel;
                                                        $findNum = stripos($channel, "-100");
                                                        if ($findNum === false) {
                                                            //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                                                            $canal = "-100";
                                                            $chat_id = $canal . $channel;
                                                        } else {
                                                            $chat_id = $channel;
                                                        }

                                                        $url_app = env('APP_URL');

                                                        $data = array(
                                                            "chat_id" => $chat_id,
                                                            "text" => "Hola! tengo la siguiente alerta, para la palabra: " . $comp . " y fue clasificada en: " . $subcategoria_nombre .
                                                                ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                                ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS"
                                                        );
                                                        $client = new client();
                                                        $res = $client->get($url, array(RequestOptions::JSON => $data));
                                                    }
                                                }catch (\exception $e) {

                                                    //$phones = [$telegram['phone'], $telegram['phoneOptional']];
                                                    $phones = [$company->phone, $company->phoneOptional];
                                                    foreach ($phones as $contactoArray) {
                                                        $contacto=$contactoArray;
                                                        $message = "Hola! tengo la siguiente alerta, para la palabra: " . $comp . " y fue clasificada en: " . $subcategoria_nombre .
                                                            ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                            ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS";
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
                                                }catch (\exception $e){
                                                    $datos=array(
                                                        'link'=>"{$url_app}ManagementPost/$encC/$encP/$encS",
                                                    );
                                                    $email = $telegram['emailCompanies'];
                                                    $this->subject="Clasificación del tema de $subcategoria_nombre ";
                                                    Mail::send('Notification.Month', $datos, function ($message) use ($email) {
                                                        $message->to($email)->subject($this->subject);
                                                        $message->cc('hosmara@publicidadweb.cr')->subject($this->subject);
                                                    });
                                                    //continue;
                                                }


                                                break;

                                            case 1: $estado=1;
                                                try {
                                                    //Envia mensaje por medio de telegram
                                                    if($alerta['notification']==1){
                                                        $token = env('TELEGRAM_TOKEN');
                                                        $url   = "https://api.telegram.org/bot$token/sendMessage";

                                                        $channel = $subcategoria['channel'];
                                                        $findNum = stripos($channel, "-100");
                                                        if($findNum === false){
                                                            //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                                                            $canal="-100";
                                                            $chat_id =$canal.$channel;
                                                        }else{
                                                            $chat_id =$channel;
                                                        }

                                                        $data = array(
                                                            "chat_id" => $chat_id,
                                                            "text" => "Hola! tengo la siguiente alerta, relacionada con: ".$comp.", acá te dejo el link para que la veas: https://www.facebook.com/".$post_id
                                                        );

                                                        $client= new client();
                                                        $res = $client->get($url,array(RequestOptions::JSON=>$data));

                                                        //Envia mensaje por medio de Whatsapp
                                                        foreach ($contactos as $contactoArray){
                                                            //$contacto=$contactoArray['phone'];
                                                            $contacto=$contactoArray['numeroTelefono'];
                                                            $message = "Hola! tengo la siguiente alerta, relacionada con: ".$comp.", acá te dejo el link para que la veas: https://www.facebook.com/".$post_id;
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

                                                            //Envia mensaje normales
                                                            $key = $company->key;
                                                            if($key){
                                                                $this->sendSms($contacto, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                                            }
                                                            continue;
                                                        }
                                                    }
                                                } catch (\exception $e) {
                                                    return "La palabra no puede ser notificada";
                                                }
                                                break;
                                        }
                                        Notification::create(['post_id'=>$post_id,'subcategory_id'=>$subcategoria_id,'word'=>$palabra_id,'status'=>$estado]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function sendSms($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key)
    {
        $url = env('SERVER'). "/services/send.php";
        $postData = array(
            'number' => $number,
            'message' => $message,
            'key' => $key,
            'type' => "sms",
        );
        $this->sendRequest($url, $postData)["messages"];

    }
    function sendRequest($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        if ($httpCode == 200) {
            $json = json_decode($response, true);
            if ($json == false) {
                if (empty($response)) {
                    throw new Exception("Missing data in request. Please provide all the required information to send messages.");
                } else {
                    throw new Exception($response);
                }
            } else {
                if ($json["success"]) {
                    return $json["data"];
                } else {
                    throw new Exception($json["error"]["message"]);
                }
            }
        } else {
            throw new Exception("HTTP Error Code : {$httpCode}");
        }
    }

}
