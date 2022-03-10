<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Compare;
use App\Models\NetworkData;
use App\Models\Notification;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NetworkInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:NetworkInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registrar los temas que contiene la company, de tal forma sus palabras y la cantidad de publicaciones';

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
        // [COMPANY - TEMA]  [TEMA - PALABRAS]  [PALABAS - PAGINAS] [PAGINAS - COUNT(CANTIDAD DE PLUBLICACION DE LA PAGINA)]

        $start_time =  Carbon::now()->subDays(3);
        $end_time =  Carbon::now()->addDay(1);

        $companies = Company::where('status', 1)->get();

        foreach ($companies as $company){
            $data = array();
            $data_word = array();
            $data_clasifications = array();
            $dataCount = array();

            $i = 0;
            $j = 0;
            $h = 0;
            $k = 0;

            $company_id = $company->id;
            $company_name = $company->slug;

            //Se obtiene los temas que se ha clasificado en las ultimas 72 horas
            $topics = Classification_Category::where('classification_category.company_id', $company->id)
                ->whereBetween('classification_category.created_at', [$start_time , $end_time])
                ->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id')
                ->groupBy('subcategoria_id')
                ->get();
            foreach ($topics as $topic){
                $data[$i] = ["$company_name",  "$topic->name"];
                $i++;

                //Se obtiene las palabras que se ha clasificado
                $words = Classification_Category::whereBetween('classification_category.created_at', [$start_time , $end_time])
                    ->where('classification_category.subcategoria_id', $topic->id)
                    ->join('notification', 'notification.post_id', 'classification_category.post_id')
                    ->join('compare', 'compare.id', 'notification.word')
                    ->groupBy('palabra')
                    ->get();
                foreach ($words as $word){
                    $data_word[$j] = ["$topic->name", "$word->palabra"];
                    $j++;

                    $clasifications = Notification::where('subcategory_id', $topic->id)
                        ->where('word', $word->id)
                        ->whereBetween('created_time',[$start_time , $end_time])
                        ->join('posts', 'posts.post_id', 'notification.post_id')
                        ->join('compare', 'compare.id', 'notification.word')
                        ->groupBy('page_id')
                        ->get();

                    foreach ($clasifications as $clasification){
                        $data_clasifications[$h]= ["$word->palabra", "$clasification->page_name"];
                        $h++;

                        //Obtener la cantidad de publicaciones que contiene la pagina clasificada
                        $count = Classification_Category::where('page_id', $clasification->page_id)->whereBetween('created_at',[$start_time , $end_time])->count();
                        $dataCount[$k] = ["$clasification->page_name", "$count"];
                        $k++;
                    }
                }
            }
            $general = array_merge($data, $data_word, $data_clasifications, $dataCount);
            $encode = json_encode($general);
            $start_day = Carbon::now()->format('Y-m-d 00:00:00');
            $end_day = Carbon::now()->format('Y-m-d 23:59:59');
            $network = NetworkData::where('company_id', $company_id)->where('topic', 0)->whereBetween('created_at', [$start_day, $end_day])->first();
            if(!$network){
                $network = NetworkData::create([
                    'company_id' => $company_id,
                    'company' => $company_name,
                    'topic' => 0,
                    'data' => $encode,
                ]);
            }
            else{
                $network->update([
                    'company_id' => $company_id,
                    'company' => $company_name,
                    'topic' => 0,
                    'data' => $encode,
                ]);
            }
        }
        //dd('fin');
    }
}
