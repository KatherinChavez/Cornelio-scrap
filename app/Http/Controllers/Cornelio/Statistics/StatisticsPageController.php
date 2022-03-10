<?php

namespace App\Http\Controllers\Cornelio\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Conversation;
use App\Models\Page;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helper;

class StatisticsPageController extends Controller
{
    public function SelectStaticsPage(){
        $company= session('company');
        $user_id=Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $page = Page::where('company_id', $companies)->orderBy('page_name')->pluck('page_name', 'page_id') ;
        //return view('Cornelio.Statistics.StatisticsPage',compact('company','page'));
        return view('Cornelio.Statistics.StatisticsPage',compact('company','page'));
    }


    public function StaticsPage(Request $request)
    {
        $pages = $request->page_id;

        if( count($request->page_id) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];

        }
        // dd($pages);
        $chart=array();
        $i=0;
        foreach ($pages as $page) {
            $page_ids_names = explode("||##||", $page);
            $page_id = trim(htmlspecialchars($page_ids_names[0]));
            $page_name = trim(htmlspecialchars($page_ids_names[1]));
            $interval_type = "Y-m";
            $interval_step = "+1 month";
            $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(7);
            $end_time = ($request->end != "") ? $request->end : Carbon::now();
            $interval = ($request->interval != "") ? $request->interval : "Diario";
            $start_time_for_query = Carbon::parse($start_time)->subDays(1);
            $end_time_for_query = Carbon::parse($end_time)->addMonths(1);
            if ($interval === "Diario") {
                $interval_type = "Y-m-d";
                $interval_step = "+1 day";
                $start_time_for_query = Carbon::parse($start_time)->subDays(0);
                $end_time_for_query = Carbon::parse($end_time)->addDays(1);
            }
            if ($interval === "Hora") {
                $interval_type = "Y-m-d H";
                $interval_step = "+1 hour";
                $start_time_for_query = Carbon::parse($start_time)->subDays(0);
                $end_time_for_query = Carbon::parse($end_time)->addDays(1);
            }
            // $chart=array();
            // $i=0;
            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

            /*----------------------------------------------- INBOX -----------------------------------------------*/

            //Muestra todas las coversaciones
            $items = Conversation::select(['id', 'created_time'])
                            ->where('page_id', '=', $page_id)
                            ->whereBetween('created_time', [$start_time_for_query, $end_time_for_query])
                            ->orderBy('updated_at', 'asc')
                            ->get()->groupBy(function ($date) use ($interval_type) {
                                return Carbon::parse($date->updated_at)->format("".$interval_type);
                            })->toArray();

            $data_set = [];
            $newInbox=[];
            foreach ($date_range as $date) {
                if (isset($items[$date])) {
                    $total=count($items[$date]);
                    $data_set[$date]=$total;
                    array_push($newInbox, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($newInbox, 0);
                }
            }

            //Muestra todas las coversaciones del administrador
            $itemsAdmin = Conversation::select(['id', 'created_time'])
                            ->where('page_id', '=', $page_id)
                            ->where('author_id', '=', $page_id)
                            ->whereBetween('created_time', [$start_time_for_query, $end_time_for_query])
                            ->orderBy('updated_at', 'asc')
                            ->get()->groupBy(function ($date) use ($interval_type) {
                                return Carbon::parse($date->updated_at)->format("".$interval_type);
                            })->toArray();
            $data_set = [];
            $newAdmin=[];
            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);
            foreach ($date_range as $date) {
                if (isset($itemsAdmin[$date])) {
                    $total=count($itemsAdmin[$date]);
                    $data_set[$date]=$total;
                    array_push($newAdmin, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($newAdmin, 0);
                }
            }

            //Muestra todas las coversaciones de la audiencia
            $itemsAudience = Conversation::select(['id', 'created_time'])
                            ->where('page_id', '=', $page_id)
                            ->where('author_id', '!=',$page_id)
                            ->whereBetween('created_time', [$start_time_for_query, $end_time_for_query])
                            ->orderBy('updated_at', 'asc')
                            ->get()->groupBy(function ($date) use ($interval_type) {
                                return Carbon::parse($date->updated_at)->format("".$interval_type);
                            })->toArray();
            $data_set = [];
            $newAudience=[];
            foreach ($date_range as $date) {
                if (isset($itemsAudience[$date])) {
                    $total=count($itemsAudience[$date]);
                    $data_set[$date]=$total;
                    array_push($newAudience, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($newAudience, 0);
                }
            }

            /*----------------------------------------------- COMMENT -----------------------------------------------*/

            $comments=Post::select(['comments.id', 'comments.created_time'])
                        ->join('comments', 'posts.post_id', 'comments.post_id')
                        ->where('posts.page_id', '=', $page_id)
                        ->whereBetween('comments.created_time', [$start_time_for_query, $end_time_for_query])
                        ->orderBy('comments.created_time', 'asc')
                        ->get()->groupBy(function ($date) use ($interval_type) {
                            return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                        })->toArray();
            $data_set = [];
            $new=[];

            foreach ($date_range as $date) {
                if (isset($comments[$date])) {
                    $total=count($comments[$date]);
                    $data_set[$date]=$total;
                    array_push($new, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }


            $chart['Inbox'][$i]['data']=$newInbox;
            $chart['Inbox'][$i]['name']='Inbox';
            $chart['Admin'][$i]['data']=$newAdmin;
            $chart['Admin'][$i]['name']='Administrador';
            $chart['Audience'][$i]['data']=$newAudience;
            $chart['Audience'][$i]['name']='Audiencia';
            $chart['Comment'][$i]['data']=$new;
            $chart['Comment'][$i]['name']=$page_name;
            $chart['fechas']=$date_range;
            $chart['status'] = "success";

            $i++;
        }
        return $chart;
    }

}
