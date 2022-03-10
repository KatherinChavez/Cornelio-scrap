<?php

namespace App\Console\Commands\TopReactions;

use App\Models\ApiWhatsapp;
use App\Models\BitacoraPDF;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Top;
use App\Traits\SendReaction;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Ilovepdf\Ilovepdf;
use QuickChart;
use function GuzzleHttp\Psr7\str;
use SebastianBergmann\CodeCoverage\TestFixture\C;

class TopReactionM extends Command
{
    //use TopicCountTrait;
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:TopReactionM';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se envia un reporte de las 10 publicaciones con mas interaccion al medio dia';

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
     * @return int
     */
    public function handle()
    {
        /************************************ ENVIO DE PDF *************************************************************/
        /* 1 PDF DE LAS 6:40
         * 2 PDF DE LAS 12:30
         * 3 PDF DE LAS 4:30
         * 4 PDF DE LAS 11:40 */

        $saludo = "¡Hola+";
        $info   = "!+Adjunto+el+TOP+10+de+temas+con+m%C3%A1s+interacciones+en+las+Redes+Sociales+al+medio+d%C3%ADa";
        $report = 2;
        $this->topReaction($report, $saludo, $info);
        return 200;

        /**************************************************************************************************************/

        //Ultimas 24 horas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);
        //Top 10 de publicaciones con mas interacciones
        $data = array();
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
        $keys= array_column($data,'count');
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
        $content = PDF::loadView('PDF.pdf', compact('topData'))->output();
        $fileName = 'Top-'.Carbon::now()->format('Y-m-d').'_hora_'.Carbon::now()->format('H-i').'.pdf';
        $file = Storage::disk('public_files')->put($fileName, $content);
        $path = Storage::disk('public_files')->path($fileName);

        $ilovepdf       = new Ilovepdf(env('ILOVEPDF_PROJECT_KEY'),env('ILOVEPDF_SECRET_KEY'));
        $myTaskCompress = $ilovepdf->newTask('compress');
        $file1          = $myTaskCompress->addFile($path);
        $myTaskCompress->setOutputFilename($fileName);
        $myTaskCompress->execute();
        //$myTaskCompress->download("../public/whatsapp_filesCompress");       //LOCAL
        $myTaskCompress->download("/home/monitoreocornel/public_html/whatsapp_filesCompress"); //PRODUCCION

        //Se envia el pdf a los que desea que resibir la informacion al medio dia
        $phones = NumberWhatsapp::where('subcategory_id', null)->where('report',2)->get();
        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';

        foreach ($phones as $contactoArray) {
            $comp = Company::where('id', $contactoArray->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $contacto=$contactoArray->numeroTelefono;
            //$contacto='50686258376';
            $group_id = $contactoArray->group_id;

            $name = $this->eliminar_acentos(str_replace(' ', '', $contactoArray->descripcion));
            $mensaje = '¡Hola+'.$name.'!+Adjunto+el+TOP+10+de+temas+con+m%C3%A1s+interacciones+en+las+Redes+Sociales+al+medio+d%C3%ADa';
            if ($contacto || $contacto != 0) {
                $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
                $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_filesCompress/' . $fileName . '&type=file';
            } elseif ($group_id || $group_id != 0) {
                $urlBody = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
                $urlPdf = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=" . env('APP_URL') . "whatsapp_filesCompress/$fileName&type=file";
            }

            $headermail = array('http'=>array('method'=>"POST",));
            $context    = stream_context_create($headermail);
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

            //$resultB = file_get_contents($urlBody,false, $context);
            //$resultP = file_get_contents($urlPdf,false, $context);
            $dataB = json_decode($resultB);
            $dataP = json_decode($resultP);

            /*if ($dataB->status == false && $dataP->status == false) {
                $client_id = env('CLIENT_ID');
                $instance = env('INSTANCE');
                if ($contacto || $contacto != 0) {
                    $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
                    $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_filesCompress/' . $fileName . '&type=file';
                } elseif ($group_id || $group_id != 0) {
                    $urlBody = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
                    $urlPdf = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=" . env('APP_URL') . "whatsapp_filesCompress/$fileName&type=file";
                }
                $resultB = file_get_contents($urlBody,false, $context);
                $resultP = file_get_contents($urlPdf,false, $context);
            }*/
            if(isset($dataB)) {
                if ($dataB->status == false) {
                    $client_id = env('CLIENT_ID');
                    $instance = env('INSTANCE');
                    if ($contacto || $contacto != 0) {
                        $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
                    } elseif ($group_id || $group_id != 0) {
                        $urlBody = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
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
                    //$resultB = file_get_contents($urlBody,false, $context);
                }
            }
            else{
                $client_id = env('CLIENT_ID');
                $instance = env('INSTANCE');
                if ($contacto || $contacto != 0) {
                    $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
                } elseif ($group_id || $group_id != 0) {
                    $urlBody = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$mensaje&type=text";
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
            }

            if(isset($dataP)) {
                if ($dataP->status == false) {
                    $client_id = env('CLIENT_ID');
                    $instance = env('INSTANCE');
                    if ($contacto || $contacto != 0) {
                        $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_filesCompress/' . $fileName . '&type=image';
                    } elseif ($group_id || $group_id != 0) {
                        $urlPdf = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=" . env('APP_URL') . "whatsapp_filesCompress/$fileName&type=file";
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
                    //$resultP = file_get_contents($urlPdf,false, $context);
                }
            }
            else{
                $client_id = env('CLIENT_ID');
                $instance = env('INSTANCE');
                if ($contacto || $contacto != 0) {
                    $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_filesCompress/' . $fileName . '&type=image';
                } elseif ($group_id || $group_id != 0) {
                    $urlPdf = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=" . env('APP_URL') . "whatsapp_filesCompress/$fileName&type=file";
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
                'position' => $value['posicion'],
                'post_id' => $value['reaction']->post_id,
                'page_id' => $value['reaction']->page_id,
                'page_name' => $value['name'],
                'company' => $company,
                'content' => $value['content'],
                'classification' => $classification,
                'likes' => $value['reaction']->likes,
                'love' => $value['reaction']->love,
                'haha' => $value['reaction']->haha,
                'sad' => $value['reaction']->sad,
                'wow' => $value['reaction']->wow,
                'angry' => $value['reaction']->angry,
                'shared' => $value['reaction']->shared,
                'count' => $value['count'],
                'date' => $value['date'],
                'fileName' => $fileName,
            ]);
        }

        BitacoraPDF::create([
            'file' => $fileName,
            'url' => env('APP_URL').'whatsapp_filesCompress/'.$fileName,
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now()
        ]);
        return 200;
    }
}
