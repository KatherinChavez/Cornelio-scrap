<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use App\Models\Post;
use App\Models\Scraps;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Traits\ScrapTrait;

class CommentCompetence extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:CommentCompetence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de los comentarios de publicaciones de las paginas que se encuentra como competencia';

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
        $end_time  =  Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query   = Carbon::parse($end_time)->addDay();

        $scrap = Scraps::where('status', 1)->where('competence', 1)->pluck('page_id');
        $posts = Post::whereIn('page_id', $scrap)
            ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
            ->orderBy('posts.created_time', 'desc')
            ->get();
        $this->CommentsPosts($posts);

        dd("Finalizado");

    }
}
