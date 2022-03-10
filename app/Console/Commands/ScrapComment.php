<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapComment extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:scrapComment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'extracción de comentarios de publicaciones clasificadas con antigüedad de 5 a 10 días';

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
        $start_time =  Carbon::now()->subDays(10);
        $end_time =  Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDays(5);

        $posts=Classification_Category::with(['page'=> function ($q) {
            $q->where('scraps.status', 1);}])
            ->whereBetween('created_at',[$start_time_for_query , $end_time_for_query])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($posts as $post){
            if(isset($post->page) && $post->page->status == 1){
                $array=[];
                $array[]=$post;
                $this->CommentsPosts($array);
            }
        }
        //$this->CommentsPosts($posts);

        dd("Finalizado");

    }
}
