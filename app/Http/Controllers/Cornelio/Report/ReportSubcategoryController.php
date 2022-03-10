<?php

namespace App\Http\Controllers\Cornelio\Report;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Compare;
use App\Models\Megacategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use App\Helper\Helper;
use QuickChart;
use Illuminate\Support\Facades\DB;
use Spipu\Html2Pdf\Tag\Html\S;

class ReportSubcategoryController extends Controller
{
    public function ReportSubcategory(){
        return view('Cornelio.Report.Subcategory.ReportSubcategory');
    }

    public function megacategory(Request $request){
        //$mega=Megacategory::where('user_id','=',$request->user_id)->get();
        $company_id = session('company_id');
        $mega=Megacategory::where('company_id','=',$company_id)->get();
        return $mega;
    }

    public function subcategory(Request $request){
        //$sub=Subcategory::where('megacategory_id','=',$request->mega)->get();
        $company_id = session('company_id');
        $sub=Subcategory::where('company_id','=',$company_id)->get();
        return $sub;
    }

    //public function getReportSubcategory($mega,$sub,$start,$end){
    public function getReportSubcategory($sub,$start,$end){
        //$mega=base64_decode($mega);
        $sub=base64_decode($sub);
        $start=base64_decode($start);
        $end=base64_decode($end);
        $fechas=array('start'=>$start,'end'=>$end);
        $sub_info=Subcategory::where('id','=',$sub)->select('id','name')->first();
        $items=[];
        $i=0;
        $start_time = ($start != "") ? $start : Carbon::now()->subDays(7);
        $end_time = ($end != "") ? $end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts=Classification_Category::where('classification_category.subcategoria_id','=',$sub)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title','subcategory.name')
            ->orderBy('posts.created_time', 'desc')
            ->distinct()
            ->get()
            ->toArray();
        $i=0;
        foreach ($posts as $post){
            $total=0;
            $totalLinea = 0;
            $totalConsulta = 0;
            $post_id=$post['post_id'];

            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?
                $total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:
                $total=0;

            $coments=Comment::where('post_id','=',$post_id)->count();
            $image="";
            $imagen="";
            $video="";
            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;
            $i++;
            if($post['picture']){
                $image=str_replace("AND", "&", $post['picture']);
                $adjunto=$imagen;
                if($post['url']){
                }
            }
            if($post['video']){
                $video=
                $image=str_replace("AND", "&", $post['picture']);
            }
        }
        $data=array('posts'=>$posts,'sub'=>$sub_info,'fechas'=>$fechas);
        //$data=array('posts'=>$posts,'sub'=>$sub_info,'mega'=>$mega,'fechas'=>$fechas);
        return view('Cornelio.Report.Subcategory.getReportSubcategory',$data);
    }

    public function chartReportePost(Request $request){

        $interval_type = "Y-m";
        $interval_step = "+1 month";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(7);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = ($request->interval != "") ? $request->interval : "Diario";
        $start_time_for_query = Carbon::parse($start_time)->subDays(1);
        $end_time_for_query = Carbon::parse($end_time)->addMonths(1);

        $chart=array();
        $i=0;
        $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

        $subcategorias =Classification_Category::where('classification_category.megacategoria_id','=',$request->megacategoria_id)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->select('classification_category.subcategoria_id as id')
            ->distinct()
            ->get();

        foreach ($subcategorias as $subcategoria){
            $subs['subcategoria']=$subcategoria['id'];
            $sub=Subcategory::where('id','=',$subcategoria['id'])->get();
            $subcategoria_id=$subcategoria['id'];
            $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();

            $subcategoria_name=$sub[0]->name;

            //$response['subcategoria'][$subcategoria_name]['chart_label'] = $subcategoria_name;

            //$interval_step = "+1 day";
            //$response['xAxis_label'] = "$interval";

            $data_set = [];
            $new=[];
            //$date_range = Helper::date_range($start_time, $end_time, $interval_step, $interval_type);

            foreach ($date_range as $date) {
                if(isset($post[$date])) {
                    $total=count($post[$date]);
                    $data_set[$date]=$total;
                    array_push($new,$total);

                } else {
                    $data_set[$date] = 0;
                    array_push($new,0);
                }
            }
            $response['subcategoria'][$subcategoria_name]['data_set'] = $data_set;
            $chart['series'][$i]['data']=$new;
            $chart['series'][$i]['name']=$subcategoria_name;
            $i++;

        }
        $chart['fechas']=$date_range;
        $response['status'] = "success";
        return $chart;

    }

    public function chartReporteInteraction(Request $request){
        $user_id = Auth::id();
        $interval_type = "Y-m";
        $interval_step = "+1 month";
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(7);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $interval = ($request->interval != "") ? $request->interval : "Diario";
        $start_time_for_query = Carbon::parse($start_time)->subDays(40);
        $end_time_for_query = Carbon::parse($end_time)->addMonths(1);
        if($request->company){
            $company = Company::where('id', $request->company)->first();
            $company_id = $company->id;
        }
        else{
            $company_id = session('company_id');
        }
        $chart=array();
        $i=0;
        $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);
        $subs=[];
        $subcategorias =Classification_Category::where('classification_category.company_id', $company_id)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->select('classification_category.subcategoria_id as id')
            ->distinct()
            ->get();
        $type="Reactions";
        $response = [];
        if( count($subcategorias) < 1 ) {
            return [
                'status' => 'error',
                'message' => 'Please, select a page!',
            ];
        }
        $i=0;

        foreach ($subcategorias as $subcategoria){
            $subs['subcategoria']=$subcategoria['id'];
            $sub=Subcategory::where('id','=',$subcategoria['id'])
                ->get();

            $subcategoria_id=$subcategoria['id'];
            $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria_id)
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->select('posts.*')
                ->distinct()
                ->get()->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); // grouping by months
                })->toArray();

            $subcategoria_name=$sub[0]->name;


            $response['subcategoria'][$subcategoria_name]['chart_label'] = $subcategoria_name;

            $interval_step = "+1 day";
            $response['xAxis_label'] = "$interval";

            $data_set = [];
            $new=[];
            foreach ($date_range as $date) {
                if(isset($post[$date])) {
                    $total=0;
                    foreach ($post[$date] as $posteo) {
                        $post_id= $posteo['post_id'];
                        $reactions=Reaction::where('post_id','=',$post_id)->first();
                        if($reactions == null){
                            $comentarios=Comment::where('post_id','=',$post_id)->count();
                            $total=$total+$comentarios;
                        }else{
                            $like=$reactions['likes'];
                            $wow=$reactions['wow'];
                            $sad=$reactions['sad'];
                            $haha=$reactions['haha'];
                            $angry=$reactions['angry'];
                            $love=$reactions['love'];
                            $shared=$reactions['shared'];
                            $comentarios=Comment::where('post_id','=',$post_id)->count();
                            $total=$total+$like+$wow+$sad+$haha+$angry+$love+$shared+$comentarios;
                        }
                    }
                    $data_set[$date]=$total;
                    array_push($new,$total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new,0);
                }

            }

            $response['subcategoria'][$subcategoria_name]['data_set'] = $data_set;
            $chart['series'][$i]['data']=$new;
            $chart['series'][$i]['name']=$subcategoria_name;
            $i++;

        }
        $chart['fechas']=$date_range;
        $response['status'] = "success";

        return $chart;
    }

    public function PDF_Topics($sub, $start, $end){
        $sub=base64_decode($sub);
        $start=base64_decode($start);
        $end=base64_decode($end);
        $fechas=array('start'=>$start,'end'=>$end);
        $sub_info=Subcategory::where('id','=',$sub)->select('id','name')->first();
        $items=[];
        $i=0;
        $start_time = ($start != "") ? $start : Carbon::now()->subDays(7);
        $end_time = ($end != "") ? $end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts=Classification_Category::where('classification_category.subcategoria_id','=',$sub)
            ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title','subcategory.name')
            ->orderBy('posts.created_time', 'desc')
            ->distinct()
            ->get()
            ->toArray();
        $i=0;
        /**************************************************************************************************************/
        foreach ($posts as $post){
            $total=0;
            $totalLinea = 0;
            $totalConsulta = 0;
            $post_id=$post['post_id'];

            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?
                $total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:
                $total=0;

            $coments=Comment::where('post_id','=',$post_id)->count();
            $random = Comment::where('post_id', '=', $post_id)->inRandomOrder()->limit(10)->get();
            $imagen="";
            $posts[$i]['random']=$random;
            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;
            $i++;
            if($post['picture']){
                $image=str_replace("AND", "&", $post['picture']);
                $adjunto=$imagen;
                if($post['url']){
                }
            }
            if($post['video']){
                $video=
                $image=str_replace("AND", "&", $post['picture']);
            }
        }

        /**************************************************************************************************************/
        $start_date = Carbon::now()->subMonth(5)->toDateString();
        //$end_date = Carbon::now()->tomorrow()->toDateString();
        $end_date = Carbon::now()->addMonth(1)->toDateString();
        $end_time =  Carbon::parse($end_time)->addMonth(1);
        $chart = Classification_Category::where('classification_category.subcategoria_id','=',$sub)
            ->whereBetween('classification_category.created_at',[$start_time , $end_time])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select(DB::raw('count(*) as count'), DB::raw('DATE_FORMAT(classification_category.created_at, "%Y-%m") as date'))
            ->groupBy('date')
            ->get();
        //dd($chart);

        $interval_type = "Y-m";
        $interval_step = "+1 month";
        $date_range = Helper::date_range($start_time, $end_time, $interval_step, $interval_type);
        $globalData = array();
        foreach ($chart as $item) {
            $temporal = ['count' => $item->count, 'date' => $item->date];
            if (key_exists('Global', $globalData)) {
                array_push($globalData['Global'], $temporal);
            } else {
                $globalData['Global'] = [$temporal];
            }
        }
        $globalArray = array();
        foreach ($globalData as $key => $value) {
            $temporal = [];
            foreach ($date_range as $date) {
                $i = in_array($date, array_column($value, 'date'));
                if ($i == true) {
                    $index = array_search($date, array_column($value, 'date'));
                    $temporal[] = $value[$index]['count'];
                } else {
                    $temporal[] = 0;
                }
            }
            $globalArray = $temporal;
        }
        foreach ($date_range as $date) {
            $categories [] = $date;
        }
        $xAxis = $categories;
        $line = new QuickChart(array(
            'width' => 600,
            'height' => 300,
            'backgroundColor' => 'white',
        ));
        $line->setConfig('{
               type: "line",
               data: {labels:' . \GuzzleHttp\json_encode($xAxis) . ',
               datasets:[{label:"' . $sub_info->name . '", data:' . \GuzzleHttp\json_encode($globalArray) . ', fill:false,borderColor:"green"}]}
            }'
        );
        $c = $line->getUrl();

        /**************************************************************************************************************/
        $data=array('posts'=>$posts,'sub'=>$sub_info,'fechas'=>$fechas, 'c'=>$c);


        $pdf = \PDF::loadView('Cornelio.Report.Subcategory.ExportTopics', compact('data','posts', 'sub_info', 'c'));
        return $pdf->download('Reporte de tema.pdf');
    }
}
