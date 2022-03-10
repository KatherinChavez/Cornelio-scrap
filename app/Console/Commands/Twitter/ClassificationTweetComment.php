<?php

namespace App\Console\Commands\Twitter;

use App\Models\Classification_Category;
use App\Models\Twitter\TwitterClassification;
use App\Traits\ScrapTweetTrait;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Traits\ScrapTrait;

class ClassificationTweetComment extends Command
{
    use ScrapTweetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClassificationTweetComment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de los comentarios de los tweets que se encuentran clasificado';

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
        $end_time   =  Carbon::now()->addDay(1);

        $tweets = TwitterClassification::join('tweets', 'tweets.id_tweet', 'twitter_classifications.id_tweet')
            ->whereBetween('twitter_classifications.created_at',[$start_time , $end_time])
            ->select('tweets.*')
            ->get();

        foreach ($tweets as $tweet){
            $this->get_Comments($tweet, 20);
        }

    }
}
