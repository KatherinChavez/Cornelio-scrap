<?php

namespace App\Console\Commands\Twitter;

use App\Models\Company;
use App\Models\Twitter\TwitterScrap;
use App\Traits\ScrapTweetTrait;
use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Console\Command;

class ScrapTweets extends Command
{
    use ScrapTweetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ScrapTweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de publicaciones de Twitter de las compañías';

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
            $pages = TwitterScrap::where('company_id',$company->id)->select('page_id as id', 'username', 'name')->where('status', 1)->get();
            foreach ($pages as $page){
                $this->get_Tweets($page, 20);
            }
        }
        dd("Finalizado");
    }
}
