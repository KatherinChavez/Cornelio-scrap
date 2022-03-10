<?php

namespace App\Console\Commands\TopReactions;

use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Models\TopReaction;
use App\Traits\SendReaction;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TopAnalysisT extends Command
{
    //use TopicCountTrait;
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:TopAnalysisT';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analisa los top reacciones y se envia deacuerdo a la empresa en la tarde ';

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
        /************************************ ENVIO DE ANALISIS    *****************************************************/
        /* 5 ANALISIS DE LAS 6:40
         * 6 ANALISIS DE LAS 12:30
         * 7 ANALISIS DE LAS 4:30
         * 8 ANALISIS DE LAS 11:40 */

        $report = 7;
        $numbers = NumberWhatsapp::where('report', $report)->get();
        $this->topAnalysis($report, $numbers);
        /*foreach ($numbers as $number){
            $id     = $number->numeroTelefono != 0 ? $number->numeroTelefono : $number->group_id;
            $query  = BitacoraMessageFb::where('number', $id)->where('report', 3)->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->first();
            if(!$query){
                $send   = [];
                $send[] = $number;
                $this->topAnalysis($report, $send);
            }
        }*/
        return 200;
        /***************************************************************************************************************/

        //Ultimos minutos
        $last = Carbon::now()->subMinute(10);
        //$last = Carbon::now()->subHour(3);

        //Numero de telefono
        $numbers = NumberWhatsapp::where('report', 7)->get();

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = TopReaction::whereBetween('created_at',[ $last, Carbon::now()])->take(10)->get();
        $i = 0;


        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';

        foreach ($numbers as $number) {
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $info = [];
            $arrayId = [];
            $arrayName = [];
            $arrayPosicion = [];

            $contacto=$number->numeroTelefono;
            $group_id = $number->group_id;
            //$contacto = '50686258376';

            $companie_top = $number->company_id;
            $companie_n = Company::where('id', $number->company_id)->first();
            $companie_name = str_replace('Prensa', '', $companie_n->nombre);
            $name = $this->eliminar_acentos(str_replace(' ', '', $companie_name));
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

            $header  = array('http'=>array('method'=>"POST",));
            $context = stream_context_create($header);

            if ($info != [] ) {
                $url_app = env("APP_URL");

                $uniqueId = array_unique($arrayId);
                $unique = array_unique($arrayName);

                $nameT = implode("%2C+", $unique);
                $positionTopics = implode("%2C+", $arrayPosicion);
                $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT));
                if ($i >= 1) {
                    if (count(array_unique($unique)) == 1 && count(array_unique($uniqueId))==1) {
                        /***************************  ENVIO DE LA INFORMACION *****************************************/
                        $message = 'El+tema+' . $nameTopics . '+figura+en+el+Top+10+en+la+posici%C3%B3n+' . $positionTopics . '.';
                        if($contacto || $contacto != 0){
                            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                        }
                        elseif($group_id || $group_id != 0){
                            $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }
                        //SE REALIZA EL ENVIO
                        $result = file_get_contents($urlMessage, false, $context);
                        //SE OBTIENE EL RESULTADO
                        $data = json_decode($result);
                        //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                        if ($data->status == false){
                            //SE OBTIENE LA INSTANCIA DE CORNELIO
                            $client_id = env('CLIENT_ID');
                            $instance = env('INSTANCE');
                            if($contacto || $contacto != 0){
                                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                            }
                            elseif($group_id || $group_id != 0){
                                $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                            }
                            //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                            $result = file_get_contents($urlMessage, false, $context);
                        }

                        /******************************  ENVIO DEL LINK ***********************************************/
                        foreach ($uniqueId as $uniqueLInk){
                            $id_encry = base64_encode($uniqueLInk);
                            $link = "{$url_app}analysisLink/{$id_encry}/";
                            $messageLink = 'En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+'.$nameTopics.'+' . $link . '';
                            if($contacto || $contacto != 0){
                                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                            }
                            elseif($group_id || $group_id != 0){
                                $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                            }
                            //SE REALIZA EL ENVIO
                            $result = file_get_contents($urlMessage, false, $context);
                            //SE OBTIENE EL RESULTADO
                            $data = json_decode($result);
                            //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                            if ($data->status == false){
                                //SE OBTIENE LA INSTANCIA DE CORNELIO
                                $client_id = env('CLIENT_ID');
                                $instance = env('INSTANCE');
                                if($contacto || $contacto != 0){
                                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                                }
                                elseif($group_id || $group_id != 0){
                                    $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                                }
                                //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                $result = file_get_contents($urlMessage, false, $context);
                            }
                        }
                    }
                    else {
                        foreach ($uniqueId as $infoPosicion){
                            $id_encry = base64_encode($infoPosicion);
                            $nameT = Subcategory::where('id', $infoPosicion)->first();
                            $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT->name));
                            $consulta = TopReaction::where('classification', $infoPosicion)->whereBetween('created_at',[$last, Carbon::now()])->pluck('position')->take(10)->toArray();
                            $positionTopics = implode(',', $consulta);
                            /***************************  ENVIO DE LA INFORMACION *************************************/
                            $message = 'El+tema+' . $nameTopics . '+figura+en+el+Top+10+en+la+posici%C3%B3n+' . $positionTopics . '.';
                            if($contacto || $contacto != 0){
                                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                            }
                            elseif($group_id || $group_id != 0){
                                $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                            }
                            //SE REALIZA EL ENVIO
                            $result = file_get_contents($urlMessage, false, $context);
                            //SE OBTIENE EL RESULTADO
                            $data = json_decode($result);
                            //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                            if ($data->status == false){
                                //SE OBTIENE LA INSTANCIA DE CORNELIO
                                $client_id = env('CLIENT_ID');
                                $instance = env('INSTANCE');
                                if($contacto || $contacto != 0){
                                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                                }
                                elseif($group_id || $group_id != 0){
                                    $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                                }
                                //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                $result = file_get_contents($urlMessage, false, $context);
                            }

                            /***************************  ENVIO DEL LINK **********************************************/
                            $link = "{$url_app}analysisLink/{$id_encry}/";
                            $messageLink = 'En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+'.$nameTopics.'+' . $link . '';
                            if($contacto || $contacto != 0){
                                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                            }
                            elseif($group_id || $group_id != 0){
                                $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                            }
                            //SE REALIZA EL ENVIO
                            $result = file_get_contents($urlMessage, false, $context);
                            //SE OBTIENE EL RESULTADO
                            $data = json_decode($result);
                            //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                            if ($data->status == false){
                                //SE OBTIENE LA INSTANCIA DE CORNELIO
                                $client_id = env('CLIENT_ID');
                                $instance = env('INSTANCE');
                                if($contacto || $contacto != 0){
                                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                                }
                                elseif($group_id || $group_id != 0){
                                    $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$messageLink&type=text";
                                }
                                //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                                $result = file_get_contents($urlMessage, false, $context);
                            }
                        }
                    }
                }
                else {
                    /***************************  ENVIO DE LA INFORMACION *************************************/
                    //$message = 'No+se+identifica+ning%C3%BAn+tema+para+' . $name . '';
                    $message = 'No+se+identifica+ning%C3%BAn+tema+de+' . $name . '+dentro+del+top10+nacional';
                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                    }
                    //SE REALIZA EL ENVIO
                    $result = file_get_contents($urlMessage, false, $context);
                    //SE OBTIENE EL RESULTADO
                    $data = json_decode($result);
                    //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                    if ($data->status == false){
                        //SE OBTIENE LA INSTANCIA DE CORNELIO
                        $client_id = env('CLIENT_ID');
                        $instance = env('INSTANCE');
                        if($contacto || $contacto != 0){
                            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                        }
                        elseif($group_id || $group_id != 0){
                            $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }
                        //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCIA DE CORNELIO
                        $result = file_get_contents($urlMessage, false, $context);
                    }
                }
            }
            else {
                //$message = 'No+se+identifica+ning%C3%BAn+tema+para+' . $name . '';
                $message = 'No+se+identifica+ning%C3%BAn+tema+de+' . $name . '+dentro+del+top10+nacional';
                if($contacto || $contacto != 0){
                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                }
                elseif($group_id || $group_id != 0){
                    $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                }
                //SE REALIZA EL ENVIO
                $result = file_get_contents($urlMessage, false, $context);
                //SE OBTIENE EL RESULTADO
                $data = json_decode($result);
                //SE CONSULTA SI EL ESTATUS ES FALSO PARA REALIZAR EL ENVIO DE DATOS CON LA INSTANCIA DE CORNELIO
                if ($data->status == false){
                    //SE OBTIENE LA INSTANCIA DE CORNELIO
                    $client_id = env('CLIENT_ID');
                    $instance = env('INSTANCE');
                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                    }
                    //SE REALIZA EL ENVIO DE INFORMACION CON LA INSTANCE DE CORNELIO
                    $result = file_get_contents($urlMessage, false, $context);
                }
            }
        }
    }
}
