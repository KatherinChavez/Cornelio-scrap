<?php

namespace App\Console\Commands;

use App\Models\Checkup;
use App\Models\Cron;
use App\Models\Post;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CronScrap extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:CronScrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //Muestra las paginas que se desea ejecutar
//        $cron = Cron::where('scraps.status', 1)
//            ->join('scraps','scraps.page_id','=','cron_pages.page_id')
//            ->get();

        $cron = Cron::with(['page'=> function ($q) {
            $q->where('scraps.status', 1);}])
            ->get();
        foreach ($cron as $pages){
            if(isset($pages->page) && $pages->page->status == 1) {
                //Tiempo que se debe ejecutar el scrap
                $time = $pages->timePost; // El tiempo que se registra es en minutos
                $page = $pages->page_id;
                $now = Carbon::now()->subMinutes($time);

                //Se pregunta por el ultimo scrap y se valida si debe de realizar scrap
                $list = Checkup::where('page_id', $page)
                    ->where('updated_Post', '>', $now)
                    ->first();
                if (!$list) {
                    $array = [];
                    $array[] = $pages;
                    $this->PostFB($array);
                }
            }
        }
        dd('Finalizado');
    }
}
