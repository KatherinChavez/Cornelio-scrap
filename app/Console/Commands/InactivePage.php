<?php

namespace App\Console\Commands;

use App\Models\Checkup;
use App\Models\Cron;
use App\Models\Post;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InactivePage extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:InactivePage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'La funcion va a consultar por las publicaciones de las paginas que se encuentra desactivada';

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
        //Muestra todas las paginas que se encuentran desactivadas
        $pages = Scraps::where('status', 0)->groupby('page_id')->distinct('page_id')->get();

        //Se realizara recorrido por cada una de las paginas que se encuentra desactivado
        foreach ($pages as $page){
            //Se realiza scrap
            $array=[];
            $array[]=$page;
            $this->Post($array);
        }
    }
}
