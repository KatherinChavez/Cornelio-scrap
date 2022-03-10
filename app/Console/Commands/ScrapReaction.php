<?php

namespace App\Console\Commands;

use App\Models\Checkup;
use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Cron;
use App\Models\Post;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapReaction extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapReaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scrap las reacciones de los comentarios';

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
        //antes reaccionesClasificados
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now();

        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDays(1);

        $companies=Company::where('status',1)->get();
        foreach ($companies as $company){
            $posts=Classification_Category::where('classification_category.company_id',$company->id)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->join('scraps','scraps.page_id','classification_category.page_id')
                ->groupBy('classification_category.post_id')
                ->orderBy('classification_category.created_at', 'desc')
                ->select('classification_category.*', 'scraps.page_name')
                ->get();
            foreach ($posts as $page) {
                $cron_page = Cron::where('page_id', $page->page_id)->first();

                $time = $cron_page->timeReaction; // El tiempo que se registra es en minutos
                $now = Carbon::now()->subMinutes($time);

                $list = Checkup::where('page_id',$page->page_id)
                    ->where('updated_Reaction', '>', $now)
                    ->first();

                if(!$list){
                    $array=[];
                    $array[]=$page;
                    $this->ReactionsPosts($array);
                }
            }
        }


    }
}
