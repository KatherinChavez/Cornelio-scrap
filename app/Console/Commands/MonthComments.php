<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthComments extends Command
{
    use ScrapTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:monthComments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza comentarios de publicaciones clasificadas dentro de un rango de fechas';

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

        $pages=Scraps::where('company_id',env('company_poder'))->get();
        $start_time =  Carbon::now()->subDays(30);
        $end_time =  Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->subDays(10);

        $posts=Classification_Category::whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->CommentsPosts($posts);

        dd("Finalizado");

    }
}
