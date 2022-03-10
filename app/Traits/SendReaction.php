<?php


namespace App\Traits;

use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\BitacoraPDF;
use App\Models\Category;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Subcategory;
use App\Models\TopReaction;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Ilovepdf\Ilovepdf;


trait SendReaction
{
    use TopicCountTrait;
    use SendSmsTrait;

    public function topReaction($report, $saludo, $infoMessage)
    {
        $start_time =  Carbon::now()->subDays(1);
        $end_time   =  Carbon::now()->addDay(1);

        //Top 10 de publicaciones con mas interacciones
        $data    = array();
        $topData = array();

        /*$reacciones = Reaction::whereBetween('created_time',[$start_time , $end_time])
            ->join('posts', 'posts.post_id', 'reaction_classifications.post_id')
            ->orderBy('reaction_classifications.created_at', 'DESC')
            ->get();*/
        $reacciones = Post::whereBetween('created_time',[$start_time , $end_time])->orderBy('created_at', 'DESC')->get();

        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys = array_column($data,'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);
        $i=0;

        foreach ($resultado as $post){
            $info = Post::where('post_id', $post['posteo'])
                ->with(['attachment', 'classification_category' => function ($q) {
                    $q->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id');}])
                ->first();
            if($info){
                $topData[$i]['posicion']=$i+ 1;
                $topData[$i]['date']=$info->created_time;
                $topData[$i]['name']=$info->page_name;
                $topData[$i]['content']=$info->content;
                $topData[$i]['reaction']=$post['reacction'];
                $topData[$i]['count']=$post['count'];

                if(isset($info['attachment']['picture'])){
                    $topData[$i]['attachment']=$info['attachment']['picture'];
                }
                if(isset($info['attachment']['title'])){
                    $topData[$i]['title']=$info['attachment']['title'];
                }
                if(isset($info['attachment']['url'])){
                    $topData[$i]['url']=$info['attachment']['url'];
                }
                if(isset($info['classification_category'])){
                    $topData[$i]['company']=$info['classification_category']['company_id'];
                    $topData[$i]['classification_post']=$info['classification_category']['post_id'];
                    $topData[$i]['subcategory']=$info['classification_category']['name'];
                    $topData[$i]['subcategoria_id']=$info['classification_category']['subcategoria_id'];
                    $topData[$i]['classification']='Clasificado';
                }
                $i++;
            }
            else{
                $topData=[];
                continue;
            }
        }
        //$pdf = \PDF::loadView('PDF.pdf', compact('topData'));
        //return $pdf->stream();

        //CREA EL PDF
        $content  = PDF::loadView('PDF.pdf', compact('topData'))->output();
        $fileName = 'Top-'.Carbon::now()->format('Y-m-d').'_hora_'.Carbon::now()->format('H-i').'.pdf';
        $file     = Storage::disk('public_files')->put($fileName, $content);
        $path     = Storage::disk('public_files')->path($fileName);

        //COMPRIME EL PDF
        $ilovepdf       = new Ilovepdf(env('ILOVEPDF_PROJECT_KEY'), env('ILOVEPDF_SECRET_KEY'));
        $myTaskCompress = $ilovepdf->newTask('compress');
        $myTaskCompress->addFile($path);
        $myTaskCompress->setOutputFilename($fileName);
        $myTaskCompress->execute();
        //$myTaskCompress->download("../public/whatsapp_filesCompress");       //LOCAL
        $myTaskCompress->download("/home/monitoreocornel/public_html/whatsapp_filesCompress"); //PRODUCCION

        //$fileName = "Top-2022-02-01_hora_12-32.pdf";

        //Se envia el pdf a los numeros de telefono que han seleccionado recibir el reporte respectivo
        $phones = NumberWhatsapp::where('subcategory_id', null)->where('report', $report)->get();
        //$phones = NumberWhatsapp::whereIn('id', [72, 146])->get();

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach ($phones as $contactoArray) {
            $comp = Company::where('id', $contactoArray->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if ($comp) {
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $contacto = $contactoArray->numeroTelefono;
            $group_id = $contactoArray->group_id;
            $name = $this->eliminar_acentos(str_replace(' ', '+', $contactoArray->descripcion));
            $mensaje = $saludo . $name . $infoMessage;
            $mensajePdf = env('APP_URL') . "whatsapp_filesCompress/$fileName";

            BitacoraMessageFb::create([
                'type' => $contacto != 0 ? 'phone' : 'group',
                'number' => $contacto != 0 ? $contacto : $group_id,
                'typeMessage' => 'text',
                'report' => $report,
                'message' => $mensaje,
                'status' => 0,
            ]);

            BitacoraMessageFb::create([
                'type' => $contacto != 0 ? 'phone' : 'group',
                'number' => $contacto != 0 ? $contacto : $group_id,
                'typeMessage' => 'file',
                'report' => $report,
                'message' => $mensajePdf,
                'status' => 0,

            ]);
        }

        //Se almacena luego de enviar los datos
        foreach ($topData as $value) {
            $company = '';
            $classification = '';
            if (isset($value['company'])) {
                $company = $value['company'];
            }
            if (isset($value['subcategoria_id'])) {
                $classification = $value['subcategoria_id'];
            }

            \App\Models\TopReaction::create([
                'position'       => $value['posicion'],
                'post_id'        => $value['reaction']->post_id,
                'page_id'        => $value['reaction']->page_id,
                'page_name'      => $value['name'],
                'company'        => $company,
                'content'        => $value['content'],
                'classification' => $classification,
                'likes'          => $value['reaction']->likes,
                'love'           => $value['reaction']->love,
                'haha'           => $value['reaction']->haha,
                'sad'            => $value['reaction']->sad,
                'wow'            => $value['reaction']->wow,
                'angry'          => $value['reaction']->angry,
                'shared'         => $value['reaction']->shared,
                'count'          => $value['count'],
                'date'           => $value['date'],
                'fileName'       => $fileName,
            ]);
        }

        BitacoraPDF::create([
            'file'       => $fileName,
            'url'        => env('APP_URL') . 'whatsapp_filesCompress/' . $fileName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function topAnalysis($report, $numbers)
    {
        //Ultimos minutos
        $last = Carbon::now()->subMinute(30);

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = \App\Models\TopReaction::whereBetween('created_at',[$last, Carbon::now()])->take(10)->get();
        $i = 0;

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach ($numbers as $number) {
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if ($comp) {
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $info = [];
            $arrayId = [];
            $arrayName = [];
            $arrayPosicion = [];

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $companie_top = $number->company_id;
            $companie_n = Company::where('id', $number->company_id)->first();
            $companie_name = str_replace('Prensa', '', $companie_n->nombre);
            $name = $this->eliminar_acentos(str_replace(' ', '+', $companie_name));

            $header = array('http' => array('method' => "POST",));
            $context = stream_context_create($header);

            foreach ($data_pdf as $pdf) {
                $companie_pdf = $pdf->company;
                if ($companie_top == $companie_pdf) {
                    $tema = Subcategory::where('id', $pdf->classification)->first();
                    $info[$i]['posicion'] = $pdf->position;
                    $info[$i]['tema'] = $tema->name;
                    $info[$i]['tema_id'] = $pdf->classification;
                    $info[$i]['companie'] = $tema->company_id;
                    array_push($arrayId, $pdf->classification);
                    array_push($arrayName, $tema->name);
                    array_push($arrayPosicion, $pdf->position);
                    $i++;
                }
            }

            try {
                if ($info != []) {
                    $url_app = env("APP_URL");
                    $uniqueId = array_unique($arrayId);
                    $unique = array_unique($arrayName);

                    $nameT = implode("%2C+", $unique);
                    $positionTopics = implode("%2C+", $arrayPosicion);
                    $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT));

                    if ($i >= 1) {
                        if (count(array_unique($unique)) == 1 && count(array_unique($uniqueId)) == 1) {
                            /***************************  ENVIO DE LA INFORMACION *****************************************/
                            $message = "El+tema+$nameTopics+figura+en+el+Top+10+en+la+posici%C3%B3n+$positionTopics.";
                            BitacoraMessageFb::create([
                                'type' => $contacto != 0 ? 'phone' : 'group',
                                'typeMessage' => 'text',
                                'number' => $contacto != 0 ? $contacto : $group_id,
                                'report' => $report,
                                'message' => $message,
                                'status' => 0,
                            ]);


                            /******************************  ENVIO DEL LINK ***********************************************/
                            foreach ($uniqueId as $uniqueLInk) {
                                $id_encry = base64_encode($uniqueLInk);
                                $link = "{$url_app}analysisLink/{$id_encry}/";
                                $messageLink = "En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+$nameTopics+$link";

                                BitacoraMessageFb::create([
                                    'type' => $contacto != 0 ? 'phone' : 'group',
                                    'typeMessage' => 'text',
                                    'number' => $contacto != 0 ? $contacto : $group_id,
                                    'report' => $report,
                                    'message' => $messageLink,
                                    'status' => 0,
                                ]);

                            }
                        } else {
                            foreach ($uniqueId as $infoPosicion) {
                                $id_encry = base64_encode($infoPosicion);
                                $nameT = Subcategory::where('id', $infoPosicion)->first();
                                $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT->name));
                                $consulta = \App\Models\TopReaction::where('classification', $infoPosicion)->whereBetween('created_at', [$last, Carbon::now()])->pluck('position')->take(10)->toArray();
                                $positionTopics = implode(',', $consulta);
                                $message = "El+tema+$nameTopics+figura+en+el+Top+10+en+la+posici%C3%B3n+$positionTopics.";

                                BitacoraMessageFb::create([
                                    'type' => $contacto != 0 ? 'phone' : 'group',
                                    'typeMessage' => 'text',
                                    'number' => $contacto != 0 ? $contacto : $group_id,
                                    'report' => $report,
                                    'message' => $message,
                                    'status' => 0,
                                ]);


                                /***************************  ENVIO DEL LINK **********************************************/
                                $link = "{$url_app}analysisLink/{$id_encry}/";
                                $messageLink = "En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+$nameTopics.+$link";
                                BitacoraMessageFb::create([
                                    'type' => $contacto != 0 ? 'phone' : 'group',
                                    'typeMessage' => 'text',
                                    'number' => $contacto != 0 ? $contacto : $group_id,
                                    'report' => $report,
                                    'message' => $messageLink,
                                    'status' => 0,
                                ]);

                            }
                        }
                    } else {
                        /***************************  ENVIO DE LA INFORMACION *************************************/
                        $message = "No+se+identifica+ning%C3%BAn+tema+de+$name+dentro+del+top10+nacional";
                        BitacoraMessageFb::create([
                            'type' => $contacto != 0 ? 'phone' : 'group',
                            'typeMessage' => 'text',
                            'number' => $contacto != 0 ? $contacto : $group_id,
                            'report' => $report,
                            'message' => $message,
                            'status' => 0,
                        ]);
                    }
                } else {
                    $message = "No+se+identifica+ning%C3%BAn+tema+de+$name+dentro+del+top10+nacional";
                    BitacoraMessageFb::create([
                        'type' => $contacto != 0 ? 'phone' : 'group',
                        'typeMessage' => 'text',
                        'number' => $contacto != 0 ? $contacto : $group_id,
                        'report' => $report,
                        'message' => $message,
                        'status' => 0,
                    ]);
                }
            } catch (\Exception $e) {
                BitacoraMessageFb::create([
                    'type' => $contacto != 0 ? 'phone' : 'group',
                    'typeMessage' => 'text',
                    'number' => $contacto != 0 ? $contacto : $group_id,
                    'report' => $report,
                    'message' => $e,
                    'error' => $e,
                ]);
                continue;
            }
        }
    }

    public function topComparator($report, $numbers){
        //Ultimos minutos
        $last = Carbon::now()->subMinute(30);

        //Numero de telefono
        //$numbers = NumberWhatsapp::where('report', $report)->get();

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = TopReaction::whereBetween('created_at',[$last, Carbon::now()])->take(10)->get();

        $i             = 0;
        $info          = [];
        $arrayId       = [];
        $arrayName     = [];
        $arrayPosicion = [];

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach($numbers as $number) {
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if ($comp) {
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $companie_top = $number->company_id;
            $companie_name = Company::where('id', $number->company_id)->first();

            foreach ($data_pdf as $pdf) {
                $companie_pdf = $pdf->company;
                if ($companie_top == $companie_pdf) {
                    $tema = Subcategory::where('id', $pdf->classification)->first();
                    if ($pdf->position <= 5) {
                        $info[$i]['posicion'] = $pdf->position;
                        $info[$i]['tema'] = $tema->name;
                        $info[$i]['tema_id'] = $pdf->classification;

                        array_push($arrayId, $pdf->classification);
                        array_push($arrayName, $tema->name);
                        array_push($arrayPosicion, $pdf->position);
                        $i++;
                    }
                }
            }
            try {
                if ($info != [] || $info != null) {
                    $uniqueId = array_unique($arrayId);
                    $uniqueName = array_unique($arrayName);
                    $url_app = env("APP_URL");
                    $start = base64_encode(Carbon::now()->subHour(72));
                    $end = base64_encode(Carbon::now());

                    if (count(array_unique($uniqueId)) == 1 && count(array_unique($uniqueName)) == 1) {
                        $idTopics = implode("", $uniqueId);
                        $nameT = implode("", $uniqueName);
                        $name = $this->eliminar_acentos(str_replace(' ', '+', $nameT));
                        $sub = base64_encode($idTopics);
                        $link = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                        $message = "En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+$name.+$link.";

                        BitacoraMessageFb::create([
                            'type' => $contacto != 0 ? 'phone' : 'group',
                            'typeMessage' => 'text',
                            'number' => $contacto != 0 ? $contacto : $group_id,
                            'report' => $report,
                            'message' => $message,
                            'status' => 0,
                        ]);
                    } else {
                        foreach ($uniqueId as $infoPosicion) {
                            $sub = base64_encode($infoPosicion);
                            $nameTopics = Subcategory::where('id', $infoPosicion)->first();
                            $name = $this->eliminar_acentos(str_replace(' ', '', $nameTopics->name));
                            $link = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                            $message = "En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+$name+$link";

                            BitacoraMessageFb::create([
                                'type' => $contacto != 0 ? 'phone' : 'group',
                                'typeMessage' => 'text',
                                'number' => $contacto != 0 ? $contacto : $group_id,
                                'report' => $report,
                                'message' => $message,
                                'status' => 0,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                BitacoraMessageFb::create([
                    'type' => $contacto != 0 ? 'phone' : 'group',
                    'typeMessage' => 'text',
                    'number' => $contacto != 0 ? $contacto : $group_id,
                    'report' => $report,
                    'message' => $message,
                    'error' => $e,
                ]);
                continue;
            }
        }
    }

    public function topBubles($report, $numbers){
        foreach ($numbers as $number) {
            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $name = '';
            $contents_encode = base64_encode("[]");
            $validation = ($number->content == "[]" ? null : $number->content);
            if ($validation != "null" && $validation) {
                $contents_encode = base64_encode($number->content);
                $contents_id = json_decode($number->content);
                $getContent = Category::whereIn('id', $contents_id)->pluck('name');
                $nameContent = json_decode($getContent);
                $nameC = implode(", ", $nameContent);
                $name = $this->eliminar_acentos(str_replace(' ', '+', $nameC));
            }

            $nameUser = $this->eliminar_acentos(str_replace(' ', '+', $number->descripcion));
            $company = $number->company_id;
            $url_app = env("APP_URL");
            $comp = base64_encode($company);
            $link = "{$url_app}BublesContent/{$comp}/{$contents_encode}";
            $message = "Hola+$nameUser%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+$name.+$link+";
            try {
                BitacoraMessageFb::create([
                    'type' => $contacto != 0 ? 'phone' : 'group',
                    'typeMessage' => 'text',
                    'number' => $contacto != 0 ? $contacto : $group_id,
                    'report' => $report,
                    'message' => $message,
                    'status' => 0,
                ]);
            } catch (\Exception $e) {
                BitacoraMessageFb::create([
                    'type' => $contacto != 0 ? 'phone' : 'group',
                    'typeMessage' => 'text',
                    'number' => $contacto != 0 ? $contacto : $group_id,
                    'report' => $report,
                    'message' => $message,
                    'error' => $e,
                ]);
                continue;
            }
        }
    }

    public function topReaction_V2($report, $saludo, $infoMessage)
    {
        $start_time =  Carbon::now()->subDays(1);
        $end_time   =  Carbon::now()->addDay(1);

        //Top 10 de publicaciones con mas interacciones
        $data    = array();
        $topData = array();

        $reacciones = Reaction::whereBetween('created_time',[$start_time , $end_time])
            ->join('posts', 'posts.post_id', 'reaction_classifications.post_id')
            ->orderBy('reaction_classifications.created_at', 'DESC')
            ->get();

        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys = array_column($data,'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);
        $i=0;

        foreach ($resultado as $post){
            $info = Post::where('post_id', $post['posteo'])
                ->with(['attachment', 'classification_category' => function ($q) {
                    $q->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id');}])
                ->first();
            if($info){
                $topData[$i]['posicion']=$i+ 1;
                $topData[$i]['date']=$info->created_time;
                $topData[$i]['name']=$info->page_name;
                $topData[$i]['content']=$info->content;
                $topData[$i]['reaction']=$post['reacction'];
                $topData[$i]['count']=$post['count'];

                if(isset($info['attachment']['picture'])){
                    $topData[$i]['attachment']=$info['attachment']['picture'];
                }
                if(isset($info['attachment']['title'])){
                    $topData[$i]['title']=$info['attachment']['title'];
                }
                if(isset($info['attachment']['url'])){
                    $topData[$i]['url']=$info['attachment']['url'];
                }
                if(isset($info['classification_category'])){
                    $topData[$i]['company']=$info['classification_category']['company_id'];
                    $topData[$i]['classification_post']=$info['classification_category']['post_id'];
                    $topData[$i]['subcategory']=$info['classification_category']['name'];
                    $topData[$i]['subcategoria_id']=$info['classification_category']['subcategoria_id'];
                    $topData[$i]['classification']='Clasificado';
                }
                $i++;
            }
            else{
                $topData=[];
                continue;
            }
        }
        //$pdf = \PDF::loadView('PDF.pdf', compact('topData'));
        //return $pdf->stream();

        //CREA EL PDF
        $content  = PDF::loadView('PDF.pdf', compact('topData'))->output();
        $fileName = 'Top-'.Carbon::now()->format('Y-m-d').'_hora_'.Carbon::now()->format('H-i').'.pdf';
        $file     = Storage::disk('public_files')->put($fileName, $content);
        $path     = Storage::disk('public_files')->path($fileName);

        //COMPRIME EL PDF
        $ilovepdf       = new Ilovepdf(env('ILOVEPDF_PROJECT_KEY'), env('ILOVEPDF_SECRET_KEY'));
        $myTaskCompress = $ilovepdf->newTask('compress');
        $myTaskCompress->addFile($path);
        $myTaskCompress->setOutputFilename($fileName);
        $myTaskCompress->execute();
        //$myTaskCompress->download("../public/whatsapp_filesCompress");       //LOCAL
        $myTaskCompress->download("/home/monitoreocornel/public_html/whatsapp_filesCompress"); //PRODUCCION

        //Se envia el pdf a los numeros de telefono que han seleccionado recibir el reporte respectivo
        $phones = NumberWhatsapp::where('subcategory_id', null)->where('report', $report)->get();

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach ($phones as $contactoArray) {
            $comp = Company::where('id', $contactoArray->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance  = $comp->instance;
            }

            $contacto   = $contactoArray->numeroTelefono;
            $group_id   = $contactoArray->group_id;
            $name       = $this->eliminar_acentos(str_replace(' ', '+', $contactoArray->descripcion));
            $mensaje    = $saludo.$name.$infoMessage;
            $mensajePdf = env('APP_URL')."whatsapp_filesCompress/$fileName";
            $header     = array('http'=>array('method'=>"POST",));
            $context    = stream_context_create($header);

            try {
                if ($contacto || $contacto != 0) {
                    $urlBody  = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$mensaje&type=text";
                    $urlPdf   = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$mensajePdf&type=image";
                }
                elseif ($group_id || $group_id != 0) {
                    $urlBody  = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
                    $urlPdf   = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensajePdf&type=file";
                }

                //ENVIO DE MENSAJE
                $sendBody = curl_init();
                curl_setopt_array($sendBody, array(
                    CURLOPT_URL => $urlBody,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $resultB = curl_exec($sendBody);
                curl_close($sendBody);

                //ENVIO DE PDF
                $sendPdf = curl_init();
                curl_setopt_array($sendPdf, array(
                    CURLOPT_URL => $urlPdf,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $resultP = curl_exec($sendPdf);
                curl_close($sendPdf);

                //$resultB = file_get_contents($urlBody, false, $context);
                //$resultP = file_get_contents($urlPdf, false, $context);
                $dataB   = json_decode($resultB);
                $dataP   = json_decode($resultP);

                if(isset($dataB)){
                    if ($dataB->status == false) {
                        $client_id = env('CLIENT_ID');
                        $instance  = env('INSTANCE');
                        if ($contacto || $contacto != 0) {
                            $urlBody  = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$mensaje&type=text";
                        } elseif ($group_id || $group_id != 0) {
                            $urlBody  = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
                        }
                        //ENVIO DE MENSAJE
                        $sendBody = curl_init();
                        curl_setopt_array($sendBody, array(
                            CURLOPT_URL => $urlBody,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $resultB = curl_exec($sendBody);
                        curl_close($sendBody);
                        //$resultB = file_get_contents($urlBody, false, $context);
                        $dataB   = json_decode($resultB);
                        if(isset($dataB)){
                            if($dataB->status == false){
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                                    'typeMessage' => 'text',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $mensaje,
                                    'error'       => $dataB->message,
                                    'status'      => 0,
                                ]);
                            }
                            else if ($dataB->status == true){
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                                    'typeMessage' => 'text',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $mensaje,
                                    'error'       => $resultB,
                                    'status'      => 1
                                ]);
                            }
                        }
                        else{
                            BitacoraMessageFb::create([
                                'type'        => $contacto != 0 ? 'phone'   : 'group',
                                'typeMessage' => 'text',
                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                'report'      => $report,
                                'message'     => $mensaje,
                                'error'       => $dataB->message,
                            ]);
                        }
                    }
                    else if ($dataB->status == true){
                        BitacoraMessageFb::create([
                            'type'        => $contacto != 0 ? 'phone'   : 'group',
                            'typeMessage' => 'text',
                            'number'      => $contacto != 0 ? $contacto : $group_id,
                            'report'      => $report,
                            'message'     => $mensaje,
                            'error'       => $resultB,
                            'status'      => 99
                        ]);
                    }
                }
                else{
                    BitacoraMessageFb::create([
                        'type'        => $contacto != 0 ? 'phone'   : 'group',
                        'typeMessage' => 'text',
                        'number'      => $contacto != 0 ? $contacto : $group_id,
                        'report'      => $report,
                        'message'     => $mensaje,
                        'error'       => $resultB,
                        'status'      => 0
                    ]);
                }

                if(isset($dataP)){
                    if ($dataP->status == false) {
                        $client_id = env('CLIENT_ID');
                        $instance  = env('INSTANCE');
                        if ($contacto || $contacto != 0) {
                            $urlPdf   = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$mensajePdf&type=image";
                        } elseif ($group_id || $group_id != 0) {
                            $urlPdf   = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensajePdf&type=file";
                        }
                        //ENVIO DE PDF
                        $sendPdf = curl_init();
                        curl_setopt_array($sendPdf, array(
                            CURLOPT_URL => $urlPdf,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $resultP = curl_exec($sendPdf);
                        curl_close($sendPdf);
                        //$resultP = file_get_contents($urlPdf, false, $context);
                        $dataP   = json_decode($resultP);
                        if(isset($dataP)){
                            if($dataP->status == false){
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                                    'typeMessage' => 'file',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $mensajePdf,
                                    'error'       => $dataP->message,
                                ]);
                            }
                        }
                        else{
                            BitacoraMessageFb::create([
                                'type'    => $contacto != 0 ? 'phone'   : 'group',
                                'number'  => $contacto != 0 ? $contacto : $group_id,
                                'report'  => $report,
                                'message' => $mensajePdf,
                                'error'   => $resultP,
                            ]);
                        }
                        continue;
                    }
                }
                else{
                    BitacoraMessageFb::create([
                        'type'        => $contacto != 0 ? 'phone'   : 'group',
                        'typeMessage' => 'file',
                        'number'      => $contacto != 0 ? $contacto : $group_id,
                        'report'      => $report,
                        'message'     => $mensajePdf,
                        'error'       => $resultP,
                    ]);
                }
            }catch (\Exception $e){
                BitacoraMessageFb::create([
                    'type'    => $contacto != 0 ? 'phone'   : 'group',
                    'number'  => $contacto != 0 ? $contacto : $group_id,
                    'report'  => $report,
                    'message' => $mensaje ,
                    'error'   => $e,
                ]);
                continue;
            }
            sleep(10);
        }

        //Se almacena luego de enviar los datos
        foreach ($topData as $value){
            $company = '';
            $classification = '';
            if(isset($value['company'])){
                $company = $value['company'];
            }
            if(isset($value['subcategoria_id'])){
                $classification = $value['subcategoria_id'];
            }

            \App\Models\TopReaction::create([
                'position'       => $value['posicion'],
                'post_id'        => $value['reaction']->post_id,
                'page_id'        => $value['reaction']->page_id,
                'page_name'      => $value['name'],
                'company'        => $company,
                'content'        => $value['content'],
                'classification' => $classification,
                'likes'          => $value['reaction']->likes,
                'love'           => $value['reaction']->love,
                'haha'           => $value['reaction']->haha,
                'sad'            => $value['reaction']->sad,
                'wow'            => $value['reaction']->wow,
                'angry'          => $value['reaction']->angry,
                'shared'         => $value['reaction']->shared,
                'count'          => $value['count'],
                'date'           => $value['date'],
                'fileName'       => $fileName,
            ]);
        }

        BitacoraPDF::create([
            'file'       => $fileName,
            'url'        => env('APP_URL').'whatsapp_filesCompress/'.$fileName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function topAnalysis_V2($report, $numbers)
    {
        //Ultimos minutos
        $last = Carbon::now()->subMinute(10);
        //$last = Carbon::now()->subHour(5);

        //Numero de telefono
        //$numbers = NumberWhatsapp::where('report', $report)->get();

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = \App\Models\TopReaction::whereBetween('created_at',[$last, Carbon::now()])->take(10)->get();
        $i = 0;

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach ($numbers as $number) {
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance  = $comp->instance;
            }

            $info          = [];
            $arrayId       = [];
            $arrayName     = [];
            $arrayPosicion = [];

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $companie_top  = $number->company_id;
            $companie_n    = Company::where('id', $number->company_id)->first();
            $companie_name = str_replace('Prensa', '', $companie_n->nombre);
            $name = $this->eliminar_acentos(str_replace(' ', '+', $companie_name));

            $header  = array('http'=>array('method'=>"POST",));
            $context = stream_context_create($header);

            foreach ($data_pdf as $pdf) {
                $companie_pdf = $pdf->company;
                if ($companie_top == $companie_pdf) {
                    $tema                 = Subcategory::where('id', $pdf->classification)->first();
                    $info[$i]['posicion'] = $pdf->position;
                    $info[$i]['tema']     = $tema->name;
                    $info[$i]['tema_id']  = $pdf->classification;
                    $info[$i]['companie'] = $tema->company_id;
                    array_push($arrayId,       $pdf->classification);
                    array_push($arrayName,     $tema->name);
                    array_push($arrayPosicion, $pdf->position);
                    $i++;
                }
            }

            try {
                if ($info != []) {
                    $url_app = env("APP_URL");
                    $uniqueId = array_unique($arrayId);
                    $unique = array_unique($arrayName);

                    $nameT          = implode("%2C+", $unique);
                    $positionTopics = implode("%2C+", $arrayPosicion);
                    $nameTopics     = $this->eliminar_acentos(str_replace(' ', '+', $nameT));

                    if ($i >= 1) {
                        if (count(array_unique($unique)) == 1 && count(array_unique($uniqueId)) == 1) {
                            /***************************  ENVIO DE LA INFORMACION *****************************************/
                            $message = "El+tema+$nameTopics+figura+en+el+Top+10+en+la+posici%C3%B3n+$positionTopics.";
                            if ($contacto || $contacto != 0) {
                                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                            } elseif ($group_id || $group_id != 0) {
                                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                            }
                            //ENVIO DE MENSAJE
                            $sendBody = curl_init();
                            curl_setopt_array($sendBody, array(
                                CURLOPT_URL => $urlMessage,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                            ));
                            $result = curl_exec($sendBody);
                            curl_close($sendBody);
                            //$result = file_get_contents($urlMessage, false, $context); //SE REALIZA EL ENVIO
                            $data   = json_decode($result); //SE OBTIENE EL RESULTADO, SE DECODIFICA

                            //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR REENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                            if(isset($data)) {
                                if ($data->status == false) {
                                    //SE OBTIENE LA INSTANCIA DE CORNELIO
                                    $client_id = env('CLIENT_ID');
                                    $instance = env('INSTANCE');
                                    if ($contacto || $contacto != 0) {
                                        $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                                    } elseif ($group_id || $group_id != 0) {
                                        $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                    }
                                    //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                    //ENVIO DE MENSAJE
                                    $sendBody = curl_init();
                                    curl_setopt_array($sendBody, array(
                                        CURLOPT_URL => $urlMessage,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                    ));
                                    $result = curl_exec($sendBody);
                                    curl_close($sendBody);

                                    //$result = file_get_contents($urlMessage, false, $context);
                                    $dataP = json_decode($result);
                                    if ($dataP->status == false) {
                                        BitacoraMessageFb::create([
                                            'type'        => $contacto != 0 ? 'phone' : 'group',
                                            'typeMessage' => 'text',
                                            'number'      => $contacto != 0 ? $contacto : $group_id,
                                            'report'      => $report,
                                            'message'     => $message,
                                            'error'       => $dataP->message,
                                        ]);
                                    }
                                }
                            }else{
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone' : 'group',
                                    'typeMessage' => 'text',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $message,
                                    'error'       => $result,
                                ]);
                            }

                            /******************************  ENVIO DEL LINK ***********************************************/
                            foreach ($uniqueId as $uniqueLInk) {
                                $id_encry    = base64_encode($uniqueLInk);
                                $link        = "{$url_app}analysisLink/{$id_encry}/";
                                $messageLink = "En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+$nameTopics+$link";
                                if ($contacto || $contacto != 0) {
                                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$messageLink&type=text&=";
                                } elseif ($group_id || $group_id != 0) {
                                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id.'&message='.$messageLink&type=text";
                                }

                                //ENVIO DE MENSAJE
                                $sendBody = curl_init();
                                curl_setopt_array($sendBody, array(
                                    CURLOPT_URL => $urlMessage,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                ));
                                $result = curl_exec($sendBody);
                                curl_close($sendBody);
                                //$result = file_get_contents($urlMessage, false, $context);//SE REALIZA EL ENVIO
                                $data   = json_decode($result);//SE OBTIENE EL RESULTADO

                                //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL REENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                                if(isset($data)) {
                                    if ($data->status == false) {
                                        //SE OBTIENE LA INSTANCIA DE CORNELIO
                                        $client_id = env('CLIENT_ID');
                                        $instance = env('INSTANCE');
                                        if ($contacto || $contacto != 0) {
                                            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$messageLink&type=text&=";
                                        } elseif ($group_id || $group_id != 0) {
                                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                                        }
                                        //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                        //ENVIO DE MENSAJE
                                        $sendBody = curl_init();
                                        curl_setopt_array($sendBody, array(
                                            CURLOPT_URL => $urlMessage,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                        ));
                                        $result = curl_exec($sendBody);
                                        curl_close($sendBody);
                                        //$result = file_get_contents($urlMessage, false, $context);
                                        $dataP  = json_decode($result);
                                        if ($dataP->status == false) {
                                            BitacoraMessageFb::create([
                                                'type'        => $contacto != 0 ? 'phone' : 'group',
                                                'typeMessage' => 'text',
                                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                                'report'      => $report,
                                                'message'     => $messageLink,
                                                'error'       => $dataP->message,
                                            ]);
                                        }
                                    }
                                }else{
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone' : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $messageLink,
                                        'error'       => $result,
                                    ]);
                                }
                            }
                        } else {
                            foreach ($uniqueId as $infoPosicion) {
                                $id_encry       = base64_encode($infoPosicion);
                                $nameT          = Subcategory::where('id', $infoPosicion)->first();
                                $nameTopics     = $this->eliminar_acentos(str_replace(' ', '+', $nameT->name));
                                $consulta       = \App\Models\TopReaction::where('classification', $infoPosicion)->whereBetween('created_at', [$last, Carbon::now()])->pluck('position')->take(10)->toArray();
                                $positionTopics = implode(',', $consulta);

                                /***************************  ENVIO DE LA INFORMACION *************************************/
                                $message = "El+tema+$nameTopics+figura+en+el+Top+10+en+la+posici%C3%B3n+$positionTopics.";
                                if ($contacto || $contacto != 0) {
                                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                                } elseif ($group_id || $group_id != 0) {
                                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                }

                                //ENVIO DE MENSAJE
                                $sendBody = curl_init();
                                curl_setopt_array($sendBody, array(
                                    CURLOPT_URL => $urlMessage,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                ));
                                $result = curl_exec($sendBody);
                                curl_close($sendBody);
                                //$result = file_get_contents($urlMessage, false, $context); //SE REALIZA EL ENVIO
                                $data   = json_decode($result); //SE OBTIENE EL RESULTADO

                                //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                                if(isset($data)) {
                                    if ($data->status == false) {
                                        //SE OBTIENE LA INSTANCIA DE CORNELIO
                                        $client_id = env('CLIENT_ID');
                                        $instance  = env('INSTANCE');

                                        if ($contacto || $contacto != 0) {
                                            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                                        } elseif ($group_id || $group_id != 0) {
                                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                        }
                                        //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                        //ENVIO DE MENSAJE
                                        $sendBody = curl_init();
                                        curl_setopt_array($sendBody, array(
                                            CURLOPT_URL => $urlMessage,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                        ));
                                        $result = curl_exec($sendBody);
                                        curl_close($sendBody);
                                        //$result = file_get_contents($urlMessage, false, $context);
                                        $dataP = json_decode($result);
                                        if ($dataP->status == false) {
                                            BitacoraMessageFb::create([
                                                'type'        => $contacto != 0 ? 'phone' : 'group',
                                                'typeMessage' => 'text',
                                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                                'report'      => $report,
                                                'message'     => $message,
                                                'error'       => $dataP->message,
                                            ]);
                                        }
                                    }
                                }else{
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone' : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $message,
                                        'error'       => $result,
                                    ]);
                                }

                                /***************************  ENVIO DEL LINK **********************************************/
                                $link = "{$url_app}analysisLink/{$id_encry}/";
                                $messageLink = "En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+$nameTopics.+$link";
                                if ($contacto || $contacto != 0) {
                                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$messageLink&type=text&=";
                                } elseif ($group_id || $group_id != 0) {
                                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                                }

                                //ENVIO DE MENSAJE
                                $sendBody = curl_init();
                                curl_setopt_array($sendBody, array(
                                    CURLOPT_URL => $urlMessage,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                ));
                                $result = curl_exec($sendBody);
                                curl_close($sendBody);
                                //$result = file_get_contents($urlMessage, false, $context);//SE REALIZA EL ENVIO
                                $data   = json_decode($result);//SE OBTIENE EL RESULTADO

                                //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                                if(isset($data)) {
                                    if ($data->status == false) {
                                        //SE OBTIENE LA INSTANCIA DE CORNELIO
                                        $client_id = env('CLIENT_ID');
                                        $instance  = env('INSTANCE');
                                        if ($contacto || $contacto != 0) {
                                            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$messageLink&type=text&=";
                                        } elseif ($group_id || $group_id != 0) {
                                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                                        }
                                        //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                        //ENVIO DE MENSAJE
                                        $sendBody = curl_init();
                                        curl_setopt_array($sendBody, array(
                                            CURLOPT_URL => $urlMessage,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                        ));
                                        $result = curl_exec($sendBody);
                                        curl_close($sendBody);
                                        //$result = file_get_contents($urlMessage, false, $context);
                                        $dataP = json_decode($result);
                                        if ($dataP->status == false) {
                                            BitacoraMessageFb::create([
                                                'type'        => $contacto != 0 ? 'phone' : 'group',
                                                'typeMessage' => 'text',
                                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                                'report'      => $report,
                                                'message'     => $messageLink,
                                                'error'       => $dataP->message,
                                            ]);
                                        }
                                    }
                                }else{
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone' : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $messageLink,
                                        'error'       => $result,
                                    ]);
                                }
                            }
                        }
                    } else {
                        /***************************  ENVIO DE LA INFORMACION *************************************/
                        //$message = 'No+se+identifica+ning%C3%BAn+tema+para+' . $name . '';
                        $message = "No+se+identifica+ning%C3%BAn+tema+de+$name+dentro+del+top10+nacional";
                        if ($contacto || $contacto != 0) {
                            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                        } elseif ($group_id || $group_id != 0) {
                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }
                        //ENVIO DE MENSAJE
                        $sendBody = curl_init();
                        curl_setopt_array($sendBody, array(
                            CURLOPT_URL => $urlMessage,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $result = curl_exec($sendBody);
                        curl_close($sendBody);
                        //$result = file_get_contents($urlMessage, false, $context);//SE REALIZA EL ENVIO
                        $data   = json_decode($result);//SE OBTIENE EL RESULTADO

                        //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                        if(isset($data)){
                            if ($data->status == false) {
                                //SE OBTIENE LA INSTANCIA DE CORNELIO
                                $client_id = env('CLIENT_ID');
                                $instance  = env('INSTANCE');
                                if ($contacto || $contacto != 0) {
                                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                                } elseif ($group_id || $group_id != 0) {
                                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                }
                                //ENVIO DE MENSAJE
                                $sendBody = curl_init();
                                curl_setopt_array($sendBody, array(
                                    CURLOPT_URL => $urlMessage,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                ));
                                $result = curl_exec($sendBody);
                                curl_close($sendBody);

                                //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                //$result = file_get_contents($urlMessage, false, $context);
                                $dataP  = json_decode($result);
                                if($dataP->status == false){
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone'   : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $message,
                                        'error'       => $dataP->message,
                                    ]);
                                }
                            }
                        }else{
                            BitacoraMessageFb::create([
                                'type'        => $contacto != 0 ? 'phone' : 'group',
                                'typeMessage' => 'text',
                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                'report'      => $report,
                                'message'     => $message,
                                'error'       => $result,
                            ]);
                        }
                    }
                } else {
                    $message = "No+se+identifica+ning%C3%BAn+tema+de+$name+dentro+del+top10+nacional";
                    if ($contacto || $contacto != 0) {
                        $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                    } elseif ($group_id || $group_id != 0) {
                        $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                    }
                    //ENVIO DE MENSAJE
                    $sendBody = curl_init();
                    curl_setopt_array($sendBody, array(
                        CURLOPT_URL => $urlMessage,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendBody);
                    curl_close($sendBody);

                    //$result = file_get_contents($urlMessage, false, $context);//SE REALIZA EL ENVIO
                    $data   = json_decode($result);//SE OBTIENE EL RESULTADO

                    //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                    if(isset($data)) {
                        if ($data->status == false) {
                            //SE OBTIENE LA INSTANCIA DE CORNELIO
                            $client_id = env('CLIENT_ID');
                            $instance  = env('INSTANCE');
                            if ($contacto || $contacto != 0) {
                                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                            } elseif ($group_id || $group_id != 0) {
                                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                            }

                            //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCE DE CORNELIO
                            //ENVIO DE MENSAJE
                            $sendBody = curl_init();
                            curl_setopt_array($sendBody, array(
                                CURLOPT_URL => $urlMessage,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                            ));
                            $result = curl_exec($sendBody);
                            curl_close($sendBody);

                            //$result = file_get_contents($urlMessage, false, $context);
                            $dataP = json_decode($result);
                            if(isset($dataP)){
                                if ($dataP->status == false) {
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone' : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $message,
                                        'error'       => $dataP->message,
                                    ]);
                                }
                            }else{
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone' : 'group',
                                    'typeMessage' => 'text',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $message,
                                    'error'       => $result,
                                ]);
                            }

                        }
                    }else{
                        BitacoraMessageFb::create([
                            'type'        => $contacto != 0 ? 'phone' : 'group',
                            'typeMessage' => 'text',
                            'number'      => $contacto != 0 ? $contacto : $group_id,
                            'report'      => $report,
                            'message'     => $message,
                            'error'       => $result,
                        ]);
                    }
                }
            }catch (\Exception $e){
                BitacoraMessageFb::create([
                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                    'typeMessage' => 'text',
                    'number'      => $contacto != 0 ? $contacto : $group_id,
                    'report'      => $report,
                    'message'     => $message,
                    'error'       => $e,
                ]);
                continue;
            }
        }
    }

    public function topComparator_V2($report, $numbers){
        //Ultimos minutos
        $last = Carbon::now()->subMinute(10);

        //Numero de telefono
        //$numbers = NumberWhatsapp::where('report', $report)->get();

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = TopReaction::whereBetween('created_at',[$last, Carbon::now()])->take(10)->get();

        $i             = 0;
        $info          = [];
        $arrayId       = [];
        $arrayName     = [];
        $arrayPosicion = [];

        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach($numbers as $number){
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance  = $comp->instance;
            }

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $companie_top  = $number->company_id;
            $companie_name = Company::where('id',$number->company_id)->first();

            $header   = array('http'=>array('method'=>"POST",));
            $context  = stream_context_create($header);

            foreach ($data_pdf as $pdf){
                $companie_pdf = $pdf->company;
                if($companie_top == $companie_pdf){
                    $tema = Subcategory::where('id', $pdf->classification)->first();
                    if($pdf->position  <= 5 ){
                        $info[$i]['posicion'] = $pdf->position;
                        $info[$i]['tema']     = $tema->name;
                        $info[$i]['tema_id']  = $pdf->classification;

                        array_push($arrayId, $pdf->classification);
                        array_push($arrayName, $tema->name);
                        array_push($arrayPosicion, $pdf->position);
                        $i++;
                    }
                }
            }
            try {
                if ($info != [] || $info != null) {
                    $uniqueId   = array_unique($arrayId);
                    $uniqueName = array_unique($arrayName);
                    $url_app    = env("APP_URL");
                    $start      = base64_encode(Carbon::now()->subHour(72));
                    $end        = base64_encode(Carbon::now());

                    if (count(array_unique($uniqueId)) == 1 && count(array_unique($uniqueName)) == 1) {
                        $idTopics = implode("", $uniqueId);
                        $nameT    = implode("", $uniqueName);
                        $name     = $this->eliminar_acentos(str_replace(' ', '+', $nameT));
                        $sub      = base64_encode($idTopics);
                        $link     = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                        $message  = "En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+$name.+$link.";

                        /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE LA COMPAÑIA******************************************************/
                        if ($contacto || $contacto != 0) {
                            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                        } elseif ($group_id || $group_id != 0) {
                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }

                        //ENVIO DE MENSAJE
                        $sendBody = curl_init();
                        curl_setopt_array($sendBody, array(
                            CURLOPT_URL => $urlMessage,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $result = curl_exec($sendBody);
                        curl_close($sendBody);
                        //$result = file_get_contents($urlMessage, false, $context); //SE REALIZA EL ENVIO
                        $data   = json_decode($result); //SE OBTIENE EL RESULTADO

                        /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE CORNELIO******************************************************/
                        //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL REENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                        if(isset($data)) {
                            if ($data->status == false) {
                                //SE OBTIENE LA INSTANCIA DE CORNELIO
                                $client_id = env('CLIENT_ID');
                                $instance  = env('INSTANCE');
                                if ($contacto || $contacto != 0) {
                                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                                } elseif ($group_id || $group_id != 0) {
                                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                }
                                //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                //ENVIO DE MENSAJE
                                $sendBody = curl_init();
                                curl_setopt_array($sendBody, array(
                                    CURLOPT_URL => $urlMessage,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                ));
                                $result = curl_exec($sendBody);
                                curl_close($sendBody);
                                //$result = file_get_contents($urlMessage, false, $context);
                                $dataC = json_decode($result);
                                if ($dataC->status == false) {
                                    BitacoraMessageFb::create([
                                        'type'        => $contacto != 0 ? 'phone' : 'group',
                                        'typeMessage' => 'text',
                                        'number'      => $contacto != 0 ? $contacto : $group_id,
                                        'report'      => $report,
                                        'message'     => $message,
                                        'error'       => $dataC->message,
                                    ]);
                                }
                            }
                        }else{
                            BitacoraMessageFb::create([
                                'type'        => $contacto != 0 ? 'phone'   : 'group',
                                'typeMessage' => 'text',
                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                'report'      => $report,
                                'message'     => $message,
                                'error'       => $result,
                            ]);
                        }
                    }
                    else {
                        foreach ($uniqueId as $infoPosicion) {
                            $sub        = base64_encode($infoPosicion);
                            $nameTopics = Subcategory::where('id', $infoPosicion)->first();
                            $name       = $this->eliminar_acentos(str_replace(' ', '', $nameTopics->name));
                            $link       = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                            $message    = "En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+$name+$link";

                            /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE LA COMPAÑIA******************************************************/
                            if ($contacto || $contacto != 0) {
                                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                            } elseif ($group_id || $group_id != 0) {
                                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                            }

                            //ENVIO DE MENSAJE
                            $sendBody = curl_init();
                            curl_setopt_array($sendBody, array(
                                CURLOPT_URL => $urlMessage,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                            ));
                            $result = curl_exec($sendBody);
                            curl_close($sendBody);
                            //$result = file_get_contents($urlMessage, false, $context); //SE REALIZA EL ENVIO
                            $data   = json_decode($result); //SE OBTIENE EL RESULTADO

                            /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE CORNELIO******************************************************/
                            //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                            if (isset($data)) {
                                if ($data->status == false) {
                                    //SE OBTIENE LA INSTANCIA DE CORNELIO
                                    $client_id = env('CLIENT_ID');
                                    $instance = env('INSTANCE');
                                    if ($contacto || $contacto != 0) {
                                        $urlMessage = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $message . '&type=text&=';
                                    } elseif ($group_id || $group_id != 0) {
                                        $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                    }
                                    //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                    //ENVIO DE MENSAJE
                                    $sendBody = curl_init();
                                    curl_setopt_array($sendBody, array(
                                        CURLOPT_URL => $urlMessage,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                    ));
                                    $result = curl_exec($sendBody);
                                    curl_close($sendBody);
                                    //$result = file_get_contents($urlMessage, false, $context);
                                    $dataC = json_decode($result);
                                    if ($dataC->status == false) {
                                        BitacoraMessageFb::create([
                                            'type' => $contacto != 0 ? 'phone' : 'group',
                                            'typeMessage' => 'text',
                                            'number' => $contacto != 0 ? $contacto : $group_id,
                                            'report' => $report,
                                            'message' => $message,
                                            'error' => $dataC->message,
                                        ]);
                                    }
                                }
                            }else{
                                BitacoraMessageFb::create([
                                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                                    'typeMessage' => 'text',
                                    'number'      => $contacto != 0 ? $contacto : $group_id,
                                    'report'      => $report,
                                    'message'     => $message,
                                    'error'       => $result,
                                ]);
                            }
                        }
                    }
                }
            }catch (\Exception $e){
                BitacoraMessageFb::create([
                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                    'typeMessage' => 'text',
                    'number'      => $contacto != 0 ? $contacto : $group_id,
                    'report'      => $report,
                    'message'     => $message,
                    'error'       => $e,
                ]);
                continue;
            }
        }
    }

    public function topBubles_V2($report, $numbers){
       // $numbers   = NumberWhatsapp::where('report', $report)->where('company_id', '!=',24)->get();
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;

        foreach ($numbers as $number){
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance  = $comp->instance;
            }

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;

            $name = '';
            $contents_encode = base64_encode("[]");
            $validation      = ($number->content == "[]" ? null :  $number->content);
            if($validation != "null" && $validation){
                $contents_encode = base64_encode($number->content);
                $contents_id     = json_decode($number->content);
                $getContent      = Category::whereIn('id', $contents_id)->pluck('name');
                $nameContent     = json_decode($getContent);
                $nameC           = implode(", ", $nameContent);
                $name            = $this->eliminar_acentos(str_replace(' ', '+', $nameC));
            }

            $header   = array('http'=>array('method'=>"POST",));
            $context  = stream_context_create($header);
            $nameUser = $this->eliminar_acentos(str_replace(' ', '+', $number->descripcion));
            $company  = $number->company_id;
            $url_app  = env("APP_URL");
            $comp     = base64_encode($company);
            $link     = "{$url_app}BublesContent/{$comp}/{$contents_encode}";
            $message  = "Hola+$nameUser%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+$name.+$link.'+";
            //$message = 'Hola+'.$nameUser.'%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+Medios+de+Comunicaci%C3%B3n%2C+Diputados+y+Grupos+Sindicales.+'.$link.'+';
            try {
                if ($contacto || $contacto != 0) {
                    $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
                } elseif ($group_id || $group_id != 0) {
                    $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                }
                //ENVIO DE MENSAJE
                $sendBody = curl_init();
                curl_setopt_array($sendBody, array(
                    CURLOPT_URL => $urlMessage,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $result = curl_exec($sendBody);
                curl_close($sendBody);
                //$result = file_get_contents($urlMessage, false, $context); //SE REALIZA EL ENVIO
                $data  = json_decode($result);//SE OBTIENE EL RESULTADO
                /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE CORNELIO******************************************************/
                //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                if(isset($data)) {
                    if ($data->status == false) {
                        //SE OBTIENE LA INSTANCIA DE CORNELIO
                        $client_id = env('CLIENT_ID');
                        $instance  = env('INSTANCE');
                        if ($contacto || $contacto != 0) {
                            $urlMessage = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $message . '&type=text&=';
                        } elseif ($group_id || $group_id != 0) {
                            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }
                        //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                        //ENVIO DE MENSAJE
                        $sendBody = curl_init();
                        curl_setopt_array($sendBody, array(
                            CURLOPT_URL => $urlMessage,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $result = curl_exec($sendBody);
                        curl_close($sendBody);
                        //$result = file_get_contents($urlMessage, false, $context);
                        $dataB  = json_decode($result);
                        if ($dataB->status == false) {
                            BitacoraMessageFb::create([
                                'type'        => $contacto != 0 ? 'phone' : 'group',
                                'typeMessage' => 'text',
                                'number'      => $contacto != 0 ? $contacto : $group_id,
                                'report'      => $report,
                                'message'     => $message,
                                'error'       => $dataB->message,
                            ]);
                        }
                    }
                }else{
                    BitacoraMessageFb::create([
                        'type'        => $contacto != 0 ? 'phone'   : 'group',
                        'typeMessage' => 'text',
                        'number'      => $contacto != 0 ? $contacto : $group_id,
                        'report'      => $report,
                        'message'     => $message,
                        'error'       => $result,
                    ]);
                }
            }catch (\Exception $e){
                BitacoraMessageFb::create([
                    'type'        => $contacto != 0 ? 'phone'   : 'group',
                    'typeMessage' => 'text',
                    'number'      => $contacto != 0 ? $contacto : $group_id,
                    'report'      => $report,
                    'message'     => $message,
                    'error'       => $e,
                ]);
                continue;
            }
        }
    }

    public function sendTopReaction($information){
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $header    = array('http'=>array('method'=>"POST",));
        $context   = stream_context_create($header);
        $message   = $information->message;

        if($information->typeMessage == 'text'){
            if($information->type == 'phone'){
                $contacto   = $information->number;
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
            }
            else if($information->type == 'group'){
                $group_id   = $information->number;
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
            }
            //ENVIO DE MENSAJE
            $sendBody = curl_init();
            curl_setopt_array($sendBody, array(
                CURLOPT_URL => $urlMessage,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));
            $result = curl_exec($sendBody);
            curl_close($sendBody);
            $data   = json_decode($result);
            if(isset($data)){
                if($data->status == false){
                    if($information->type == 'phone'){
                        $contacto      = $information->number;
                        $messageDecode = urldecode($message);
                        $api           = ApiWhatsapp::first('key');
                        $key           = $api->key;
                        if($key){
                            $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        }
                    }
                    else if($information->type == 'group'){
                        $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                        $sendList = curl_init();
                        curl_setopt_array($sendList, array(
                            CURLOPT_URL => $list,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $result = curl_exec($sendList);
                        curl_close($sendList);
                        $data   = json_decode($result);
                        if(isset($data)){
                            if ($data->status == true) {
                                foreach ($data->data as $sendSms) {
                                    $api           = ApiWhatsapp::first('key');
                                    $key           = $api->key;
                                    $messageDecode = urldecode($message);
                                    $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                    if($key){
                                        $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else{
                if($information->type == 'phone'){
                    $contacto      = $information->number;
                    $messageDecode = urldecode($message);
                    $api           = ApiWhatsapp::first('key');
                    $key           = $api->key;
                    if($key){
                        $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                    }
                }
                else if($information->type == 'group'){
                    $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                    $sendList = curl_init();
                    curl_setopt_array($sendList, array(
                        CURLOPT_URL => $list,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendList);
                    curl_close($sendList);
                    $data   = json_decode($result);
                    if(isset($data)){
                        if ($data->status == true) {
                            foreach ($data->data as $sendSms) {
                                $api           = ApiWhatsapp::first('key');
                                $key           = $api->key;
                                $messageDecode = urldecode($message);
                                $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                if($key){
                                    $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
            }
        }
        else if($information->typeMessage == 'file'){
            if($information->type == 'phone'){
                $contacto   = $information->number;
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=image";
            }
            else if($information->type == 'group'){
                $group_id   = $information->number;
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=file";
            }
            //$result = file_get_contents($urlMessage, false, $context);
            //ENVIO DE PDF
            $sendPdf = curl_init();
            curl_setopt_array($sendPdf, array(
                CURLOPT_URL => $urlMessage,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));
            $resultP = curl_exec($sendPdf);
            curl_close($sendPdf);
            $data   = json_decode($resultP);
            if(isset($data)){
                if($data->status == false){
                    if($information->type == 'phone'){
                        $contacto      = $information->number;
                        $messageDecode = "Te compartimos el siguiente link para que puedas ver las reacciones con más interacción. $message";
                        $api           = ApiWhatsapp::first('key');
                        $key           = $api->key;
                        if($key){
                            $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        }
                    }
                    else if($information->type == 'group'){
                        $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                        $sendList = curl_init();
                        curl_setopt_array($sendList, array(
                            CURLOPT_URL => $list,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                        ));
                        $result = curl_exec($sendList);
                        curl_close($sendList);
                        //$result = file_get_contents($urlMessage, false, $context);
                        $data   = json_decode($result);
                        if(isset($data)){
                            if ($data->status == true) {
                                foreach ($data->data as $sendSms) {
                                    $api           = ApiWhatsapp::first('key');
                                    $key           = $api->key;
                                    $messageDecode = "Te compartimos el siguiente link para que puedas ver las reacciones con más interacción. $message";
                                    $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                    if($key){
                                        $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else{
                if($information->type == 'phone'){
                    $contacto      = $information->number;
                    $messageDecode = "Te compartimos el siguiente link para que puedas ver las reacciones con más interacción. $message";
                    $api           = ApiWhatsapp::first('key');
                    $key           = $api->key;
                    if($key){
                        $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                    }
                }
                else if($information->type == 'group'){
                    $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                    $sendList = curl_init();
                    curl_setopt_array($sendList, array(
                        CURLOPT_URL => $list,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendList);
                    curl_close($sendList);
                    //$result = file_get_contents($urlMessage, false, $context);
                    $data   = json_decode($result);
                    if(isset($data)){
                        if ($data->status == true) {
                            foreach ($data->data as $sendSms) {
                                $api           = ApiWhatsapp::first('key');
                                $key           = $api->key;
                                $messageDecode = "Te compartimos el siguiente link para que puedas ver las reacciones con más interacción. $message";
                                $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                if($key){
                                    $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function sendTopAnalysis($information)
    {
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $header    = array('http'=>array('method'=>"POST",));
        $context   = stream_context_create($header);
        $message   = $information->message;

        if($information->type == 'phone'){
            $contacto   = $information->number;
            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
        }
        else if($information->type == 'group'){
            $group_id   = $information->number;
            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
        }
        //ENVIO DE MENSAJE
        $sendBody = curl_init();
        curl_setopt_array($sendBody, array(
            CURLOPT_URL => $urlMessage,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        $result = curl_exec($sendBody);
        curl_close($sendBody);
        //$result = file_get_contents($urlMessage, false, $context);
        $data   = json_decode($result);
        if(isset($data)){
            if($data->status == false){
                if($information->type == 'phone'){
                    $contacto      = $information->number;
                    $messageDecode = urldecode($message);
                    $api           = ApiWhatsapp::first('key');
                    $key           = $api->key;
                    if($key){
                        $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                    }
                }
                else if($information->type == 'group'){
                    $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                    $sendList = curl_init();
                    curl_setopt_array($sendList, array(
                        CURLOPT_URL => $list,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendList);
                    curl_close($sendList);
                    $data   = json_decode($result);
                    if(isset($data)){
                        if ($data->status == true) {
                            foreach ($data->data as $sendSms) {
                                $api           = ApiWhatsapp::first('key');
                                $key           = $api->key;
                                $messageDecode = urldecode($message);
                                $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                if($key){
                                    $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
            }
        }
        else{
            if($information->type == 'phone'){
                $contacto      = $information->number;
                $messageDecode = urldecode($message);
                $api           = ApiWhatsapp::first('key');
                $key           = $api->key;
                if($key){
                    $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                }
            }
            else if($information->type == 'group'){
                $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                $sendList = curl_init();
                curl_setopt_array($sendList, array(
                    CURLOPT_URL => $list,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $result = curl_exec($sendList);
                curl_close($sendList);
                $data   = json_decode($result);
                if(isset($data)){
                    if ($data->status == true) {
                        foreach ($data->data as $sendSms) {
                            $api           = ApiWhatsapp::first('key');
                            $key           = $api->key;
                            $messageDecode = urldecode($message);
                            $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                            if($key){
                                $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                            }
                        }
                    }
                }
            }
        }
    }

    public function sendTopComparator($information)
    {
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $header    = array('http'=>array('method'=>"POST",));
        $context   = stream_context_create($header);
        $message   = $information->message;

        if($information->type == 'phone'){
            $contacto   = $information->number;
            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
        }
        else if($information->type == 'group'){
            $group_id   = $information->number;
            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
        }
        //ENVIO DE MENSAJE
        $sendBody = curl_init();
        curl_setopt_array($sendBody, array(
            CURLOPT_URL => $urlMessage,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        $result = curl_exec($sendBody);
        curl_close($sendBody);
        //$result = file_get_contents($urlMessage, false, $context);
        $data   = json_decode($result);
        if(isset($data)){
            if($data->status == false){
                if($information->type == 'phone'){
                    $contacto      = $information->number;
                    $messageDecode = urldecode($message);
                    $api           = ApiWhatsapp::first('key');
                    $key           = $api->key;
                    if($key){
                        $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                    }
                }
                else if($information->type == 'group'){
                    $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                    $sendList = curl_init();
                    curl_setopt_array($sendList, array(
                        CURLOPT_URL => $list,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendList);
                    curl_close($sendList);
                    $data   = json_decode($result);
                    if(isset($data)){
                        if ($data->status == true) {
                            foreach ($data->data as $sendSms) {
                                $api           = ApiWhatsapp::first('key');
                                $key           = $api->key;
                                $messageDecode = urldecode($message);
                                $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                if($key){
                                    $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
            }
        }
        else{
            if($information->type == 'phone'){
                $contacto      = $information->number;
                $messageDecode = urldecode($message);
                $api           = ApiWhatsapp::first('key');
                $key           = $api->key;
                if($key){
                    $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                }
            }
            else if($information->type == 'group'){
                $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                $sendList = curl_init();
                curl_setopt_array($sendList, array(
                    CURLOPT_URL => $list,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $result = curl_exec($sendList);
                curl_close($sendList);
                $data   = json_decode($result);
                if(isset($data)){
                    if ($data->status == true) {
                        foreach ($data->data as $sendSms) {
                            $api           = ApiWhatsapp::first('key');
                            $key           = $api->key;
                            $messageDecode = urldecode($message);
                            $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                            if($key){
                                $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                            }
                        }
                    }
                }
            }
        }
    }

    public function sendTopBubles($information)
    {
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $header    = array('http'=>array('method'=>"POST",));
        $context   = stream_context_create($header);
        $message   = $information->message;

        if($information->type == 'phone'){
            $contacto   = $information->number;
            $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$contacto&message=$message&type=text&=";
        }
        else if($information->type == 'group'){
            $group_id   = $information->number;
            $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
        }
        //ENVIO DE MENSAJE
        $sendBody = curl_init();
        curl_setopt_array($sendBody, array(
            CURLOPT_URL => $urlMessage,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        $result = curl_exec($sendBody);
        curl_close($sendBody);
        $data   = json_decode($result);
        if(isset($data)){
            if($data->status == false){
                if($information->type == 'phone'){
                    $contacto      = $information->number;
                    $messageDecode = urldecode($message);
                    $api           = ApiWhatsapp::first('key');
                    $key           = $api->key;
                    if($key){
                        $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                    }
                }
                else if($information->type == 'group'){
                    $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                    $sendList = curl_init();
                    curl_setopt_array($sendList, array(
                        CURLOPT_URL => $list,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ));
                    $result = curl_exec($sendList);
                    curl_close($sendList);
                    $data   = json_decode($result);
                    if(isset($data)){
                        if ($data->status == true) {
                            foreach ($data->data as $sendSms) {
                                $api           = ApiWhatsapp::first('key');
                                $key           = $api->key;
                                $messageDecode = urldecode($message);
                                $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                if($key){
                                    $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
            }
        }
        else{
            if($information->type == 'phone'){
                $contacto      = $information->number;
                $messageDecode = urldecode($message);
                $api           = ApiWhatsapp::first('key');
                $key           = $api->key;
                if($key){
                    $this->sendSms($contacto, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                }
            }
            else if($information->type == 'group'){
                $list = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
                $sendList = curl_init();
                curl_setopt_array($sendList, array(
                    CURLOPT_URL => $list,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $result = curl_exec($sendList);
                curl_close($sendList);
                $data   = json_decode($result);
                if(isset($data)){
                    if ($data->status == true) {
                        foreach ($data->data as $sendSms) {
                            $api           = ApiWhatsapp::first('key');
                            $key           = $api->key;
                            $messageDecode = urldecode($message);
                            $number        = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                            if($key){
                                $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                            }
                        }
                    }
                }
            }
        }
    }

}
