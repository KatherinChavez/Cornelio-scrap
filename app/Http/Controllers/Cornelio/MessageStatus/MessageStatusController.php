<?php

namespace App\Http\Controllers\Cornelio\MessageStatus;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use Carbon\Carbon;
use Illuminate\Http\Request;


class MessageStatusController extends Controller
{
    public function index(Request $request)
    {
        $start_time =  Carbon::now()->subHour(12);
        $end_time   =  Carbon::now();
        if($request->search){
            $messages = BitacoraMessageFb::whereBetween('created_at',[$start_time, $end_time])
                ->where('number','LIKE','%'.$request->search.'%')
                ->orderBy('created_at', 'desc')
                ->paginate();
        }else {
            $messages = BitacoraMessageFb::whereBetween('bitacora_messagefb.created_at',[$start_time, $end_time])->orderBy('bitacora_messagefb.created_at', 'desc')->paginate();
        }
        return view("Cornelio.MessageStatus.index", compact('messages'));
    }

    public function resend(BitacoraMessageFb $messageFb)
    {
        BitacoraMessageFb::where('id', $messageFb->id)->update(['status' => 100]);
        $api       = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance  = $api->instance;
        $number    = $messageFb->number;
        $message   = $messageFb->message;

        if($messageFb->typeMessage == 'text'){
            if($messageFb->type == 'phone'){
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$number&message=$message&type=text";
            }
            else if($messageFb->type == 'group'){
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$number&message=$message&type=text";
            }
        }
        else if($messageFb->typeMessage == 'file'){
            if($messageFb->type == 'phone'){
                $urlMessage = "https://wapiad.com/api/send.php?client_id=$client_id&instance=$instance&number=$number&message=$message&type=file";
            }
            else if($messageFb->type == 'group'){
                $urlMessage = "https://wapiad.com/api/sendgroupmsg.php?client_id=$client_id&instance=$instance&group_id=$number&message=$message&type=file";
            }
        }

        if($messageFb->report == 1 || $messageFb->report == 2 ||$messageFb->report == 3 ||$messageFb->report == 4){
            $status = $messageFb->typeMessage == 'text' ? 1: 2;
        }else if($messageFb->report == 5 || $messageFb->report == 6 ||$messageFb->report == 7 ||$messageFb->report == 8){
            $status = 3;
        }else if($messageFb->report == 9 || $messageFb->report == 10 ||$messageFb->report == 11 ||$messageFb->report == 12){
            $status = 4;
        }else if($messageFb->report == 13 || $messageFb->report == 14 ||$messageFb->report == 15 ||$messageFb->report == 16){
            $status = 5;
        }else if($messageFb->report == 99){
            $status = 99;
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
                        BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => $status]);
                        return redirect()->route('messageStatus.index')->with('info','Se ha envido exitosamente');
                    } else if ($data->status == "successfully queued") {
                        BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => $status]);
                        return redirect()->route('messageStatus.index')->with('info','Se ha envido exitosamente');
                    } else {
                        BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->message, 'status' => 0]);
                        return redirect()->route('messageStatus.index')->with('info','No se ha envido el mensaje');
                    }
                }
            } else if ($data->status == "successfully queued") {
                BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => $status]);
                return redirect()->route('messageStatus.index')->with('info','Se ha envido exitosamente');
            } else {
                BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => 0]);
                return redirect()->route('messageStatus.index')->with('info','Se ha presentado un error !');
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
                    BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => $status]);
                    return redirect()->route('messageStatus.index')->with('info','Se ha envido exitosamente');
                } else if ($data->status == "successfully queued") {
                    BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => $status]);
                    return redirect()->route('messageStatus.index')->with('info','Se ha envido exitosamente');
                } else {
                    BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $data->status, 'status' => 0]);
                    return redirect()->route('messageStatus.index')->with('info','No se ha envido el mensaje seleccionado');
                }
            } else {
                BitacoraMessageFb::where('id', $messageFb->id)->update(['error' => $result, 'status' => 0]);
                return redirect()->route('messageStatus.index')->with('info','Error !! No se puede enviar el mensaje');
            }
        }
    }
}
