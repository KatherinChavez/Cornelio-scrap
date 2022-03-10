<?php

namespace App\Console\Commands;

use App\Models\Checkup;
use App\Models\Cron;
use App\Models\Post;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StatusPage extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:StatusPage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactiva o Activa las paginas';

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
        //Muestra todas las paginas que se encuentra en la tabla
        $pages = Scraps::orderBy('id', 'DESC')->groupby('page_id')->distinct('page_id')->get();

        //Se realizara recorrido por cada una de las paginas encontrada
        foreach ($pages as $page){

            //Se consulta por el tiempo limite que se debe inactivar la pagina
            $limit = Cron::where('page_id', $page->page_id)->pluck('limit_time')->first();
            #10080 hace siete días
            $limit = ($limit != null) ? $limit : 10080;

            //Se obtiene el tiempo limite que desea consultar
            $time = Carbon::now()->subMinute($limit);

            //Se consulta por la ultima publicacion
            $post = Post::where('page_id', $page->page_id)->where('created_time','>',$time)->first();

            if($post){
                Scraps::where('page_id', $page->page_id)->update(['status'=> 1]);
            }
            else{
                Scraps::where('page_id', $page->page_id)->update(['status'=> 0]);
            }
        }
    }
}
