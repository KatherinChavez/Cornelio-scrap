<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Company;
use App\Models\Scraps;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Compare;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Subcategory;
use Carbon\Carbon;
use DB;

class DesclassificationContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:DesclassificationContent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desclasificacion automatica de posteos de un contenido(categoria)';

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

        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now();


        // convierte las fechas para usarlas en consultas
        $start_time_for_query = Carbon::parse($start_time)->format('Y-m-d');
        $end_time_for_query = Carbon::parse($end_time)->addDay()->format('Y-m-d');

        // carga companies
        $companies=Company::where('status',1)->get();
        //recorre las companies
        foreach ($companies as $company){
            //se llama todos los contenidos de company
            $contenido = Category::where('company_id',$company->id)->get();
            $scraps=Scraps::select('page_id')->where('company_id',$company->id)->groupBy('page_id')->get();
            $scraps=$scraps->pluck('page_id');


            /* Se recorre cada una de las publicaciones que se han clasificado */
            $clasificacion = Classification_Category::select(DB::raw('classification_category.*,count(post_id) as count'))
                ->groupby('post_id')
                ->having('count','>',  1)
                ->get();
            foreach ($clasificacion as $desclasificar){
                $select = Classification_Category::where('post_id',$desclasificar->post_id)
                                                ->where('subcategoria_id', '!=',$desclasificar->subcategoria_id)
                                                ->get();

                foreach ($select as $destroy){
                    //Se elimina los duplicados, solo deja clasificado una unica publicacion
                    $destroy->delete();
                }
            }
        }
    }
}
