<?php

namespace App\Http\Controllers\Cornelio\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helper;

class StatisticsSubCategoryController extends Controller
{
    public function SelectStaticsSubC(){
        $company= session('company');
        $user_id=Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $subcategory = Subcategory::where('company_id', $companies)->pluck('name', 'id') ;
        return view('Cornelio.Statistics.StatisticsSubcategory',compact('company','subcategory'));
    }

    public function getSubC(Request $request){
        //$subcategory = Subcategory::where('user_id', $request->user_id)->get();
        $company= session('company');
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        $subcategory = Subcategory::where('company_id', $companies)->get();
        return $subcategory;
    }

    public function StaticsSubC(Request $request)
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
                    $total=count($sub[$date]);
                    $data_set[$date]=$total;
                    array_push($new, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Subcategoria'][$i]['data']=$new;
            $chart['Subcategoria'][$i]['name']=$sub_name;
            $i++;

            $chart['fechas']=$date_range;
        }


        $chart['status'] = "success";
        return  $chart;


    }

    public function StaticsSubC2(Request $request)
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
        $data_set = [];
        $new=[];
        foreach ($date_range as $date) {
            if(isset($sub[$date])) {
                $total=count($sub[$date]);
                $data_set[$date]=$total;
                array_push($new,$total);

            } else {
                $data_set[$date] = 0;
                array_push($new,0);
            }
        }
        $chart['Subcategoria'][$i]['data']=$new;
        $chart['Subcategoria'][$i]['name']='Subcategoria';
        $i++;
        $chart['fechas']=$date_range;
        return $chart;
    }


}
