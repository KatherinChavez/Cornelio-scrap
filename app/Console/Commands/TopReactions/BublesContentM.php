<?php

namespace App\Console\Commands\TopReactions;


use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\Category;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Traits\SendReaction;
use App\Traits\TopicCountTrait;
use Illuminate\Console\Command;
use Carbon\Carbon;

class BublesContentM extends Command
{
    //use TopicCountTrait;
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:BublesContentM';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Burbuja con las palabras de los contenidos al medio dia';

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
        /************************************ ENVIO DE BURBUJA     *****************************************************/
        /* 13 BURBUJA DE LAS 6:40
         * 14 BURBUJA DE LAS 12:30
         * 15 BURBUJA DE LAS 4:30
         * 16 BURBUJA DE LAS 11:40 */

        $report  = 14;
        $numbers = NumberWhatsapp::where('report', $report)->where('company_id', '!=',24)->get();
        $this->topBubles($report, $numbers);
        /*foreach ($numbers as $number){
            $id     = $number->numeroTelefono != 0 ? $number->numeroTelefono : $number->group_id;
            $query  = BitacoraMessageFb::where('number', $id)->whereIn('report', [2,6,10])->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->first();
            if(!$query){
                $send   = [];
                $send[] = $number;
                $this->topBubles($report, $send);
            }
        }*/
        return 200;
        /***************************************************************************************************************/

        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now();

        //$numbers = NumberWhatsapp::where('report', 14)->get();
        $numbers = NumberWhatsapp::where('report', 14)->where('company_id', '!=',24)->get();
        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;

        foreach ($numbers as $number){
            $comp = Company::where('id', $number->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
            if($comp){
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }

            $contacto = $number->numeroTelefono;
            $group_id = $number->group_id;
            //$contacto = '50686258376';

            $name = '';
            $contents_encode = base64_encode("[]");
            $validation = ($number->content == "[]" ? null :  $number->content);
            if($validation != "null" && $validation){
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
            $message = 'Hola+'.$nameUser.'%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+'.$name.'.+'.$link.'+';
            //$message = 'Hola+'.$nameUser.'%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+Medios+de+Comunicaci%C3%B3n%2C+Diputados+y+Grupos+Sindicales.+'.$link.'+';
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