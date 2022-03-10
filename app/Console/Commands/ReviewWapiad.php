<?php

namespace App\Console\Commands;

use App\Models\ApiWhatsapp;
use App\Models\Bitacora;
use App\Models\Company;
use App\Traits\SendSmsTrait;
use Illuminate\Console\Command;


class ReviewWapiad extends Command
{
    use SendSmsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ReviewWapiad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa que Wapiad se encuentra conectado';

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
        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        $url = 'https://wapiad.com/api/checkconnection.php?client_id='.$client_id.'&instance='.$instance.'';
        //$result = file_get_contents($url);
        $send = curl_init();
        curl_setopt_array($send, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        $result = curl_exec($send);
        curl_close($send);
        $data = json_decode($result);
        if(isset($data->status)) {
            if ($data->status == true && $data->status == "connected") {
                Bitacora::create([
                    'message' => 'Conectado',
                    'client_id' => $client_id,
                    'instance' => $instance,
                ]);
                return ' Se encuentra conectado';
            } else {
                //RECONECTA LA INSTACIA
                $conectar = 'https://wapiad.com/api/reconnect.php?client_id=' . $client_id . '&instance=' . $instance . '';
                //$resultC = file_get_contents($conectar);
                $send = curl_init();
                curl_setopt_array($send, array(
                    CURLOPT_URL => $conectar,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));
                $resultC = curl_exec($send);
                curl_close($send);
                $dataC = json_decode($resultC);
                if(isset($dataC->status)) {
                    if ($dataC->status == true && $dataC->message == 'paired') {
                        return ' Se encuentra conectado';
                    } else {
                        $qr = 'https://wapiad.com/api/getqr.php?client_id=' . $client_id . '&instance=' . $instance . '';
                        $result_qr = file_get_contents($qr);

                        $url = 'https://wapiad.com/api/checkconnection.php?client_id=' . $client_id . '&instance=' . $instance . '';
                        $result = file_get_contents($url);
                        $data_qr = json_decode($result);

                        //$data_qr = json_decode($result_qr);
                        if ($data_qr->status == true && $data_qr->message == 'paired') {
                            return ' Se encuentra conectado';
                        } else {
                            Bitacora::create([
                                'message' => 'Desconectado',
                                'client_id' => $client_id,
                                'instance' => $instance,
                            ]);

                            $message = '! Buenas ! Se informa que WAPIAD no se encuentra conectado, por lo tanto, no se puede realizar envío de datos.';
                            $key = $api->key;
                            $numbers = ['50688333737', '50686258376', '50661418599'];
                            //$numbers = ['50686258376', '50661418599'];
                            foreach ($numbers as $number) {
                                if ($key) {
                                    $this->sendSms($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                }
                            }
                        }
                    }
                }
                else{
                    Bitacora::create([
                        'message' => 'Desconectado',
                        'client_id' => $client_id,
                        'instance' => $instance,
                    ]);

                    $message = "! Buenas ! Se informa que WAPIAD no se encuentra conectado, nos indica: $resultC";
                    $key = $api->key;
                    $numbers = ['50688333737', '50686258376', '50661418599'];
                    //$numbers = ['50686258376', '50661418599'];
                    foreach ($numbers as $number) {
                        if ($key) {
                            $this->sendSms($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        }
                    }
                }
            }
        }

//        $companies = Company::where('client_id', '!=', $client_id)->where('instance', '!=', $instance)->where('instance','!=',"0")->where('client_id','!=',"0")->get();
//        foreach ($companies as $companie){
//            $companie_client_id = $companie->client_id;
//            $companie_instance = $companie->instance;
//
//            $url = 'https://wapiad.com/api/checkconnection.php?client_id='.$companie_client_id.'&instance='.$companie_instance.'';
//            $result = file_get_contents($url);
//            $data = json_decode($result);
//
//            if($data->status == true && $data->message == 'paired'){
//                Bitacora::create([
//                    'message' => 'Conectado',
//                    'client_id' => $companie_client_id,
//                    'instance' => $companie_client_id,
//                ]);
//                return ' Se encuentra conectado';
//            }
//            else{
//                //RECONECTA LA INSTACIA
//                $conectar = 'https://wapiad.com/api/reconnect.php?client_id='.$companie_client_id.'&instance='.$companie_client_id.'';
//                $resultC = file_get_contents($conectar);
//                $dataC = json_decode($resultC);
//                if($dataC->status == true && $dataC->message == 'paired'){
//                    return ' Se encuentra conectado';
//                }
//                else{
//                    $qr = 'https://wapiad.com/api/getqr.php?client_id='.$companie_client_id.'&instance='.$companie_instance.'';
//                    $result_qr = file_get_contents($qr);
//
//                    $url = 'https://wapiad.com/api/checkconnection.php?client_id='.$companie_client_id.'&instance='.$companie_instance.'';
//                    $result_qr = file_get_contents($url);
//                    $data_qr = json_decode($result_qr);
//                    if($data_qr->status == true && $data_qr->message == 'paired'){
//                        return ' Se encuentra conectado';
//                    }
//                    else{
//                        Bitacora::create([
//                            'message' => 'Desconectado',
//                            'client_id' => $companie_client_id,
//                            'instance' => $companie_instance,
//                        ]);
//
//                        $message = '! Buenas ! Se informa que la instancia y el identificador de WAPIAD de la compañía '.$companie->nombre.' no se encuentra conectado, por lo tanto, no se puede realizar envío de datos.';
//                        $key = $companie->key;
//
//                        $numbers = [$companie->phone, $companie->phoneOptional];
//                        //$numbers = ['50688333737','50686258376', '50661418399'];
//                        foreach ($numbers as $number){
//                            if($key){
//                                $this->sendSms($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
//                            }
//                        }
//                    }
//                }
//            }
//        }
        return 'Alerta final';
    }
}
