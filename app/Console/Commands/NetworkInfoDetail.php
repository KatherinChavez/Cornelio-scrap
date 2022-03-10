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

class NetworkInfoDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:NetworkInfoDetail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se obtiene los datos que contiene cada uno de los temas';

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

        $companies = Company::/*where('id', 15)->*/where('status', 1)->get();
        foreach ($companies as $company){
            $company_id = $company->id;
            $company_name = $company->slug;
            $result = array();
            $h = 0;

            //Se obtiene el tema clasificado en las ultimas 72
            $topics = Classification_Category::where('classification_category.company_id', $company_id)
                ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id')
                ->groupBy('subcategoria_id')
                ->get();

            foreach ($topics as $topic){
                $data = array();
                $data_word = array();
                $dataCount = array();

                $i = 0;
                $j = 0;
                $k = 0;

                //Se obtiene las paginas clasificadas que contiene el tema
                $clasifications = Classification_Category::where('subcategoria_id', $topic->subcategoria_id)
                    ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                    ->join('posts', 'posts.post_id', 'classification_category.post_id')
                    ->groupBy('classification_category.page_id')
                    ->get();

                foreach($clasifications as $clasification){
                    $data[$i] = ["$topic->name",  "$clasification->page_name"];
                    $i++;

                    //Se obtiene las palabras que contiene la pagina
                    $words = Classification_Category::where('classification_category.subcategoria_id', $topic->subcategoria_id)
                        ->where('page_id', $clasification->page_id)
                        ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                        ->join('notification', 'notification.post_id', 'classification_category.post_id')
                        ->join('compare', 'compare.id', 'notification.word')
                        ->groupBy('classification_category.page_id')
                        ->get();

                    foreach ($words as $word){
                        $data_word[$j] = ["$clasification->page_name", "$word->palabra"];
                        $j++;

                        //Se obtiene la cantidad de palabra que se encontro clasificada
                        $count = Notification::where('word', $word->word)
                            ->whereBetween('created_at',[$start_time , $end_time])
                            ->count();
                        $dataCount[$k] = ["$word->palabra", "$count"];
                        $k++;

                    }


                }
                $general = array_merge($data, $data_word, $dataCount);
                $result['tema'][$h]['name'] = $topic->name;
                $result['tema'][$h]['chart'] = $general;
                $h++;
            }

            $encode = json_encode($result);
            $start_day = Carbon::now()->format('Y-m-d 00:00:00');
            $end_day = Carbon::now()->format('Y-m-d 23:59:59');
            $network = NetworkData::where('company_id', $company_id)->where('topic', 1)->whereBetween('created_at', [$start_day, $end_day])->first();
            if(!$network){
                $network = NetworkData::create([
                    'company_id' => $company_id,
                    'company' => $company_name,
                    'topic' => 1,
                    'data' => $encode,
                ]);
            }
            else{
                $network->update([
                    'company_id' => $company_id,
                    'company' => $company_name,
                    'topic' => 1,
                    'data' => $encode,
                ]);
            }
        }
        //dd('fin');
    }
}
