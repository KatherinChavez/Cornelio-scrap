<?php

namespace App\Console\Commands;

use App\Models\ApiWhatsapp;
use App\Models\Bitacora;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClearReview extends Command
{
    use SendSmsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClearReview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los datos que se encuentra en la bitacora api ';

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
        $start = Carbon::now()->subMonth(1)->format('Y-m-d H:i:s');
        $end = Carbon::now()->format('Y-m-d H:i:s');

        $disconnected = Bitacora::whereBetween('created_at',[$start , $end])
            ->where('message', 'Desconectado')
            ->count();

        $connected = Bitacora::whereBetween('created_at',[$start , $end])
            ->where('message', 'Conectado')
            ->count();

        $numbers = ['50688333737','50686258376', '50661418599'];
        //$numbers = ['50686258376', '50661418599'];

        $message = '! Hola ! Se informa que el sistema ha realizado control del API WAPIAD, que se encuentra registrado en un lapso de tiempo del '
            .$start.' al '.$end.', en el periodo de un mes se encuentra registrado una cantidad de '.$disconnected.' veces que se ha desconectado y una cantidad de '.
            $connected.' veces que se ha encontrado conectado';

        $api = ApiWhatsapp::first('key');
        $key = $api->key;

        foreach ($numbers as $number){
            if($key){
                $this->sendSms($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $key);
            }
        }

        $all = Bitacora::whereBetween('created_at',[$start , $end])->get();
        foreach ($all as $api){
            Bitacora::where('id', $api->id)->delete();
        }
    }
}
