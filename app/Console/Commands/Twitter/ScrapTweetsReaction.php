<?php

namespace App\Console\Commands\Twitter;

use App\Models\Company;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterScrap;
use App\Traits\ScrapTweetTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapTweetsReaction extends Command
{
    use ScrapTweetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapTweetsReaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de reacciones de todos los tweets que se encuentran';

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
        $end_time   =  Carbon::now()->addDays(1);
        $scraps     = TwitterScrap::where('status', 1)->get();
        foreach ($scraps as $scrap) {
            $tweets = Tweet::where('author_id', $scrap->page_id)
                            ->whereBetween('created_at',[$start_time , $end_time])
                            ->get();
            foreach ($tweets as $tweet){
                $this->get_reaction($tweet->id_tweet);
            }
        }
    }
}
