<?php

namespace App\Console\Commands;

use App\Models\Checkup;
use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Cron;
use App\Models\Post;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapPageReaction extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapPageReaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scrap las reacciones de paginas en especifico';

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
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now();

        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDays(1);

        $pages = Scraps::whereIn('page_id',[142921462922, 265769886798719, 115872105050, 317034895124955])->get();
        foreach ($pages as $pageR){
            $posts=Post::where('page_id', $pageR->page_id)
                ->whereBetween('created_at',[$start_time_for_query , $end_time_for_query])
                ->get();

            foreach ($posts as $page) {
                $cron_page = Cron::where('page_id', $page->page_id)->first();
                $time = $cron_page->timeReaction; // El tiempo que se registra es en minutos
                $now = Carbon::now()->subMinutes($time);

                $list = Checkup::where('page_id',$page->page_id)
                    //->where('updated_Reaction', '<', $now)
                    ->first();

                if($list){

                    $array=[];
                    $array[]=$page;
                    $this->ReactionsPostsGeneral($array);
                }
            }
        }
    }
}
