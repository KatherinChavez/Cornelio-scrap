<?php

namespace App\Http\Controllers\Cornelio\Report;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Megacategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Helper\Helper;

class ReportMegacategoryController extends Controller
{
    public function ReportMegacategory(){
        $company = session('company');
        $user_id = Auth::id();
        $compain = Company::where('slug', $company)->first();
        $companies = $compain->id;
        //$Megacategoria = Megacategory::where('company_id', $companies)->pluck('name', 'id');
        $contenido = Category::where('company_id', $companies)->pluck('name', 'id');
        //return view('Cornelio.Report.Megacategory.ReportMegacategory',compact('company', 'Megacategoria'));
        return view('Cornelio.Report.Megacategory.ReportMegacategory',compact('company', 'contenido'));
    }

    public function getReportMegacategory($idE,$startE,$endE){
        $id=base64_decode($idE);//Es el id del contenido
        $start=base64_decode($startE);
        $end=base64_decode($endE);

        $categorias=category::where('id','=',$id)->first();
        $scrap = Scraps::where('categoria_id', $categorias->id)->get();
        $start_time = ($start != "") ? $start : Carbon::now()->subDays(1);
        $end_time = ($end != "") ? $end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts=[];
        $fechas=array('start'=>$start,'end'=>$end);
        $adjunto="";
        $publicaciones="";
        foreach ($scrap as $subcategoriaData){
            $subcategoria=$subcategoriaData['id'];
            $categoria= category::where('id','=',$id)->select('name', 'id')->first();
            $sub=array('sub'=>$categoria['name'],'id'=>$categoria['id']);
            $post=Classification_Category::where('classification_category.page_id','=',$subcategoriaData->page_id)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->with(['post','subcategory','attachment','page'])
                ->distinct('post_id')
                ->get()
                ->toArray();

            if(array_key_exists(0, $post)){
                $post[0]=array_merge($post[0],$sub);
            }
            $posts= array_merge($posts,$post);
        }

        $i=0;
        foreach ($posts as $post){
            $total=0;
            $post_id=$post['post_id'];
            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?$total=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:$total=0;

            $coments=Comment::where('post_id','=',$post_id)->count();
            $image="";
            $imagen="";
            $video="";
            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;
            $i++;
            if(isset($post['attachment']['picture'])){
                $image=str_replace("AND", "&", $post['attachment']['picture']);
                $adjunto=$imagen;
                if(isset($post['attachment']['url'])){
                }
            }
            if(isset($post['attachment']['video'])){
                $video=
                $image=str_replace("AND", "&", $post['attachment']['picture']);
            }
        }
        $data=array('posts'=>$posts,'contenido'=>$categorias,'fechas'=>$fechas,'megacategoria_id'=>$id);
        return view('Cornelio.Report.Megacategory.getMegacategory',$data);
    }

    public function reportCloudComments(Request $request){
        $data=[];

        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now()->addDay(1);


        $subcategoria=$request->sub;
//        $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
//            ->whereBetween('classification_category.created_at',[$start_time,$end_time])
//            ->join('posts','posts.post_id','=','classification_category.post_id')
//            ->join('comments','comments.post_id','=','posts.post_id')
//            ->select('comments.comment')
//            ->get();
//
//        foreach($post as $comments){
//            $getComentario = $comments['comment'];
//            $arrayPalabra =array(" de ", "hola", " paso "," de ", " q ", " con ", " qué ",  " que ", "que ", " porque ", " lo ", " estas ", " del ", "¡", "!", " estos ", " la ", " este ",  " te ", " tu ",
//                " Él ", " por ", "tienen", " como ", " cómo ", " y ", " un ", " una ", " más ", " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ",
//                " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " es ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", "tener", " esta ", " ahi ", " ahí ",
//                " ja", " je", " les "," buen ", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ", " bien ", ", ", " tienen  ", "tiene",
//                " cada ", " solo "," nada "," ellos "," ellas "," cada "," sobre "," bajo "," desde "," sobre "," ante "," entre "," tras "," pro " , " vamos", " sus ",
//                " según "," segun "," hacia ", " cabe ", " tras ", " jaja ", " jeje ", " ja ", " el ", " el, ", " donde, ", " donde ", " ver ", "así ",  " así ", " Asi ", " le ",
//                " buenos ", " buenas ",  " dias " , "días",  "cuando", "saludos ", " saludos",  "tener ", " tener ", " varios", "¿","?" , " van ", " algunas", " alguna", " han ", " al ",
//                " tan ", " ya ", " y ", " no ", " si ", " sale ", " he ", " eran ", " fue ", " ve ", " nada ", " ah ", " muy ", " osea ", " sea ", " yo ", " hay ", " eran ", " tantos ",
//                " Y ", "un ", " no ", " mi ", "q ", " nada ", " o ", "como ", " los ", " las "," su", " casi ", " dejan ", " unas ", " vos ", " tu ", " ni ", " a ","ya ", "....", "...",
//                " estos ", " va ", " pueden ", " es ", " pasa ", " pueden ", " despues ", " después ", " estén ", " esté ", " este ", " nuestro ", " quien ", " más ", " nos ");
//            $reemplazar = str_ireplace($arrayPalabra," ", $getComentario);
//            array_push($data,$reemplazar);
//        }
//        return $data;


        $posts=[];
        $adjunto=[];
        $sub=[];
        $i=0;
        $subcategoria=$request->sub;
        $categoria=Subcategory::where('id','=',$subcategoria)->select('name')->first();
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
            ->whereBetween('classification_category.created_at',[$start_time." 00:00:00" , $end_time." 23:59:59"])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('comments','comments.post_id','=','posts.post_id')
            ->select('comments.comment')
            ->orderBy('posts.created_time', 'desc')
            ->get()
            ->toArray();
        return $post;
    }

    public function reportCloudCommentsPost(Request $request){
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now()->addDay(1);

        $posts=[];
        $adjunto=[];
        $sub=[];
        $i=0;
        $subcategoria=$request->sub;
        $categoria=Subcategory::where('id','=',$subcategoria)->select('name')->first();
        $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
            ->whereBetween('classification_category.created_at',[$start_time , $end_time])
            ->join('posts','posts.post_id','=','classification_category.post_id')
            ->join('comments','comments.post_id','=','posts.post_id')
            ->select('comments.comment')
            ->orderBy('posts.created_time', 'desc')
            ->get()
            ->toArray();
        return $post;
    }

    public function ReportImpact(Request $request)
    {
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();

        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $posts = Classification_Category::where('classification_category.subcategoria_id', '=', $request->sub)
            ->whereBetween('classification_category.created_at', [$start_time_for_query, $end_time_for_query])
            ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
            ->get();
        $i = 0;
        foreach ($posts as $post) {

            $total = 0;
            $post_id = $post['post_id'];

            /*************************************** Reacciones *******************************************/
            $totalLinea =0;
            $reacciones=Reaction::where('post_id','=',$post_id)->first();
            $reacciones?$totalLinea=$reacciones['likes']+$reacciones['love']+$reacciones['haha']+$reacciones['wow']+$reacciones['sad']+$reacciones['angry']+$reacciones['shared']:$totalLinea=0;

            /*************************************** Comentarios *******************************************/
            $coments = Comment::where('post_id', '=', $post_id)->count();
            $image = "";
            $imagen = "";
            $video = "";
            $posts[$i]['comentarios'] = $coments;
            $posts[$i]['reacciones'] = $totalLinea;
            $i++;

        }
        $j=0;
        foreach ($posts as $post){
            $value = $post['page_name'];
            $reacciones = $post['comentarios'] + $post['reacciones'];
            if (isset($contar[$value])){
                $contar[$value]['interacciones'] += $reacciones;
                $contar[$value]['publicaciones'] += 1;
                $contar[$value]['nombre'] = $value;
            }else{
                $contar[$value]['interacciones'] = $reacciones;
                $contar[$value]['publicaciones'] = 1;
                $contar[$value]['nombre'] = $value;
            }
        }
        $ordenado=[];
        foreach ($contar as $item){
            $ordenado[$j]=$item;
            $j++;
        }

        return $ordenado;
    }


    public function messageRandom(Request $request){
        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDay(1);
        $i=0;
        if($request->subcategoria_id){
            $posts=Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->get();
        }
        if($request->post_id){
            $comments=Comment::where('post_id','=',$request->post_id)->inRandomOrder()->limit(10)->get();
            $items[$i]['comentarios']=$comments;
            $items[$i]['post']=$request->post_id;
            $i++;
            return $items;
        }
        $items=[];
        foreach ($posts as $post){
            $comments=Comment::where('post_id','=',$post['post_id'])->inRandomOrder()->limit(10)->get();
            $items[$i]['comentarios']=$comments;
            $items[$i]['post']=$post['post_id'];
            $i++;
        }
        return $items;

    }

    public function ReportInteraction(Request $request)
    {
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
        $subcategoria = Subcategory::where('megacategory_id','=', $request->megacategoria_id)->pluck('id');
        $sub=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
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
        $chart['series'][$i]['data']=$newComment;
        $chart['SeriesR'][$i]['data']=$new;

        $i++;
        $chart['fechas']=$date_range;
        return $chart;
    }
}
