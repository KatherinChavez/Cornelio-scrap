<?php

namespace App\Http\Controllers\Cornelio\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Reaction;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helper;
class StatisticsInteractionController extends Controller
{
    public function SelectStaticsInteraction(){
        $company= session('company');
        $user_id=Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $subcategory = Subcategory::where('company_id', $companies)->orderBy('name')->distinct('id')->pluck('name', 'id') ;
        return view('Cornelio.Statistics.StatisticsInteraction',compact('company','subcategory'));
    }

    public function StaticsInteractionReaction(Request $request)
    {
        $items = null;
        $response = [];

        $subcategorias = $request->subcategoria;
        if( count($subcategorias) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];
        }

        $chart=array();
        $k=0;
        foreach ($subcategorias as $subcategoria) {
            $sub_ids_names = explode("||##||", $subcategoria);
            $sub_id = trim(htmlspecialchars($sub_ids_names[0]));
            $sub_name = trim(htmlspecialchars($sub_ids_names[1]));

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

            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

            $sub=Classification_Category::where('classification_category.subcategoria_id','=',$sub_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();
            $data_set = [];
            $new=[];

            /*----------------------------------------------- REACCION -----------------------------------------------*/

            foreach ($date_range as $date) {
                if (isset($sub[$date])) {
                    foreach ($sub[$date] as $posteo) {
                        $total=0;
                        $post_id= $posteo['post_id'];
                        $reactions=Reaction::where('post_id','=',$post_id)
                            ->select('reaction_classifications.*')
                            ->get();
                        if(count($reactions) > 0){
                            $like=$reactions['0']['likes'];
                            $wow=$reactions['0']['wow'];
                            $sad=$reactions['0']['sad'];
                            $haha=$reactions['0']['haha'];
                            $angry=$reactions['0']['angry'];
                            $love=$reactions['0']['love'];
                            $shared=$reactions['0']['shared'];
                            $total=$total+$like+$wow+$sad+$haha+$angry+$love+$shared;
                        }
                        //$data_set[$date]=$total;
                        //array_push($new,$total);

                    }
                    $data_set[$date]=$total;
                    array_push($new,$total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Reaccion'][$k]['data']=$new;
            $chart['Reaccion'][$k]['name']=$sub_name;
            $k++;

            $chart['fechas']=$date_range;
        }


        $chart['status'] = "success";
        return  $chart;
    }

    public function StaticsInteractionReaction2(Request $request)
    {
        $items = null;
        $response = [];

        $subcategorias = $request->subcategoria;
        if( count($subcategorias) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];
        }

        $chart=array();
        $k=0;
        foreach ($subcategorias as $subcategoria) {
            $sub_ids_names = explode("||##||", $subcategoria);
            $sub_id = trim(htmlspecialchars($sub_ids_names[0]));
            $sub_name = trim(htmlspecialchars($sub_ids_names[1]));

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

            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

            $sub=Classification_Category::where('classification_category.subcategoria_id','=',$sub_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();

            $data_set = [];
            $new=[];

            /*----------------------------------------------- REACCION -----------------------------------------------*/

            foreach ($date_range as $date) {
                if (isset($sub[$date])) {
                    foreach ($sub[$date] as $posteo) {
                        $totalLinea = 0;
                        $totalConsulta = 0;
                        $post_id= $posteo['post_id'];
                        $reactions=Reaction::where('post_id','=',$post_id)
                            ->select('reactions.*')
                            ->get();
                        foreach($reactions as $type){
                            $postReaction= $type['reacciones'];
                            $obj = json_decode($postReaction);
                            foreach ($obj as $i){
                                $totalLinea = $totalLinea+$i;
                            }
                            $totalConsulta = $totalConsulta+$totalLinea;
                            $data_set[$date]=$totalConsulta;
                            array_push($new,$totalConsulta);

                        }
                    }
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Reaccion'][$k]['data']=$new;
            $chart['Reaccion'][$k]['name']=$sub_name;
            $k++;

            $chart['fechas']=$date_range;
        }


        $chart['status'] = "success";
        return  $chart;
    }

    public function StaticsInteractionComment(Request $request)
    {
        $items = null;
        $response = [];

        $subcategorias = $request->subcategoria;
        if( count($subcategorias) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];
        }
        $chart=array();
        $i=0;
        foreach ($subcategorias as $subcategoria) {
            $sub_ids_names = explode("||##||", $subcategoria);
            $sub_id = trim(htmlspecialchars($sub_ids_names[0]));
            $sub_name = trim(htmlspecialchars($sub_ids_names[1]));

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

            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

            /*----------------------------------------------- COMMENT -----------------------------------------------*/
            $sub=Classification_Category::where('classification_category.subcategoria_id','=',$sub_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();
            $data_set = [];
            $new=[];
            foreach ($date_range as $date) {
                if (isset($sub[$date])) {
                    foreach ($sub[$date] as $posteo) {
                        $total = 0;
                        $post_id= $posteo['post_id'];
                        $comentarios=Comment::where('post_id','=',$post_id)->get();
                        $total=$total+count($comentarios);
                        //$data_setC[$date]=$total;
                        //array_push($new,$total);
                    }
                    $data_setC[$date]=$total;
                    array_push($new,$total);

                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Comentarios'][$i]['data']=$new;
            $chart['Comentarios'][$i]['name']=$sub_name;
            $i++;

            $chart['fechas']=$date_range;
        }


        $chart['status'] = "success";
        return  $chart;
    }

    public function StaticsInteraction2(Request $request)
    {
        $interval_type = "Y-m";
        $interval_step = "+1 month";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(7);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = ($request->interval != "") ? $request->interval : "Diario";
        $start_time_for_query = Carbon::parse($start_time)->subDays(1);
        $end_time_for_query = Carbon::parse($end_time)->addMonths(1);
        if( $interval === "Diario" ) {
            $interval_type = "Y-m-d";
            $interval_step = "+1 day";
            $start_time_for_query = Carbon::parse($start_time)->subDays(0);
            $end_time_for_query = Carbon::parse($end_time)->addDays(1);
        }
        if( $interval === "Hora" ) {
            $interval_type = "Y-m-d H";
            $interval_step = "+1 hour";
            $start_time_for_query = Carbon::parse($start_time)->subDays(0);
            $end_time_for_query = Carbon::parse($end_time)->addDays(1);
        }
        $chart=array();
        $i=0;
        $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

        $sub=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria)
                    ->join('posts','posts.post_id','=','classification_category.post_id')
                    ->select('posts.*')
                    ->distinct()
                    ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                    ->get()->groupBy(function($date) use ($interval_type)  {
                        return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                    })->toArray();

        /*----------------------------------------------- REACTION -----------------------------------------------*/

        $data_set = [];
        $new=[];
        foreach ($date_range as $date) {
            if(isset($sub[$date])) {
                foreach ($sub[$date] as $posteo) {
                    $totalLinea = 0;
                    $totalConsulta = 0;
                    $post_id= $posteo['post_id'];
                    $reactions=Reaction::where('post_id','=',$post_id)
                    ->select('reactions.*')
                    ->get();
                    foreach($reactions as $type){
                        $postReaction= $type['reacciones'];
                        $obj = json_decode($postReaction);
                        foreach ($obj as $i){
                            $totalLinea = $totalLinea+$i;
                        }
                        $totalConsulta = $totalConsulta+$totalLinea;
                        $data_set[$date]=$totalConsulta;
                        array_push($new,$totalConsulta);

                    }
                }
            } else {
                $data_set[$date] = 0;
                array_push($new,0);
            }
        }

        /*----------------------------------------------- COMMENT -----------------------------------------------*/

        $data_setC = [];
        $newComment=[];
        foreach ($date_range as $date) {
            if(isset($sub[$date])) {
                foreach ($sub[$date] as $posteo) {
                    $total = 0;
                    $post_id= $posteo['post_id'];
                    $comentarios=Comment::where('post_id','=',$post_id)->get();
                    $total=$total+count($comentarios);
                    $data_setC[$date]=$total;
                    array_push($newComment,$total);
                }
            } else {
                $data_setC[$date] = 0;
                array_push($newComment,0);
            }
        }

        $chart['Comentario'][$i]['data']=$newComment;
        $chart['Reaccion'][$i]['data']=$new;

        $i++;
        $chart['fechas']=$date_range;
        return $chart;
    }

}
