<?php

namespace App\Console\Commands\Twitter;

use App\Models\Company;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterScrap;
use App\Traits\ScrapTweetTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapTweetsComment extends Command
{
    use ScrapTweetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapTweetsComment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de comentarios de todos los tweets que se encuentran';

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
        $end_time   =  Carbon::now()->addDays(1);

        $tweets = Tweet::where('status', 1)
            ->whereBetween('tweets.created_at',[$start_time , $end_time])
            ->join('twitter_scraps','twitter_scraps.page_id','=','tweets.author_id')
            ->distinct('tweets.id_tweet')
            ->orderBy('tweets.created_at', 'desc')
            ->get();

        foreach ($tweets as $tweet){
            $this->get_Comments($tweet, 100);
        }
        dd("Finalizado");
    }
}
