<?php

namespace App\Http\Controllers\Cornelio\Comparator;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Fan;
use App\Models\Info_page;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComparatorController extends Controller
{
    public function ComparatorPage()
    {
        return view('Cornelio.Comparator.ComparatorPage');
        //return view('Cornelio.Comparator.prueba');

    }

    public function CheckPage(Request $request)
    {
        //Valida si la pagina se encuentra en ;a base de datso
        $pagina1 = $request->pagina1;
        $pagina2 = $request->pagina2;
        $error = [];
        $scrap1 = Scraps::where('page_id', '=', $pagina1)->first();
        $scrap2 = Scraps::where('page_id', '=', $pagina2)->first();
        if ($scrap1 === null) {
            $error['status'] = 0;
            $error['pagina'] = $pagina1;
            return $error;
        }
        if ($scrap2 === null) {
            $error['status'] = 0;
            $error['pagina'] = $pagina2;
            return $error;
        }

    }

    public function Fans(Request $request)
    {
        $fan1 = Fan::where('page_id', '=', $request->pagina1)->get();
        $fan2 = Fan::where('page_id', '=', $request->pagina2)->orderBy('fan_count', 'desc')->limit(9)->get();
        $page = [];
        $page[1] = $fan1;
        $page[2] = $fan2;
        return $page;
    }

    public function Talking(Request $request)
    {
        $talking1 = Info_page::where('page_id', '=', $request->pagina1)->select('talking')->first();
        $talking2 = Info_page::where('page_id', '=', $request->pagina2)->select('talking')->first();
        $page = [];
        $page[1] = $talking1;
        $page[2] = $talking2;
        return $page;
    }

    public function DailyPost2(Request $request)
    {
        $pages = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;

        foreach ($pages as $page) {
            $page_id = $page;
            $page_nameSQL = Page::Select('page_name')->where('page_id', '=', $page_id)->first();
            $page_name = $page_nameSQL['page_name'];
            $interaction = "Posts";
            $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
            $end_time = ($request->end != "") ? $request->end : Carbon::now();
            $interval = "Daily";
            $author_type = "=";

            $interval_type = "Y-m";
            if ($interval == "Daily") {
                $interval_type = "Y-m-d";
            } elseif ($interval == "Hourly") {
                $interval_type = "Y-m-d H";
            }
            $start_time_for_query = Carbon::parse($start_time)->subDay(0);
            $end_time_for_query = Carbon::parse($end_time)->addDay(2);


            if ($interaction == "Posts") {
                $items = Post::where('posts.page_id', '=', $page_id)
                    ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                    ->orderBy('posts.created_time', 'asc')
                    ->get()->groupBy(function ($date) use ($interval_type) {
                        return Carbon::parse($date->created_time)->format("" . $interval_type); // grouping by months
                    })->toArray();
                $total = Post::where('page_id', '=', $page_id)
                    ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                    ->count();

                $response['yAxis_label'] = "Post";
                $response['pages'][$page_name]['chart_label'] = $page_name;
                $response['pages'][$page_name]['total'] = $total;
            }

            if ($items == null) {
                continue;
            }

            // set interval step
            $interval_step = "+1 day";
            if ($interval == "Monthly") {
                $interval_step = "+1 month";
                $response['xAxis_label'] = "Month";
            } elseif ($interval == "Daily") {
                $interval_step = "+1 day";
                $response['xAxis_label'] = "Day";
            } elseif ($interval == "Hourly") {
                $interval_step = "+1 hour";
                $response['xAxis_label'] = "Hour";
            }

            // generate date range between start_time and end_time
            if ($interval == "Hourly") {
                $end_time = Carbon::parse($end_time)->addHour(23);
            }
            $date_range = Helper::date_range($start_time, $end_time, $interval_step, $interval_type);

            $data_set = [];

            foreach ($date_range as $date) {
                if (isset($items[$date])) {
                    $data_set[$date] = count($items[$date]);
                } else {
                    $data_set[$date] = 0;
                }
            }
            $response['pages'][$page_name]['data_set'] = $data_set;

        }

        $response['status'] = "success";
        return $response;

    }


    public function DailyPost(Request $request){
        $pages = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;
        $chart=array();
        $i=0;
        foreach ($pages as $page) {
            $page_id = $page;
            $page_name = Scraps::where('page_id', $page_id)->pluck('page_name')->first();
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


            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);


            /*----------------------------------------------- PUBLICATION -----------------------------------------------*/

            $comments=Post::where('posts.page_id', '=', $page_id)
                ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                ->orderBy('posts.created_time', 'asc')
                ->get()->groupBy(function ($date) use ($interval_type) {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();

            $commentsTotal=Post::where('posts.page_id', '=', $page_id)
                ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                ->orderBy('posts.created_time', 'asc')
                ->count();

            $items = Post::where('posts.page_id', '=', $page_id)
                ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                ->orderBy('posts.created_time', 'asc')
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

            $chart['Comment'][$i]['data']=$new;
            $chart['Comment'][$i]['name']=$page_name;
            $chart['Comment'][$i]['total']=$commentsTotal;
            $chart['fechas']=$date_range;
            $chart['status'] = "success";

            $i++;
        }
        return $chart;

    }


    public function Comments(Request $request)
    {
        $pages = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;
        $chart=array();
        $i=0;
        foreach ($pages as $page) {
            $page_id = $page;
            $page_name = Scraps::where('page_id', $page_id)->pluck('page_name')->first();
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

            // $chart=array();
            // $i=0;
            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);



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


            $chart['Comment'][$i]['data']=$new;
            $chart['Comment'][$i]['name']=$page_name;
            $chart['fechas']=$date_range;
            $chart['status'] = "success";

            $i++;
        }
        return $chart;
    }

    public function Comments2(Request $request)
    {
        $pages = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;
        foreach ($pages as $page) {
            $page_id = $page;
            $page_nameSQL = Page::Select('page_name')->where('page_id', '=', $page_id)->first();
            $page_name = $page_nameSQL['page_name'];
            $interaction = "Comments";
            $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
            $end_time = ($request->end != "") ? $request->end : Carbon::now();
            $interval = "Daily";
            $author_type = "=";

            $interval_type = "Y-m";
            if ($interval == "Daily") {
                $interval_type = "Y-m-d";
            } elseif ($interval == "Hourly") {
                $interval_type = "Y-m-d H";
            }

            $start_time_for_query = Carbon::parse($start_time)->subDay(0);
            $end_time_for_query = Carbon::parse($end_time)->addDay(2);


            if ($interaction == "Comments") {
                $items = Post::where('posts.page_id', '=', $page_id)
                    ->join('comments', 'comments.post_id', '=', 'posts.post_id')
                    ->whereBetween('comments.created_time', [$start_time_for_query, $end_time_for_query])
                    ->get()->groupBy(function ($date) use ($interval_type) {
                        return Carbon::parse($date->created_time)->format("" . $interval_type); // grouping by months
                    })->toArray();;
                $total = Post::select(['comments.post_id'])
                    ->join('comments', 'posts.post_id', '=', 'comments.post_id')
                    ->where('posts.page_id', '=', $page_id)
                    ->whereBetween('comments.created_time', [$start_time_for_query, $end_time_for_query])
                    ->count();


                $response['yAxis_label'] = "Comment";
                $response['pages'][$page_name]['chart_label'] = $page_name;
                $response['pages'][$page_name]['total'] = $total;
            }
            if ($items == null) {
                continue;
            }

            // set interval step
            $interval_step = "+1 day";
            if ($interval == "Monthly") {
                $interval_step = "+1 month";
                $response['xAxis_label'] = "Month";
            } elseif ($interval == "Daily") {
                $interval_step = "+1 day";
                $response['xAxis_label'] = "Day";
            } elseif ($interval == "Hourly") {
                $interval_step = "+1 hour";
                $response['xAxis_label'] = "Hour";
            }

            // generate date range between start_time and end_time
            if ($interval == "Hourly") {
                $end_time = Carbon::parse($end_time)->addHour(23);
            }
            $date_range = Helper::date_range($start_time, $end_time, $interval_step, $interval_type);

            $data_set = [];

            foreach ($date_range as $date) {
                if (isset($items[$date])) {
                    $data_set[$date] = count($items[$date]);
                } else {
                    $data_set[$date] = 0;
                }
            }
            $response['pages'][$page_name]['data_set'] = $data_set;

        }

        $response['status'] = "success";
        return $response;
    }

    public function TypePost(Request $request)
    {
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time)->subDay(0);
        $end_time_for_query = Carbon::parse($end_time)->addDay(2);
        $photo1 = Post::where('page_id', '=', $request->pagina1)
            ->where('type', '=', 'Photo')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $video1 = Post::where('page_id', '=', $request->pagina1)
            ->where('type', '=', 'Video')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $link1 = Post::where('page_id', '=', $request->pagina1)
            ->where('type', '=', 'Link')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $status1 = Post::where('page_id', '=', $request->pagina1)
            ->where('type', '=', 'Status')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();

        // Pagina 2
        $photo2 = Post::where('page_id', '=', $request->pagina2)
            ->where('type', '=', 'Photo')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $video2 = Post::where('page_id', '=', $request->pagina2)
            ->where('type', '=', 'Video')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $link2 = Post::where('page_id', '=', $request->pagina2)
            ->where('type', '=', 'Link')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();
        $status2 = Post::where('page_id', '=', $request->pagina2)
            ->where('type', '=', 'Status')
            ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])->count();

        $page = [];
        $page[1]['photo'] = $photo1;
        $page[1]['link'] = $link1;
        $page[1]['video'] = $video1;
        $page[1]['status'] = $status1;
        $page[2]['photo'] = $photo2;
        $page[2]['link'] = $link2;
        $page[2]['video'] = $video2;
        $page[2]['status'] = $status2;

        return $page;
    }

    public function Engagement(Request $request)
    {
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time)->subDay(0);
        $end_time_for_query = Carbon::parse($end_time)->addDay(2);
        $page1 = [];
        $page2 = [];
        $reacciones1 = Reaction::where('page_id', '=', $request->pagina1)
            ->whereBetween('created_at', [$start_time_for_query, $end_time_for_query])
            //->select('reaction_classifications.*')
            ->get();

        $reacciones2 = Reaction::where('page_id', '=', $request->pagina2)
            ->whereBetween('created_at', [$start_time_for_query, $end_time_for_query])
            //->select('reaction_classifications.*')
            ->get();

        $total1 = Post::select(['comments.post_id'])
            ->join('comments', 'posts.post_id', '=', 'comments.post_id')
            ->where('posts.page_id', '=', $request->pagina1)
            ->whereBetween('comments.created_time', [$start_time_for_query, $end_time_for_query])
            ->count();

        $total2 = Post::select(['comments.post_id'])
            ->join('comments', 'posts.post_id', '=', 'comments.post_id')
            ->where('posts.page_id', '=', $request->pagina2)
            ->whereBetween('comments.created_time', [$start_time_for_query, $end_time_for_query])
            ->count();


        $page1['pagina1']['reacciones'] = $reacciones1;
        $page1['pagina1']['comments'] = $total1;
        $page1['pagina2']['reacciones'] = $reacciones2;
        $page1['pagina2']['comments'] = $total2;


        return $page1;
    }

    public function TopPost(Request $request)
    {
        ini_set('memory_limit', '15G');
        $pages = array();
        $data = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;
        $variable = 0;
        $temMayor = [];
        $interaction = "Comments";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = "Daily";
        $author_type = "=";

        $interval_type = "Y-m";
        if ($interval == "Daily") {
            $interval_type = "Y-m-d";
        } elseif ($interval == "Hourly") {
            $interval_type = "Y-m-d H";
        }

        $start_time_for_query = Carbon::parse($start_time)->subDay(0);
        $end_time_for_query = Carbon::parse($end_time)->addDay(2);

        foreach ($pages as $page) {
            $page_id = $page;
            $page_nameSQL = Page::Select('page_name')->where('page_id', '=', $page_id)->first();
            $page_name = $page_nameSQL['page_name'];
            $response['pages'][$page]['name'] = $page_name;

            //if ($interaction == "Comments") {
                $items = Post::where('posts.page_id', '=', $page_id)
                    ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                    ->get();
                $data = [];
                foreach ($items as $item) {
                    $comments = Comment::where('post_id', '=', $item['post_id'])
                        ->whereBetween('created_time', [$start_time_for_query, $end_time_for_query])
                        ->count();
                    $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
                    (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'] + $comments;

                    $data[$item['post_id']]['count'] = $totalLinea;
                    $data[$item['post_id']]['posteo'] = $item['post_id'];
                }
                usort($data, function ($a, $b) {
                    return $b['count'] - $a['count'];
                });

                $response['pages'][$page]['top'] = array_slice($data, 0, 5);
            //}
        }
        $response['status'] = "success";
        return $response;
    }

    public function TopPost1(Request $request)
    {
        ini_set('memory_limit', '15G');
        $pages = array();
        $data = array();
        $pages[1] = $request->pagina1;
        $pages[2] = $request->pagina2;
        $variable = 0;
        $temMayor = [];
        $interaction = "Comments";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = "Daily";
        $author_type = "=";

        $interval_type = "Y-m";
        if ($interval == "Daily") {
            $interval_type = "Y-m-d";
        } elseif ($interval == "Hourly") {
            $interval_type = "Y-m-d H";
        }

        $start_time_for_query = Carbon::parse($start_time)->subDay(0);
        $end_time_for_query = Carbon::parse($end_time)->addDay(2);

        foreach ($pages as $page) {
            $page_id = $page;
            $page_nameSQL = Page::Select('page_name')->where('page_id', '=', $page_id)->first();
            $page_name = $page_nameSQL['page_name'];
            $response['pages'][$page]['name'] = $page_name;

            if ($interaction == "Comments") {
                $items = Post::where('posts.page_id', '=', $page_id)
                    ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                    ->get();
                $data = [];
                foreach ($items as $item) {
                    $comments = Comment::where('post_id', '=', $item['post_id'])
                        ->whereBetween('created_time', [$start_time_for_query, $end_time_for_query])
                        ->count();

                    $reacciones = Reaction::where('post_id', '=', $item['post_id'])->select('reacciones')->get();
                    $totalConsulta = 0;
                    $totalLinea = 0;
                    foreach ($reacciones as $type) {
                        $postReaction = $type['reacciones'];
                        $obj = json_decode($postReaction);

                        foreach ($obj as $j) {
                            $totalLinea = $totalLinea + $j;
                        }
                    }

                    $data[$item['post_id']]['count'] = $totalLinea;
                    $data[$item['post_id']]['posteo'] = $item['post_id'];
                }
                usort($data, function ($a, $b) {
                    return $b['count'] - $a['count'];
                });
                $response['pages'][$page]['top'] = array_slice($data, 0, 5);
            }
        }
        $response['status'] = "success";
        return $response;
    }

    public function getDatos(Request $request)
    {
        $posteos = Post::where('posts.post_id', '=', $request->post_id)
            ->join('attachments', 'posts.post_id', '=', 'attachments.post_id')
            ->select('posts.*', 'attachments.picture', 'attachments.video', 'attachments.url', 'attachments.title')
            ->get();
        return $posteos;
    }


}
