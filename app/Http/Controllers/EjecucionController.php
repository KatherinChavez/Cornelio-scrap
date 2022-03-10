<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\Category;
use App\Models\Checkup;
use App\Models\Compare;
use App\Models\NetworkData;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Page;
use App\Models\TopReaction;
use App\Traits\TopicCountTrait;
use Aws\Comprehend\ComprehendClient;
use GuzzleHttp\Client;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Cron;
use App\Models\Reaction;
use App\Models\Subcategory;
use App\Models\Word;
use App\Traits\ScrapTrait;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Post;
use App\Models\Scraps;
use Carbon\Carbon;
use Facebook;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use QuickChart;
use function Symfony\Component\String\u;

class  EjecucionController extends Controller
{
    use ScrapTrait;
    use TopicCountTrait;

    public function index()
    {
        $category = Artisan::call('Cornelio:AutoClassification');
        return $category;

    }

    public function ClassificationContent()
    {
        $category = Artisan::call('Cornelio:ClassificationContent');
        return $category;

    }

    public function comment()
    {
        $category = Artisan::call('Cornelio:CommentClassification');
        return $category;
    }

    public function mega()
    {
        $category = Artisan::call('Cornelio:mega_comment');
        return $category;
    }

    public function MonthComments()
    {
        $category = Artisan::call('Cornelio:monthComments');
        return $category;
    }

    public function postS()
    {
        //$category = Artisan::call('Cornelio:posts');
        $category = Artisan::call('Cornelio:ScrapTweetsComment');
        return $category;
    }

    public function diario()
    {
        $category = Artisan::call('Cornelio:ReportDaily');
        return $category;

    }

    public function link()
    {
        $category = Artisan::call('Cornelio:ReportLink');
        return $category;

    }

    public function month()
    {
        $category = Artisan::call('Cornelio:ReportMes');
        return $category;

    }

    public function ScrapComment()
    {
        $category = Artisan::call('Cornelio:scrapComment');
        return $category;

    }

    public function ScrapPost()
    {
        $category = Artisan::call('Cornelio:ScrapPost');
        return $category;

    }

    public function reacciones()
    {
        $category = Artisan::call('Cornelio:ScrapReaction');
        return $category;

    }

    public function count()
    {
        $category = Artisan::call('Topic:count');
        return $category;

    }
    public function countC()
    {
        $category = Artisan::call('Content:count');
        return $category;

    }
    public function inbox()
    {
        $category = Artisan::call('Cornelio:ScrapInbox');
        return $category;

    }

    public function CronScrap()
    {
        $category = Artisan::call('Cornelio:CronScrap');
        return $category;
    }

    public function DesclassificationContent()
    {
        $category = Artisan::call('Cornelio:DesclassificationContent');
        return $category;

    }

    public function alertTopics(){
        $sub = Subcategory::all();
        foreach ($sub as $item) {
            $alerta = Alert::where('subcategory_id',$item->id)->first();
            if(!$alerta){
                $alert = Alert::create([
                    'subcategory_id' => $item->id,
                    'notification' => 0,
                    'report' => 0,
                ]);
            }
        }
    }

    public function ejecucionCron(){
        $scrap = Scraps::where('categoria_id',122)->get(); //categoria
        foreach ($scrap as $page){
            $cron = Cron::where('page_id', '=', $page->page_id)->update(['timePost'=>'10080', 'timeReaction'=>'1440']);
            //$cronApp = Cron::where('page_id', '=', $page->page_id)->update(['id_appPost'=>'16', 'id_appReaction'=>'17']);
        }
        return 'fin';
    }

    public function StatusPage(){
        $category = Artisan::call('Cornelio:StatusPage');
        return $category;
    }

    public function InactivePage(){
        $category = Artisan::call('Cornelio:InactivePage');
        return $category;
    }

    public function pruebaComando(Request $request){
        $category = Artisan::call('Cornelio:ClassificationSentimentPost');
        //$category = Artisan::call('Cornelio:SendAllReaction');
        //$category = Artisan::call('Cornelio:TopReactionT');
        //$category = Artisan::call('Cornelio:TopAnalysisT');
        //$category = Artisan::call('Cornelio:TopComparatorT');
        //$category = Artisan::call('Cornelio:BublesContentM');
        //return $category;
    }
}
