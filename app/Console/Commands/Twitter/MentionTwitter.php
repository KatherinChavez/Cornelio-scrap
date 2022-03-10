<?php

namespace App\Console\Commands\Twitter;

use App\Models\Category;
use App\Models\Company;
use App\Models\Scraps;
use App\Models\Twitter\Tweet;
use App\Models\Twitter\TwitterClassification;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterNotification;
use App\Models\Twitter\TwitterScrap;
use App\Traits\ScrapTweetTrait;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Compare;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MentionTwitter extends Command
{
    use ScrapTweetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:MentionTwitter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene las menciones de las paginas de Twitter';

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
        // carga companies
        $companies=Company::where('status',1)->get();
        //recorre las companies
        foreach ($companies as $company){
            $pages = TwitterScrap::where('company_id',$company->id)->select('page_id as id', 'username', 'name')->where('status', 1)->get();
            foreach ($pages as $page){
                $this->get_Mentions($page);
            }
        }
        dd('fin');
    }
}
