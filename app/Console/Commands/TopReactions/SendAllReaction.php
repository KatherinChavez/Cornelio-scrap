<?php

namespace App\Console\Commands\TopReactions;

use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\NumberWhatsapp;
use App\Traits\SendReaction;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;
use SebastianBergmann\CodeCoverage\TestFixture\C;
use function Symfony\Component\String\u;

class SendAllReaction extends Command
{
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:SendAllReaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia todos los mensajes en cola';

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
       /* 1   ENVIO DE SALUDO
        * 2   ENVIO DE PDF
        * 3   ENVIO DE ANALISIS
        * 4   ENVIO DEL TOP 5
        * 5   ENVIO DE BURBUJA DE PALABRA
        * 99  ENVIO DE ALERTA O NOTIFICACIONES
        * 100 FUE ENVIADO A WAPIAD, PERO AUN NO SE HA RECIBIDO UNA RESPUESTA
        * 500 NO SE ENVIO O SE OBTUVO UN ERROR DEL API*/

       /*primero se envia el saludo y el pdf, si se envio correctamente continua con el siguiente
       debemos de preguntar si el tipo es text o si es pdf
       */

        $this->validationStatus();
        //$start_time =  Carbon::now()->subHour(1);
        $start_time =  Carbon::now()->subMinute(40);
        $end_time   =  Carbon::now();
        $query      = BitacoraMessageFb::where('status', 0)->whereBetween('created_at',[$start_time, $end_time])->get();
        count($query) > 0 ? $this->sendWhatsappMessageReport() : $this->sendMessageReportSms();
    }

    public function validationStatus(){
        $start_time =  Carbon::now()->subHour(4);
        $end_time   =  Carbon::now();
        $query      = BitacoraMessageFb::where('status', 100)->whereBetween('created_at',[$start_time, $end_time])->get();
        foreach ($query as $data){
            if($data->updated_at->diffInMinutes(Carbon::now()) > 20){
                BitacoraMessageFb::where('id', $data->id)->update(['status' => 0]);
            }
        }
    }

    public function sendWhatsappMessageReport()
    {
        $start_time =  Carbon::now()->subHour(2);
        $end_time   =  Carbon::now();

        try{
            /**************************** SE ENVIA LOS MENSAJES QUE SE HAN ALERTADO *************************************/
            $messageAll = BitacoraMessageFb::where('status', 0)->where('report', 99)->whereBetween('created_at',[$start_time, $end_time])->get();
            $n = 0;
            foreach ($messageAll as $sendMessage){
                $status  = 99;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                    //$this->sendMessageReport($sendMessage, $status);
                    $sendData_Alert[$n]=$sendMessage;
                    $n++;
                }
            }
            if(isset($sendData_Alert)){
                foreach ($sendData_Alert as $sendMessage){
                    $status  = 99;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }

            /**************************** SE ENVIA SALUDO DEL PRIMER REPORTE ********************************************/
            $messageAll = BitacoraMessageFb::where('status', 0)->whereIn('report', [1,2,3,4])->where('typeMessage', 'text')->whereBetween('created_at',[$start_time, $end_time])->get();
            $i = 0;
            foreach ($messageAll as $sendMessage){
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', $sendMessage->report)->where('typeMessage', 'text')->where('status', 0)->whereBetween('created_at', [$start_time, $end_time])->first();
                    if ($query) {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                        $sendData_TXT[$i]=$sendMessage;
                        $i++;
                    }
                }
            }
            if(isset($sendData_TXT)){
                foreach ($sendData_TXT as $sendMessage){
                    $status  = 1;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }

            /**************************** SE ENVIA PDF DEL PRIMER REPORTE    ********************************************/
            $messagePDF = BitacoraMessageFb::where('status', 0)->whereIn('report', [1,2,3,4])->where('typeMessage', 'file')->whereBetween('created_at',[$start_time, $end_time])->get();
            $j = 0;
            foreach ($messagePDF as $sendMessage){
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', $sendMessage->report)->where('typeMessage', 'text')->where('status', 1)->whereBetween('created_at', [$start_time, $end_time])->first();
                    if ($query && $sendMessage->status == 0) {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                        $sendData_PDF[$j]=$sendMessage;
                        $j++;
                    }
                }
            }
            if(isset($sendData_PDF)){
                foreach ($sendData_PDF as $sendMessage){
                    $status  = 2;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }

            /**************************** SE ENVIA EL ANALISIS DEL REPORTE   ********************************************/
            $messageAnalysis = BitacoraMessageFb::where('status', 0)->whereIn('report', [5,6,7,8])->whereBetween('created_at',[$start_time, $end_time])->get();
            $k = 0;
            foreach ($messageAnalysis as $sendMessage){
                $status = 3;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    if ($sendMessage->report == 5 && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA SALUDO Y PDF
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 1)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                            }
                        }
                    }
                    if ($sendMessage->report == 6 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 2)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }

                    }
                    if ($sendMessage->report == 7 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 3)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }
                    }
                    if ($sendMessage->report == 8 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 4)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;

                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;

                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }
                    }
                }
            }
            if(isset($sendData)){
                foreach ($sendData as $sendMessage){
                    $status  = 3;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }


            /**************************** SE ENVIA EL TOP 5 DEL REPORTE       *******************************************/
            $messageReport = BitacoraMessageFb::where('status', 0)->whereIn('report', [9,10,11,12])->whereBetween('created_at',[$start_time, $end_time])->get();
            $p = 0;
            foreach ($messageReport as $sendMessage){
                $status = 4;
                if($sendMessage->status != 100 && $sendMessage->status == 0 ) {
                    if ($sendMessage->report == 9  && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 5)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1, 5])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 10 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 6)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2, 6])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 11 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 7)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3, 7])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 12 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 8)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4, 8])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                }
            }
            if(isset($sendData_Analysis)){
                foreach ($sendData_Analysis as $sendMessage){
                    $status  = 4;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }

            /**************************** SE ENVIA LA BURBUJA DE PALABRA      *******************************************/
            $messageBuble = BitacoraMessageFb::where('status', 0)->whereIn('report', [13,14,15,16])->whereBetween('created_at',[$start_time, $end_time])->get();
            $q = 0;
            foreach ($messageBuble as $sendMessage){
                $status = 5;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    if ($sendMessage->report == 13 && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [5, 9])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1, 5, 9])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 14 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [6, 10])->whereIn('status', [3, 4])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2, 6, 10])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 15 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [7, 11])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3, 7, 11])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 16 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [8, 12])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4, 8, 12])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                }
            }
            if(isset($sendData_Buble)){
                foreach ($sendData_Buble as $sendMessage){
                    $status  = 5;
                    $this->sendMessageReport($sendMessage, $status);
                    //sleep(10);
                }
            }

        }catch (\Exception $e){
        }
    }

    public function sendMessageReport($sendMessage, $status){
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $number    = $sendMessage->number;
        $message   = $sendMessage->message;

        if($sendMessage->typeMessage == 'text'){
            if($sendMessage->type == 'phone'){
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$number&message=$message&type=text";
            }
            else if($sendMessage->type == 'group'){
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$number&message=$message&type=text";
            }
        }
        else if($sendMessage->typeMessage == 'file'){
            if($sendMessage->type == 'phone'){
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$number&message=$message&type=file";
            }
            else if($sendMessage->type == 'group'){
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$number&message=$message&type=file";
            }
        }

        //ENVIO DE MENSAJE
        $send = curl_init();
        curl_setopt_array($send, array(
            CURLOPT_URL => $urlMessage,
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
        if (isset($data)) {
            if ($data->status == false) {
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
                $data = json_decode($result);
                if (isset($data)) {
                    if ($data->status == true) {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => $status]);
                    } else if ($data->status == "successfully queued") {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => $status]);
                    } else {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->message, 'status' => 0]);
                    }
                }
            } else if ($data->status == "successfully queued") {
                BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => $status]);
            } else {
                BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => 0]);
            }
        } else {
            $send = curl_init();
            curl_setopt_array($send, array(
                CURLOPT_URL => $urlMessage,
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
            if (isset($data)) {
                if ($data->status == true) {
                    BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => $status]);
                } else if ($data->status == "successfully queued") {
                    BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => $status]);
                } else {
                    BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $data->status, 'status' => 0]);
                }
            } else {
                BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'WhatsApp', 'error' => $result, 'status' => 0]);
            }
        }
    }

    public function sendMessageReportSms()
    {
        $start_time =  Carbon::now()->subHour(3);
        $end_time   =  Carbon::now();

        $api       = ApiWhatsapp::first();
        $key       = $api->key;
        $client_id = $api->client_id;
        $instance  = $api->instance;

        try{
            /**************************** SE ENVIA LOS MENSAJES QUE SE HAN ALERTADO *************************************/
            $messageAll = BitacoraMessageFb::where('status', 0)->where('report', 99)->whereBetween('created_at',[$start_time, $end_time])->get();
            $n = 0;
            foreach ($messageAll as $sendMessage){
                $status  = 99;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                    //$this->sendMessageReport($sendMessage, $status);
                    $sendData_Alert[$n]=$sendMessage;
                    $n++;
                }
            }
            if(isset($sendData_Alert)){
                foreach ($sendData_Alert as $sendMessage){
                    $status = 99;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /**************************** SE ENVIA SALUDO DEL PRIMER REPORTE ********************************************/
            $messageAll = BitacoraMessageFb::where('status', 0)->whereIn('report', [1,2,3,4])->where('typeMessage', 'text')->whereBetween('created_at',[$start_time, $end_time])->get();
            $i = 0;
            foreach ($messageAll as $sendMessage){
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', $sendMessage->report)->where('typeMessage', 'text')->where('status', 0)->whereBetween('created_at', [$start_time, $end_time])->first();
                    if ($query) {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                        $sendData_TXT[$i]=$sendMessage;
                        $i++;
                    }
                }
            }
            if(isset($sendData_TXT)){
                foreach ($sendData_TXT as $sendMessage){
                    $status = 1;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms','error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /**************************** SE ENVIA PDF DEL PRIMER REPORTE    ********************************************/
            $messagePDF = BitacoraMessageFb::where('status', 0)->whereIn('report', [1,2,3,4])->where('typeMessage', 'file')->whereBetween('created_at',[$start_time, $end_time])->get();
            $j = 0;
            foreach ($messagePDF as $sendMessage){
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', $sendMessage->report)->where('typeMessage', 'text')->where('status', 1)->whereBetween('created_at', [$start_time, $end_time])->first();
                    if ($query && $sendMessage->status == 0) {
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                        $sendData_PDF[$j]=$sendMessage;
                        $j++;
                    }
                }
            }
            if(isset($sendData_PDF)){
                foreach ($sendData_PDF as $sendMessage){
                    $status  = 2;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /**************************** SE ENVIA EL ANALISIS DEL REPORTE   ********************************************/
            $messageAnalysis = BitacoraMessageFb::where('status', 0)->whereIn('report', [5,6,7,8])->whereBetween('created_at',[$start_time, $end_time])->get();
            $k = 0;
            foreach ($messageAnalysis as $sendMessage){
                $status = 3;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    if ($sendMessage->report == 5 && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA SALUDO Y PDF
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 1)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                            }
                        }
                    }
                    if ($sendMessage->report == 6 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 2)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }

                    }
                    if ($sendMessage->report == 7 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 3)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;
                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }
                    }
                    if ($sendMessage->report == 8 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 4)->where('status', 2)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData[$k]=$sendMessage;
                            $k++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;

                                //$this->sendMessageReport($sendMessage, $status);
                            } else if ($query->status == 2) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData[$k]=$sendMessage;
                                $k++;

                                //$this->sendMessageReport($sendMessage, $status);
                            }
                        }
                    }
                }
            }
            if(isset($sendData)){
                foreach ($sendData as $sendMessage){
                    $status  = 3;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


            /**************************** SE ENVIA EL TOP 5 DEL REPORTE       *******************************************/
            $messageReport = BitacoraMessageFb::where('status', 0)->whereIn('report', [9,10,11,12])->whereBetween('created_at',[$start_time, $end_time])->get();
            $p = 0;
            foreach ($messageReport as $sendMessage){
                $status = 4;
                if($sendMessage->status != 100 && $sendMessage->status == 0 ) {
                    if ($sendMessage->report == 9  && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 5)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                            //$this->sendMessageReport($sendMessage, $status);
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1, 5])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                                //$this->sendMessageReport($sendMessage, $status);
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 10 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 6)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2, 6])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 11 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 7)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3, 7])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                    if ($sendMessage->report == 12 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->where('report', 8)->where('status', 3)->orderBy('id', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Analysis[$p]=$sendMessage;
                            $p++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4, 8])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            } else if (($query->status == 2) || ($query->status == 3)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Analysis[$p]=$sendMessage;
                                $p++;
                            }
                        }
                    }
                }
            }
            if(isset($sendData_Analysis)){
                foreach ($sendData_Analysis as $sendMessage){
                    $status  = 4;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /**************************** SE ENVIA LA BURBUJA DE PALABRA      *******************************************/
            $messageBuble = BitacoraMessageFb::where('status', 0)->whereIn('report', [13,14,15,16])->whereBetween('created_at',[$start_time, $end_time])->get();
            $q = 0;
            foreach ($messageBuble as $sendMessage){
                $status = 5;
                if($sendMessage->status != 100 && $sendMessage->status == 0) {
                    if ($sendMessage->report == 13 && $sendMessage->status == 0) { // REPORTE DE LA MAÑANA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [5, 9])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [1, 5, 9])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 14 && $sendMessage->status == 0) { // REPORTE DE MEDIO DÍA
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [6, 10])->whereIn('status', [3, 4])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [2, 6, 10])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 15 && $sendMessage->status == 0) { // REPORTE DE TARDE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [7, 11])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [3, 7, 11])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                    if ($sendMessage->report == 16 && $sendMessage->status == 0) { // REPORTE DE NOCHE
                        $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [8, 12])->whereIn('status', [3, 4])->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->first();
                        if ($query) {
                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                            //$this->sendMessageReport($sendMessage, $status);
                            $sendData_Buble[$q]=$sendMessage;
                            $q++;
                        } else {
                            $query = BitacoraMessageFb::where('number', $sendMessage->number)->whereIn('report', [4, 8, 12])->orderBy('id', 'desc')->orderBy('report', 'desc')->whereBetween('created_at', [$start_time, $end_time])->latest()->first();
                            if (!$query) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            } else if (($query->status == 2) || ($query->status == 3) || ($query->status == 4)) {
                                BitacoraMessageFb::where('id', $sendMessage->id)->update(['status' => 100]);
                                //$this->sendMessageReport($sendMessage, $status);
                                $sendData_Buble[$q]=$sendMessage;
                                $q++;
                            }
                        }
                    }
                }
            }
            if(isset($sendData_Buble)){
                foreach ($sendData_Buble as $sendMessage){
                    $status  = 5;
                    if($sendMessage->type == 'phone'){
                        $message    = urldecode($sendMessage->message);
                        $status_sms = $this->sendSms($sendMessage->number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                        BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend'=>'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                    }
                    else if($sendMessage->type == 'group'){
                        $group_id = $sendMessage->number;
                        $list     = "https://wapiad.com/api/listmember.php?client_id=$client_id&instance=$instance&group_id=$group_id";
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
                                    if($sendSms->isSuperAdmin == true) {
                                        $api = ApiWhatsapp::first('key');
                                        $key = $api->key;
                                        $messageDecode = urldecode($sendMessage->message);
                                        $number = str_replace('@s.whatsapp.net', '', $sendSms->jid);
                                        if ($key) {
                                            $status_sms = $this->sendSms($number, $messageDecode, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
                                            BitacoraMessageFb::where('id', $sendMessage->id)->update(['typeSend' => 'Sms', 'error' => $status_sms == null ? "successfully" : $status_sms, 'status' => $status_sms == null ? $status : 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }catch (\Exception $e){
        }

    }
}
