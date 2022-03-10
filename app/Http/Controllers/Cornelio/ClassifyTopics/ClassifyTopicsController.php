<?php

namespace App\Http\Controllers\Cornelio\ClassifyTopics;

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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\CodeCoverage\TestFixture\C;
use Spipu\Html2Pdf\Tag\Html\Sub;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ClassifyTopicsController extends Controller
{
    public function index(Request $request)
    {
        $star_time = Carbon::now()->subDay(1);
        $end_time = Carbon::now();
        if($request->search){
            $classifications = Classification_Category::whereBetween('classification_category.created_at',[$star_time , $end_time])
                ->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id')
                ->join('posts', 'posts.post_id', 'classification_category.post_id')
                ->join('scraps', 'scraps.page_id', 'classification_category.page_id')
                ->where('scraps.page_name', 'like', '%' . $request->search . '%')
                ->orWhere('subcategory.name', 'like', '%' . $request->search . '%')
                ->orWhere('posts.content', 'like', '%' . $request->search . '%')
                ->orderBy('classification_category.created_at', 'DESC')
                ->select('posts.content', 'subcategory.name', 'classification_category.*')
                ->groupBy('classification_category.post_id')
                ->get();
        }
        else{
            $classifications = Classification_Category::whereBetween('classification_category.created_at',[$star_time , $end_time])
                ->with('page')
                ->where('classification_category.company_id', session('company_id'))
                ->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id')
                ->join('posts', 'posts.post_id', 'classification_category.post_id')
                ->orderBy('classification_category.created_at', 'DESC')
                ->select('posts.content', 'subcategory.name', 'classification_category.*')
                ->get();
        }
        $topics = Subcategory::where('company_id', session('company_id'))->pluck('name','id');
        return view('Cornelio.ClassifyTopics.index', compact('classifications', 'topics'));
    }

    public function classify(Request $request){
        $start = $request->start;
        $end = $request->end;
        $topics = $request->subcategory_id;
        $compain=Company::where('slug',session('company'))->first();
        $company_id=$compain->id;

        $start_time_for_query = Carbon::parse($start)->format('Y-m-d');
        $end_time_for_query = Carbon::parse($end)->addDay()->format('Y-m-d');

        $contenido = Category::where('company_id',$company_id)->get();

        $scraps=Scraps::select('page_id')->where('company_id',$company_id)->groupBy('page_id')->get();
        $scraps=$scraps->pluck('page_id');

        $posts=Post::whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
            ->whereIn('posts.page_id',$scraps)
            ->join('attachments', 'attachments.post_id', '=', 'posts.post_id')
            ->select('posts.*','attachments.title')
            ->get();

        foreach ($contenido as $contenidos) {
            $sub = Subcategory::where('id', $topics)->first();
            $subcategoria_id=$topics;
            $subcategoria_nombre = $sub->name;
            $categoria_id = $contenidos->id;

            //consulta los numeros de whatsApp asignados a la subcategoria
            $contactos = NumberWhatsapp::where('subcategory_id', '=', $topics)->get();
            //consulta las palabras que se deben de usar para clasificar los post en la subcategoria
            $palabras = Compare::where('subcategoria_id', '=', $topics)
                ->orderBy('prioridad', 'asc')
                ->get();

            //consulta si el tema/subcategoria debe de ser alertada o no
            $alerta = Alert::where('subcategory_id', '=', $topics)->first();
            $alerta??$alerta['notification'] = 0;
            //recorre cada palabra para comparar en los post
            foreach ($palabras as $palabra) {
                $comp = $palabra['palabra'];
                $palabra_id = $palabra['id'];
                $prioridad = $palabra['prioridad'];

                //recorre cada post
                foreach ($posts as $post) {
                    $content = $post['content'];
                    $title = $post['title'];
                    $post_id = $post['post_id'];
                    $page_id = $post['page_id'];

                    //busca la palabra dentro de la publicacion
                    $find = stripos($content, $comp);
                    // si se encuentra la palabra dentro de la publicacion
                    if ($find == true || $find !== false) {
                        // se consulta si la publicacion ya se notifico con la palabra encontrada
                        $notificacion = Notification::where('post_id', '=', $post_id)
                            ->where('word', '=', $palabra_id)
                            ->where('subcategory_id', '=', $topics)
                            ->first();
                        //si no se a notificado la publicacion con la palabra encontrada
                        if ($notificacion == null) {
                            //consulta si ya se clasifico el post
                            $clasificacion = Classification_Category::where('post_id', '=', $post_id)
                                ->where('subcategoria_id', '=', $topics)
                                ->first();
                            // si no se a clasificado se procede a clasificar
                            if ($clasificacion == null) {
                                Classification_Category::create(['page_id' => $page_id,
                                    'post_id' => $post_id, 'subcategoria_id' => $topics,
                                    'megacategoria_id' => $categoria_id,
                                    'company_id' => $company_id
                                ]);
                            }
                            $estado = '0';
                            /* segun la prioridad de la plabra encontrada se procede a
                            0 => 3 no notificar por ningun medio
                            1 => 2 notificar canal de telegram de la agencia
                            2 => 1 notificar al canal respectivo de la palabra
                            */
                            switch ($prioridad) {
                                //No se notifica
                                case 3:
                                    $estado = 0;
                                    break;
                                case 2:
                                    $estado = 2;
                                    //Se notifica al canal de la agencia
                                    // revisa si se debe de alertar o no
                                    if ($alerta['notification'] == 1) {
                                        $encP = base64_encode($post_id);
                                        $encS = base64_encode($topics);
                                        $encC = base64_encode($company_id);

                                        $token = env('TELEGRAM_TOKEN');
                                        $url = "https://api.telegram.org/bot$token/sendMessage";
                                        $canal = "-100";
                                        $canal_agencia = env('TELEGRAM_CANAL');
                                        try {
                                            $telegram = Company::where('id', $company_id)->first();
                                            //$channel = $telegram['channel'];
                                            $channel = $compain->channel;
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
                                                "text" => "Hola! tengo la siguiente alerta, para la palabra: $comp y fue clasificada en: " . $subcategoria_nombre .
                                                    ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                    ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS"
                                            );
                                            $client = new client();
                                            $res = $client->get($url, array(RequestOptions::JSON => $data));
                                        } catch (\exception $e) {
                                            //$phones = [$telegram['phone'], $telegram['phoneOptional']];
                                            $phones = [$compain->phone, $compain->phoneOptional];
                                            foreach ($phones as $contactoArray) {
                                                $contacto = $contactoArray;
                                                $message = "Hola! tengo la siguiente alerta, para la palabra: " . $comp . " y fue clasificada en: " . $subcategoria_nombre .
                                                    ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                    ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS";
                                                $data = [
                                                    'phone' => $contacto,
                                                    'body' => $message,
                                                ];
                                                $json = json_encode($data); // Encode data to JSON
                                                $url = env('WHA_API_URL') . env('WHA_API_TOKEN');
                                                $options = stream_context_create(['http' => [
                                                    'method' => 'POST',
                                                    'header' => 'Content-type: application/json',
                                                    'content' => $json
                                                ]
                                                ]);
                                                $result = file_get_contents($url, false, $options);
                                                continue;
                                            }
                                            //return"La palabra no puede ser notificada";
                                        } catch (\exception $e) {
                                            $datos = array(
                                                'link' => "{$url_app}ManagementPost/$encC/$encP/$encS",
                                            );
                                            $email = $telegram['emailCompanies'];
                                            $this->subject = "Clasificación del tema de $subcategoria_nombre ";
                                            Mail::send('Notification.Month', $datos, function ($message) use ($email) {
                                                $message->to($email)->subject($this->subject);
                                                $message->cc('hosmara@publicidadweb.cr')->subject($this->subject);
                                            });
                                            //continue;
                                        }

                                    }

                                    break;
                                case 1:
                                    $estado = 1;
                                    if ($alerta['notification'] == 1) {
                                        //Envia mensaje por medio de telegram
                                        $token = env('TELEGRAM_TOKEN');
                                        $url = "https://api.telegram.org/bot$token/sendMessage";

                                        try {
                                            $channel = $sub->channel;

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
                                                $url = env('WHA_API_URL') . env('WHA_API_TOKEN');
                                                $options = stream_context_create(['http' => [
                                                    'method' => 'POST',
                                                    'header' => 'Content-type: application/json',
                                                    'content' => $json
                                                ]
                                                ]);
                                                $result = file_get_contents($url, false, $options);

                                                //Envia mensajes normales
                                                $key = $compain->key;
                                                if ($key) {
                                                    //$this->sendSms($contacto, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                                }

                                                continue;
                                            }
                                        } catch (\exception $e) {
                                            return "La palabra no puede ser notificada";
                                        }
                                    }
                                    break;
                            }

                            //Se guarda la notificacion
                            Notification::create(['post_id' => $post_id, 'subcategory_id' => $subcategoria_id, 'word' => $palabra_id, 'status' => $estado]);
                        }

                    }
                    else {
                        /*si no encuentra la palabra en el contenido de la publicacion procede a buscarla en el titulo del abjunto y el resto del procedimiento
                        es el mismo a q si hubiera encontrado la palabra en el contenido de la publicacion*/
                        $find2 = stripos($title, $comp);
                        if ($find2 == true || $find2 !== false) {
                            $notificacion = Notification::where('post_id', '=', $post_id)
                                ->where('word', '=', $palabra_id)
                                ->where('subcategory_id', '=', $subcategoria_id)
                                ->first();
                            if ($notificacion == null) {
                                $clasificacion = Classification_Category::where('post_id', '=', $post_id)->where('subcategoria_id', '=', $subcategoria_id)->first();
                                if ($clasificacion == null) {
                                    Classification_Category::create([
                                        'page_id' => $page_id, 'post_id' => $post_id,
                                        'subcategoria_id' => $subcategoria_id,
                                        //'megacategoria_id'=>$megacategori_id,
                                        'megacategoria_id' => $categoria_id,
                                        'company_id' => $company_id
                                    ]);
                                }
                                $estado = '0';
                                //Notification::create(['post_id'=>$post_id,'subcategory_id'=>$subcategoria_id,'word'=>$palabra_id,'status'=>$estado]);

                                switch ($prioridad) {
                                    //Es baja y no
                                    case 3:
                                        $estado = 0;
                                        break;
                                    case 2:
                                        $estado = 2;
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
                                                $channel = $compain->channel;
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
                                        } catch (\exception $e) {

                                            //$phones = [$telegram['phone'], $telegram['phoneOptional']];
                                            $phones = [$compain->phone, $compain->phoneOptional];
                                            foreach ($phones as $contactoArray) {
                                                $contacto = $contactoArray;
                                                $message = "Hola! tengo la siguiente alerta, para la palabra: " . $comp . " y fue clasificada en: " . $subcategoria_nombre .
                                                    ", acá te dejo el link para que la veas: https://www.facebook.com/" . $post_id .
                                                    ". En el siguiente link lo podrás administrar => {$url_app}ManagementPost/$encC/$encP/$encS";
                                                $data = [
                                                    'phone' => $contacto,
                                                    'body' => $message,
                                                ];
                                                $json = json_encode($data); // Encode data to JSON
                                                $url = env('WHA_API_URL') . env('WHA_API_TOKEN');
                                                $options = stream_context_create(['http' => [
                                                    'method' => 'POST',
                                                    'header' => 'Content-type: application/json',
                                                    'content' => $json
                                                ]
                                                ]);
                                                $result = file_get_contents($url, false, $options);
                                                continue;
                                            }
                                        } catch (\exception $e) {
                                            $datos = array(
                                                'link' => "{$url_app}ManagementPost/$encC/$encP/$encS",
                                            );
                                            $email = $telegram['emailCompanies'];
                                            $this->subject = "Clasificación del tema de $subcategoria_nombre ";
                                            Mail::send('Notification.Month', $datos, function ($message) use ($email) {
                                                $message->to($email)->subject($this->subject);
                                                $message->cc('hosmara@publicidadweb.cr')->subject($this->subject);
                                            });
                                            //continue;
                                        }


                                        break;
                                    case 1:
                                        $estado = 1;
                                        $alerta['notification'] = 1;
                                        if ($alerta['notification'] == 1) {


                                            //Envia mensaje por medio de telegram
                                            $token = env('TELEGRAM_TOKEN');
                                            $url = "https://api.telegram.org/bot$token/sendMessage";

                                            try {
                                                $channel = $sub->channel;

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
                                                    $url = env('WHA_API_URL') . env('WHA_API_TOKEN');
                                                    $options = stream_context_create(['http' => [
                                                        'method' => 'POST',
                                                        'header' => 'Content-type: application/json',
                                                        'content' => $json
                                                    ]
                                                    ]);
                                                    $result = file_get_contents($url, false, $options);

                                                    //Envia mensajes normales
                                                    $key = $compain->key;
                                                    if ($key) {
                                                        //$this->sendSms($contacto, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                                    }

                                                    continue;
                                                }
                                            } catch (\exception $e) {
                                                return "La palabra no puede ser notificada";
                                            }
                                        }
                                        break;
                                }
                                Notification::create(['post_id' => $post_id, 'subcategory_id' => $subcategoria_id, 'word' => $palabra_id, 'status' => $estado]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function edit(Request $request)
    {
        $classification = Classification_Category::where('classification_category.id', $request->id_classification)
                ->join('scraps', 'scraps.page_id', 'classification_category.page_id')
                ->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id')
                ->join('posts', 'posts.post_id', 'classification_category.post_id')
                ->get();
        return $classification;
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_classification' => 'required',
            'subcategory' => 'required',
        ]);

        $classification = Classification_Category::where('id', $request->id_classification)->first();
        $classification->update([
            'subcategoria_id' => $request->subcategory,
        ]);
        return redirect()->route('ClassifyTopics.index')->with('info','Se ha actualizado exitosamente');
    }

    public function destroy(Classification_Category $classification_Category)
    {
        $classification_Category->delete();
        return redirect()->route('ClassifyTopics.index')->with('info', 'Eliminada correctamente');
    }
}
