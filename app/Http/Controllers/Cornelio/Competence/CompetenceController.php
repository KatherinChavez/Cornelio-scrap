<?php

namespace App\Http\Controllers\Cornelio\Competence;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Cron;
use App\Models\Info_page;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Carbon\Carbon;
use Facebook;
use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    public function index()
    {
        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pages = Scraps::where('competence', 1)->where('company_id', $company_id)->where('status', 1)->with('categories')->paginate();
        $pagePrincipal = Scraps::where('id', $company_page->page)->get();
        $pageGeneral = Scraps::where('company_id', $company_id)->where('competence', 0)->where('status', 1)->get();
        $selectPage = Scraps::where('competence', 0)->where('company_id', $company_id)->where('id','!=', $company_page->page)->where('status', 1)->orderBy('page_name')->pluck('page_name', 'id');
        return view('Cornelio.Competence.index', compact('pages', 'selectPage', 'company_id', 'pagePrincipal', 'pageGeneral'));
    }

    public function store(Request $request){
        //Se clasifica como competencia
        $request->validate([
            'page' => 'required',
            'company_id' => 'required',
        ]);

        $query = Scraps::where('id', $request->page)->where('company_id', $request->company_id)->first();
        if($query){
            $query->update(['competence' =>1]);
            return 200;
        }
        return 501;
    }

    public function delete(Scraps $scraps){
        //Se desclasifica como competencia
        $page = Scraps::where('id', $scraps->id ) ->update(['competence' => 0]);
        return redirect()->route('Competence.index')->with('info', 'La página se desclasificó como competencia ');
    }

    public function cloudWordPage(){
        $company_id = session('company_id');
        $cloudWord = Comment::where('page_id', $company_id)
            ->where('created_time', '>=', Carbon::now()->subHour(24))
            ->get();
        return $cloudWord;
    }

    public function FeelingPageComments(Request $request){
        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);

        //El objeto data por medio de un array obtiene los datos que se desea almacenar
        $data=array();
       //Cuenta la cantidad de veces que realizo consulta
        $i = 0;

        //Se llama la pagina principal de la compania
        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pagePrincipal = Scraps::where('id', $company_page->page)->first();

        //Se obtiene las paginas
        //$pages  = [$request->page_id, $pagePrincipal->page_id];
        $pages = ($pagePrincipal != "") ? [$request->page_id, $pagePrincipal->page_id] : [$request->page_id];

        foreach($pages as $page){
            //Se llama las publicaciones clasificadas de la pagina
            $rating = Comment::where('page_id', $page)
                ->whereBetween('created_time',[$start_time , $end_time])
                ->with('sentiment')
                ->select('comment_id')
                ->get();

            //Se obtiene el nombre de la pagina
            $page_name = Post::where('page_id', $page)->first();
            $interation = 0;
            $positivo = 0;
            $negativo = 0;
            $neutral = 0;

            foreach ($rating as $comment){
                if(isset($comment['sentiment'])){
                    if($comment['sentiment']->sentiment == 'Positivo'){
                        $positivo++;
                    }
                    elseif ($comment['sentiment']->sentiment == 'Negativo'){
                        $negativo++;
                    }
                    elseif ($comment['sentiment']->sentiment == 'Neutral'){
                        $neutral++;
                    }
                    $interation++;
                }
            }
            $data['Page'][$i]['Name']=$page_name->page_name;
            $data['Page'][$i]['negativo']=$interation ? round((($negativo / $interation) * 100),2) : $interation;
            $data['Page'][$i]['neutral']=$interation ? round((($neutral / $interation) * 100),2) : $interation;
            $data['Page'][$i]['positivo']=$interation ? round((($positivo / $interation) * 100),2) : $interation;
            $i++;
        }


        return $data;
    }

    public function StaticsPost(Request $request)
    {
        $items = null;
        $chart=array();
        $i=0;

        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pagePrincipal = Scraps::where('id', $company_page->page)->first();
        $pages = ($pagePrincipal != "") ? [$request->page_id, $pagePrincipal->page_id] : [$request->page_id];
        foreach ($pages as $page){
            $interval_type = "Y-m";
            $interval_step = "+1 month";
            $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(30);
            $end_time = ($request->end != "") ? $request->end : Carbon::now();
            $interval = ($request->interval != "") ? $request->interval : "Diario";

            if ($interval === "Diario") {
                $interval_type = "Y-m-d";
                $interval_step = "+1 day";
                $start_time_for_query = Carbon::parse($start_time)->subDays(0);
                $end_time_for_query = Carbon::parse($end_time)->addDays(1);
            }

            $date_range = Helper::date_range($start_time_for_query, $end_time_for_query, $interval_step, $interval_type);

            /*----------------------------------------------- POSTS -----------------------------------------------*/
            $posts = Post::where('page_id', $page)
                ->distinct('post_id')
                ->whereBetween('posts.created_time',[$start_time_for_query , $end_time_for_query])
                ->get()
                ->groupBy(function($date) use ($interval_type)  {
                    return Carbon::parse($date->created_time)->format("".$interval_type); })
                ->toArray();

            $page_name = Post::where('page_id', $page)->first();
            $data_set = [];
            $new=[];
            foreach ($date_range as $date) {
                if (isset($posts[$date])) {
                    $total=count($posts[$date]);
                    $data_set[$date]=$total;
                    array_push($new, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Page'][$i]['data']=$new;
            $chart['Page'][$i]['name']=$page_name->page_name;
            $chart['fechas']=$date_range;
            $chart['status'] = "success";
            $i++;
        }
        return  $chart;
    }

    public function StaticsComments(Request $request){
        $items = null;
        $chart=array();
        $i=0;

        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pagePrincipal = Scraps::where('id', $company_page->page)->first();
        $pages = ($pagePrincipal != "") ? [$request->page_id, $pagePrincipal->page_id] : [$request->page_id];
        foreach ($pages as $page) {
            $interval_type = "Y-m";
            $interval_step = "+1 month";
            $start_time = ($request->start != "") ? $request->start : Carbon::now()->subDays(30);
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

            /*----------------------------------------------- POSTS -----------------------------------------------*/
            $commets = Comment::where('page_id', $page)
                ->distinct('post_id')
                ->whereBetween('created_time',[$start_time_for_query , $end_time_for_query])
                ->get()
                ->groupBy(function ($date) use ($interval_type) {
                    return Carbon::parse($date->created_time)->format("" . $interval_type);
                })
                ->toArray();

            $page_name = Post::where('page_id', $page)->first();
            $data_set = [];
            $new = [];
            foreach ($date_range as $date) {
                if (isset($commets[$date])) {
                    $total = count($commets[$date]);
                    $data_set[$date] = $total;
                    array_push($new, $total);
                } else {
                    $data_set[$date] = 0;
                    array_push($new, 0);
                }
            }
            $chart['Comments'][$i]['data'] = $new;
            $chart['Comments'][$i]['name'] = $page_name->page_name;
            $chart['fechas'] = $date_range;
            $chart['status'] = "success";
            $i++;
        }
        return  $chart;
    }

    public function TopPagePost(Request $request)
    {
        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pagePrincipal = Scraps::where('id', $company_page->page)->first();

        $pages = ($pagePrincipal != "") ? [$request->page_id, $pagePrincipal->page_id] : [$request->page_id];

        $start_time = ($request->start != "") ? $request->start : Carbon::now()->subMonth(1);
        $end_time = ($request->end != "") ? $request->end : Carbon::now();

        $start_time_for_query = Carbon::parse($start_time)->subDay(0);
        $end_time_for_query = Carbon::parse($end_time)->addDay(2);

        $topData = array();
        $j = 0;

        foreach ($pages as $page) {
            $page_id = $page;
            $page_nameSQL = Page::Select('page_name')->where('page_id', '=', $page_id)->first();
            $page_name = $page_nameSQL['page_name'];
            $response['pages'][$page]['name'] = $page_name;
            $items = Post::where('posts.page_id', '=', $page_id)
                ->whereBetween('posts.created_time', [$start_time_for_query, $end_time_for_query])
                ->with('attachment')
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

            $keys= array_column($data,'count');
            array_multisort($keys, SORT_DESC, $data);
            $resultado = array_slice($data, 0, 10);

            $i = 0;
            foreach ($resultado as $post){
                $info = Post::where('post_id', $post['posteo'])->with('attachment')->first();
                $topData[$i]['name']=$info->page_name;
                $topData[$i]['content']=$info->content;
                $topData[$i]['date']=$info->created_time;
                $topData[$i]['count']=$post['count'];
                if(isset($info['attachment']['picture'])){
                    $topData[$i]['picture']=$info['attachment']['picture'];
                }
                if(isset($info['attachment']['url'])){
                    $topData[$i]['url']=$info['attachment']['url'];
                    $topData[$i]['title']=$info['attachment']['title'];
                }
                $i++;
            }
            $arrayTop[$j] = ['name' => $page_name, 'data' => $topData];
            $j++;
        }
        return $arrayTop;
    }

    public function getInformation(Request $request){
        $company_id = session('company_id');
        $company_page = Company::where('id', $company_id)->first();
        $pagePrincipal = Scraps::where('id', $company_page->page)->first();
        $pages = ($pagePrincipal != "") ? [$request->page_id, $pagePrincipal->page_id] : [$request->page_id];

        foreach ($pages as $page){
            $page_id = $page;
            $app = Cron::where('page_id', $page)
                ->join('apps_fb', 'apps_fb.id', 'cron_pages.id_appPost')
                ->first();
            $app_id = $app->app_fb_id;
            $app_secret = base64_decode($app->app_fb_secret);
            $config=array(
                'app_id' => "$app_id",
                'app_secret' => $app_secret,
                'default_graph_version' => env('APP_FB_VERSION')
            );

            $parametros='?fields=picture,fan_count,category,about,company_overview,location,phone,emails,talking_about_count,name';
            $fb = new Facebook\Facebook($config);
            $token= ($app->app_fb_id."|".base64_decode($app->app_fb_secret));
            try {
                $infoPage= $fb->get('/' . $page_id . '' . $parametros . '', $token);
                $estado = True;
            }
            catch (Facebook\Exceptions\FacebookResponseException $e){
                $estado=False;
            }
            catch(Facebook\Exceptions\FacebookSDKException $e) {
                $estado=False;
            }
            if ($estado == True){
                $data = $infoPage->getDecodedBody();
                $page_name = $data['name'];
                $picture = $data['picture']['data']['url'];
                $fan_count = $data['fan_count'];
                $category = $data['category'];
                $about = $data['about'];
                $location = (isset($data['location']))? $data['location']['city'] : '';
                $talking = $data['talking_about_count'];
                if (array_key_exists('emails', $data)) {
                    $emails = $data['emails'][0];
                } else {
                    $emails = null;
                }
                if (array_key_exists('phone', $data)) {
                    $phone = $data['phone'];
                } else {
                    $phone = null;
                }
                if (array_key_exists('company_overview', $data)) {
                    $company_overview = $data['company_overview'];
                } else {
                    $company_overview = null;
                }
                $info = Info_page::where('page_id', '=', $page_id)->first();
                if (!$info) {
                    $info = Info_page::create(['page_id' =>$page_id, 'page_name' => $page_name, 'fan_count' => $fan_count,
                        'category' =>$category, 'about' => $about, 'company_overview' => $company_overview, 'location' => $location,
                        'phone' => $phone, 'emails' => $emails, 'talking' => $talking, 'picture' => $picture]);
                } else {
                    $info->update(['fan_count' => $fan_count, 'category' =>$category, 'about' => $about, 'company_overview' => $company_overview,
                        'location' => $location,'phone' => $phone, 'emails' => $emails, 'talking' => $talking, 'picture' => $picture]);
                }
            }
        }

        $info = Info_page::whereIn('page_id', $pages)->get();
        return $info;
    }
}
