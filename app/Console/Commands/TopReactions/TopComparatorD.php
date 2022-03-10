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
use Spipu\Html2Pdf\Tag\Html\Sub;

class TopComparatorD extends Command
{
   // use TopicCountTrait;
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:TopComparatorD';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analiza el top 5 del pdf de las reacciones de dia';

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
        /************************************ ENVIO DE COMPARADOR  *****************************************************/
        /* 9  COMPARADOR DE LAS 6:40
         * 10 COMPARADOR DE LAS 12:30
         * 11 COMPARADOR DE LAS 4:30
         * 12 COMPARADOR DE LAS 11:40 */

        $report  = 9;
        $numbers = NumberWhatsapp::where('report', $report)->get();
        $this->topComparator($report, $numbers);
        /*foreach ($numbers as $number){
            $id     = $number->numeroTelefono != 0 ? $number->numeroTelefono : $number->group_id;
            $query  = BitacoraMessageFb::where('number', $id)->whereIn('report', [1,5])->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->first();
            if(!$query){
                $send   = [];
                $send[] = $number;
                $this->topComparator($report, $send);
            }
        }*/
        return 200;
        /***************************************************************************************************************/

        //Ultimos minutos
        $last = Carbon::now()->subMinute(30);
        //$last = Carbon::now()->subHour(6);
        //Numero de telefono
        $numbers = NumberWhatsapp::where('report', 9)->get();

        //Se llama los datos de cada una de las reacciones que se enviaron por pdf
        $data_pdf = TopReaction::whereBetween('created_at',[$last, Carbon::now()])->take(10)->get();
        //$data_pdf = TopReaction::take(10)->get();

        $i = 0;
        $info = [];
        $arrayId = [];
        $arrayName = [];
        $arrayPosicion = [];

        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;

        foreach($numbers as $number){
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $contacto=$number->numeroTelefono;
            $group_id = $number->group_id;
            //$contacto='50661418599';

            $companie_top = $number->company_id;
            $companie_name = Company::where('id',$number->company_id)->first();
            foreach ($data_pdf as $pdf){
                $companie_pdf = $pdf->company;
                if($companie_top == $companie_pdf){
                    $tema = Subcategory::where('id', $pdf->classification)->first();
                    if($pdf->position  <= 5 ){
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
            if($info != [] || $info != null){
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
                    $message = 'En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+'.$name.'+'.$link.'';

                    /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE LA COMPAÑIA******************************************************/
                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                    }
                    //SE REALIZA EL ENVIO
                    $result = file_get_contents($urlMessage);
                    //SE OBTIENE EL RESULTADO
                    $data = json_decode($result);

                    /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE CORNELIO******************************************************/
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
                        $result = file_get_contents($urlMessage);
                    }
                }
                else{
                    foreach ($uniqueId as $infoPosicion){
                        $sub = base64_encode($infoPosicion);
                        $nameTopics = Subcategory::where('id', $infoPosicion)->first();
                        $name = $this->eliminar_acentos(str_replace(' ', '', $nameTopics->name));
                        $link = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                        $message = 'En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+'.$name.'+'.$link.'';

                        /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE LA COMPAÑIA******************************************************/
                        if($contacto || $contacto != 0){
                            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                        }
                        elseif($group_id || $group_id != 0){
                            $urlMessage ="https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$group_id&message=$message&type=text";
                        }
                        //SE REALIZA EL ENVIO
                        $result = file_get_contents($urlMessage);
                        //SE OBTIENE EL RESULTADO
                        $data = json_decode($result);

                        /********************* ENVIO DE MENSAJE POR MEDIO DE DE LA INSTANCIA DE CORNELIO******************************************************/
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
                            $result = file_get_contents($urlMessage);
                        }
                    }
                }
            }
        }
    }
}
