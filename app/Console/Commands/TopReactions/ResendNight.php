<?php

namespace App\Console\Commands\TopReactions;

use App\Models\BitacoraMessageFb;
use App\Models\NumberWhatsapp;
use App\Traits\SendReaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;
use SebastianBergmann\CodeCoverage\TestFixture\C;

class ResendNight extends Command
{
    use SendReaction;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ResendNight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se reenvia los mensajes que no han salido en la noche';

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
        /* 4  PDF DE LAS 6:40
         * 8  ANALISIS DE LAS 6:40
         * 12 COMPARADOR DE LAS 6:40
         * 16 BURBUJA DE PALABRAS DE LAS 6:40 */

        $pdf        = $this->resendTopReaction();
        sleep(20);
        $analysis   = $this->resendTopAnalysis();
        sleep(20);
        $comparator = $this->resendTopComparator();
        sleep(20);
        $bubles     = $this->resendTopBubles();
        return 200;
    }

    public function resendTopReaction(){
        $dataPdf = BitacoraMessageFb::where('report', 4)->orderBy('report', 'ASC')->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->get();
        //ENVIO DE TODOS LOS REPORTES PDF QUE HAN FALLADO
        foreach ($dataPdf as $sendPdf){
            $this->sendTopReaction($sendPdf);
        }
        sleep(10);

        //ENVIO DE ANALISIS
        $data = BitacoraMessageFb::where('report', 4)->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->groupBy('number')->get();
        foreach ($data as $sendAnalisis){
            $report  = 8;
            $numbers = $sendAnalisis->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendAnalisis->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendAnalisis->number)->where('report', $report)->get());
            $this->topAnalysis($report, $numbers);
        }
        sleep(10);

        //ENVIO DE COMPARADOR
        foreach ($data as $sendComparator){
            $report  = 12;
            $numbers = $sendComparator->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendComparator->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendComparator->number)->where('report', $report)->get());
            $this->topComparator($report, $numbers);
        }
        sleep(10);

        //ENVIO DE BURBUJA DE PALABRA
        foreach ($data as $sendBuble){
            $report  = 16;
            $numbers = $sendBuble->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendBuble->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendBuble->number)->where('report', $report)->get());
            $this->topBubles($report, $numbers);
        }
    }

    public function resendTopAnalysis(){
        $dataAnalysis = BitacoraMessageFb::where('report', 8)->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->orderBy('report', 'ASC')->get();
        foreach ($dataAnalysis as $sendAnalysis){
            $this->sendTopAnalysis($sendAnalysis);
        }
        sleep(10);

        $data = BitacoraMessageFb::where('report', 8)->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->groupBy('number')->get();
        //ENVIO DE COMPARADOR
        foreach ($data as $sendComparator){
            $report  = 12;
            $numbers = $sendComparator->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendComparator->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendComparator->number)->where('report', $report)->get());
            $this->topComparator($report, $numbers);
        }
        sleep(10);

        //ENVIO DE BURBUJA DE PALABRA
        foreach ($data as $sendBuble){
            $report  = 16;
            $numbers = $sendBuble->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendBuble->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendBuble->number)->where('report', $report)->get());
            $this->topBubles($report, $numbers);
        }
    }

    public function resendTopComparator(){
        $dataComparator = BitacoraMessageFb::where('report', 12)->orderBy('report', 'ASC')->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->get();
        foreach ($dataComparator as $sendComparator){
            $this->sendTopComparator($sendComparator);
        }
        sleep(10);

        $data = BitacoraMessageFb::where('report', 12)->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->groupBy('number')->get();
        //ENVIO DE BURBUJA DE PALABRA
        foreach ($data as $sendBuble){
            $report  = 16;
            $numbers = $sendBuble->type == 'phone' ?
                (NumberWhatsapp::where('numeroTelefono', $sendBuble->number)->where('report', $report)->get()) :
                (NumberWhatsapp::where('group_id',       $sendBuble->number)->where('report', $report)->get());
            $this->topBubles($report, $numbers);
        }
    }

    public function resendTopBubles(){
        $dataBubles = BitacoraMessageFb::where('report', 16)->orderBy('report', 'ASC')->whereBetween('created_at',[Carbon::today()->format("Y-m-d 00:00:00"), Carbon::now()])->get();
        foreach ($dataBubles as $sendBubles){
            $this->sendTopBubles($sendBubles);
        }
    }
}
