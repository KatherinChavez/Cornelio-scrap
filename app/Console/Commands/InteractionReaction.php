<?php

namespace App\Console\Commands;

use App\Models\ApiWhatsapp;
use App\Models\BitacoraPDF;
use App\Models\NumberWhatsapp;
use App\Traits\ScrapTrait;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use App\Models\TopReaction;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class InteractionReaction extends Command
{
    use TopicCountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:InteractionReaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene las reacciones con mayor interaccion';

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
        $start_time =  Carbon::now()->subMonth(1);
        //$start_time =  Carbon::now()->subWeek(1);
        $end_time =  Carbon::now();

        $random=substr(md5(mt_rand()), 0, 4);

        //Se llama todos los datos que se encuentra almacenado en la tabla
        $top = TopReaction::orderBy('count', 'DESC')
            ->with(['attachment'])
            ->whereBetween('created_at',[$start_time , $end_time])
            ->distinct('post_id')
            ->get();

        $topData = $top->unique('post_id')->take(20);

        $max = TopReaction::whereBetween('created_at',[$start_time , $end_time])->max('count');

        $content = PDF::loadView('PDF.interactionReaction', compact('topData'))->output();
        $fileName = 'InteractionTop-'.$random.'.pdf';
        $file = Storage::disk('public_interaction')->put($fileName, $content);
        $path = Storage::disk('public_interaction')->path($fileName);

        //Se envia el pdf a los que desea que resibir la informacion al medio dia
        $phones = NumberWhatsapp::where('subcategory_id', null)->groupBy('numeroTelefono')->whereIn('report', [1, 2, 3, 4])->get();
        //$phones = NumberWhatsapp::where('subcategory_id', null)->groupBy('numeroTelefono')->whereIn('id', [71, 72, 73])->whereIn('report', [1, 2, 3, 4])->get();

        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';

        foreach ($phones as $contactoArray) {
            $contacto=$contactoArray->numeroTelefono;
            //$contacto='50686258376';
            $group_id = $contactoArray->group_id;
            $name = $this->eliminar_acentos(str_replace(' ', '+', $contactoArray->descripcion));

            $mensaje = 'Hola+'.$name.'!+Se+informa+que+la+publicaci%C3%B3n+con+m%C3%A1s+interacci%C3%B3n+en+un+mes+cuenta+con+un+total+de+'.$max.'+reacciones.+Adjunto+el+siguiente+resumen+con+las+publicaciones+con+mayor+interacci%C3%B3n+';
            if($contacto || $contacto != 0){
                $urlBody ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$mensaje.'&type=text&=';
                $urlPdf ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.env('APP_URL').'interaction_files/'.$fileName.'&type=image';
            }

            elseif($group_id || $group_id != 0){
                $urlBody = 'https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$mensaje.'&type=file';
                $urlPdf = 'https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.env('APP_URL').'interaction_files/'.$fileName.'&type=file';
            }
            file_get_contents($urlBody);
            file_get_contents($urlPdf);
        }
        BitacoraPDF::create([
            'file' => $fileName,
            'url' => env('APP_URL').'interaction_files/'.$fileName,
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now()
        ]);
        //dd(file_get_contents($urlBody), file_get_contents($urlPdf));
    }
}
