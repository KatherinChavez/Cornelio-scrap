<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Traits\ScrapTrait;

class CommentClassification extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:CommentClassification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de los comentarios de publicaciones clasificadas';

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
        $end_time_for_query = Carbon::parse($end_time)->addDay();

        /*$posts=Classification_Category::where('classification_category.company_id','!=',null)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->select('posts.*')
            ->orderBy('posts.created_time', 'desc')
            ->get();*/

        $posts=Classification_Category::orderBy('created_at', 'desc')
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->get();
        $this->CommentsPosts($posts);

        dd("Finalizado");

    }
}
