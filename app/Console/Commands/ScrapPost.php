<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Cron;
use App\Models\Post;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapPost extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapPost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de publicaciones de las páginas de las compañías';

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
        $companies=Company::where('status',1)->get();
        foreach ($companies as $company){
            $pages=Scraps::where('company_id',$company->id)->where('status', 1)->get();
            foreach ($pages as $page){
                $cron_page=Cron::where('page_id',$page->page_id)->first();
                if (!$cron_page){
                    $array=[];
                    $array[]=$page;
                    $this->PostFB($array);
                    continue;
                }
                $time = $cron_page->timePost; // El tiempo que se registra es en minutos
                $now = Carbon::now()->subMinutes($time);

                $list = Post::where('page_id',$page->page_id)
                    ->where('created_at', '>=', $now)
                    ->orderBy('created_at', 'DESC')
                    ->first();
                if(!$list){
                    $array=[];
                    $array[]=$page;
                    $this->PostFB($array);
                }
            }
        }
        dd("Finalizado");
    }
}
