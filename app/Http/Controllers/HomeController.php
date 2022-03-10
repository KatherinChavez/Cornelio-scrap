<?php

namespace App\Http\Controllers;

use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Compare;
use App\Models\NetworkData;
use App\Models\Notification;
use App\Models\Reaction;
use App\Models\Subcategory;
use App\Models\Top;
use DB;
use App\Models\Company;
use App\Models\Page;
use App\Models\Post;
use App\Models\Scraps;
use App\Traits\ScrapTrait;
use App\Console\Commands;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Tag\Html\Sub;

class HomeController extends Controller
{
    use ScrapTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function tops(){

        //Ultimas 24 horas
        $endDateTime = Carbon::now()->subHours(24);
        $company_id=session('company_id');

        /*********************************************************************************************************/
        //Top de 10 paginas con mas publicaciones en las ultimas 24 horas
        $pages = Post::select(DB::raw('page_name,page_id, count(page_id) as count'))
            ->where('created_time','>=', $endDateTime)
            ->groupby('page_id')
            ->orderby('count', 'DESC')
            ->take(10)
            ->get();


        /*********************************************************************************************************/
        // Total de interacciones generadas por mis temas
        //cantidas de publicidad ultimas 72 horas
        $temas = Classification_Category::select(DB::raw('classification_category.*,count(subcategoria_id) as count'))
            ->where('created_at', '>=', Carbon::now()->subHour(72))
            ->with('subcategory')
            ->where('company_id', $company_id)
            ->groupby('subcategoria_id')
            ->orderby('count', 'DESC')
            ->take(10)
            ->get();
        $temasCount = Classification_Category::where('company_id', $company_id)->where('created_at', '>=', Carbon::now()->subHour(72))->count('id');


        /*********************************************************************************************************/
        //Top 10 de palabras mas populares en las ultimas 24 horas
        $infoTemas=array();
        $topTemas = Top::where('type', 'topic')
                ->where('created_at', Carbon::today()->format("Y-m-d"))
                ->first();

        if($topTemas){
            $interaccionesT = json_decode($topTemas->interaction);
        }
        else{
            $interaccionesT = [];
        }
        $h=0;
        $y = 0;
        foreach ($interaccionesT as $interact) {
            foreach ($interact as $valor) {
                foreach ($valor as $key => $value) {
                    $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                        "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",'🟢',
                        "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https", '🇨🇷' );
                    $result_replace = str_ireplace($array_replace_words,"", $value->word);
                    if($result_replace != "" &&  strlen($result_replace)>=4){
                        $infoTemas[$y]['word'] = $value->word;
                        $infoTemas[$y]['count'] = $value->count;
                        $y++;
                    }
                    else{
                        continue;
                    }
                }
            }
        }
        $temp = array_unique(array_column($infoTemas, 'word'));
        $infoTemas = array_intersect_key($infoTemas, $temp);
        $keysContent = array_column($infoTemas,'count');
        array_multisort($keysContent, SORT_DESC, $infoTemas);
        $responseTemas['temas'][$h]['datos'] = array_slice($infoTemas, 0, 10);
        $h++;

        /*********************************************************************************************************/
        //Top de 10 temas de los contenidos
        $infoContent=array();
        $topContenido = Top::where('tops.company_id', $company_id)
            ->where('tops.created_at', Carbon::today()->format("Y-m-d"))
            ->join('category', 'category.id', 'tops.type')
            ->get();
        $j=0;
        $response=[];
        foreach ($topContenido as $content) {
            $category = $content->name;
            $interaccionesC = \GuzzleHttp\json_decode($content->interaction);
            $k = 0;
            foreach ($interaccionesC as $interact) {
                    foreach ($interact as $valor) {
                    foreach ($valor as $key => $value) {
                        $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                            "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",'🟢',
                            "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https", '🇨🇷' );
                        $result_replace = str_ireplace($array_replace_words,"", $value->word);
                        if($result_replace != "" &&  strlen($result_replace)>=4){
                            $infoContent[$k]['word'] = $value->word;
                            $infoContent[$k]['count'] = $value->count;
                            $k++;
                        }
                        else{
                            continue;
                        }
                    }
                }
            }
            $cont = array_unique(array_column($infoContent, 'word'));
            $infoContent = array_intersect_key($infoContent, $cont);
            $keysContent = array_column($infoContent,'count');
            array_multisort($keysContent, SORT_DESC, $infoContent);
            $response['category'][$j]['datos'] = array_slice($infoContent, 0, 10);
            $response['category'][$j]['name'] = $category;
            $j++;
        }

        /*********************************************************************************************************/
        //Top 10 de publicaciones con mas interacciones
        $data = array();
        $topData = array();
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);

        //$reacciones = Reaction::whereBetween('created_time',[$start_time , $end_time])
//            ->join('posts', 'posts.post_id', 'reaction_classifications.post_id')
//            ->orderBy('reaction_classifications.created_at', 'DESC')
//            ->get();
        $reacciones = Post::whereBetween('created_time',[$start_time , $end_time])->orderBy('created_at', 'DESC')->get();

        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys= array_column($data,'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);

        $i=0;
        foreach ($resultado as $post){
            $info = Post::where('post_id', $post['posteo'])->first();
            if($info){
                $topData[$i]['date']=$info->created_time;
                $topData[$i]['name']=$info->page_name;
                $topData[$i]['content']=$info->content;
                $topData[$i]['reaction']=$post['reacction'];
                $topData[$i]['count']=$post['count'];
                $i++;
            }
            else{
                $topData=[];
               continue;
            }
        }

        /*********************************************************************************************************/
        //Imprimir datos
        return view('tops', compact('pages', 'temas', 'temasCount', 'responseTemas','response', 'topData'));
    }

    public function wordContent(){
        $company_id=session('company_id');
        $infoContent=array();
        $topContenido = Top::where('tops.company_id', $company_id)
            ->where('tops.created_at', Carbon::today()->format("Y-m-d"))
            ->join('category', 'category.id', 'tops.type')
            ->get();
        $j=0;
        $response=[];
        $chartBubbles = [];
        foreach ($topContenido as $content) {
            $category = $content->name;
            $interaccionesC = \GuzzleHttp\json_decode($content->interaction);
            $k = 0;
            foreach ($interaccionesC as $interact) {
                foreach ($interact as $valor) {
                    foreach ($valor as $key => $value) {
                        $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                            "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",
                            "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https", '🇨🇷' );
                        $result_replace = str_ireplace($array_replace_words,"", $value->word);
                        if($result_replace != "" &&  strlen($result_replace)>=4){
                            $infoContent[$k]['word'] = $value->word;
                            $infoContent[$k]['count'] = $value->count * 50;
                            $k++;
                        }
                        else{
                            continue;
                        }
                    }
                }
            }

            $cont = array_unique(array_column($infoContent, 'word'));
            $infoContent = array_intersect_key($infoContent, $cont);
            $keysContent = array_column($infoContent,'count');
            array_multisort($keysContent, SORT_DESC, $infoContent);
            $response['category'][$j]['datos'] = array_slice($infoContent, 0, 10);
            $h = 0;
            foreach ($response['category'][$j]['datos'] as $value){
                $bubbles_data[$h] = ['name' => $value['word'], 'value' => $value['count']];
                $h++;
            }
            $chartBubbles[$j] = ['name' => $category, 'data' => $bubbles_data];
            $j++;
        }
        return $chartBubbles;
    }

    public function cloudWord(Request $request){
        if($request->subcategoria_id){
            $cloudWord = Classification_Category::where('classification_category.subcategoria_id','=',$request->subcategoria_id)
                ->join('comments', 'comments.post_id', 'classification_category.post_id')
                ->where('classification_category.created_at', '>=', Carbon::now()->subHour(48))
                ->get();
        }
        if($request->page_id){
            $cloudWord = Comment::where('page_id', $request->page_id)
                ->where('created_time', '>=', Carbon::now()->subHour(24))
                ->get();
        }
        return $cloudWord;
    }

    public function FeelingComments(){
        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);

        //Se obtiene el id de la compañia
        $company_id=session('company_id');

        //El objeto data por medio de un array obtiene los datos que se desea almacenar
        $data=array();
        //Cuenta la cantidad de veces que realizo consulta
        $i = 0;

        //Se llaman todos los temas que se encuentra registrado en la compania
        $temas = Subcategory::where('company_id', $company_id)->get();

        //Se realiza un recorrido por cada uno de los temas que se ha encontrado en el objeto temas
        foreach($temas as $topic){
            //Se llama las publicaciones clasificadas cuando el tema sea igual al objeto topic
            $rating = Comment::where('subcategoria_id', $topic->id)
                ->whereBetween('classification_category.created_at',[$start_time , $end_time])
                ->join('classification_category', 'classification_category.post_id', '=', 'comments.post_id')
                ->with('sentiment')
                ->select('comment_id')
                ->distinct('classification_category.post_id')
                ->get();

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
            $new['positivo'] =  $interation ? round((($positivo / $interation) * 100),2) : $interation;
            $new['negativo'] = $interation ? round((($negativo / $interation) * 100),2) : $interation;;
            $new['neutral'] = $interation ? round((($neutral / $interation) * 100),2) : $interation;

            $data['Tema'][$i]['Tema']=$topic->name;
            $data['Tema'][$i]['interation']=$new;
            $data['Tema'][$i]['negativo']=$interation ? round((($negativo / $interation) * 100),2) : $interation;
            $data['Tema'][$i]['neutral']=$interation ? round((($neutral / $interation) * 100),2) : $interation;
            $data['Tema'][$i]['positivo']=$interation ? round((($positivo / $interation) * 100),2) : $interation;
            $i++;
        }
        return $data;
    }

    public  function NetworkTopics (){
        // [COMPANY - TEMA]  [TEMA - PALABRAS]  [PALABAS - PAGINAS] [PAGINAS - COUNT(CANTIDAD DE PLUBLICACION DE LA PAGINA)]
        $company_id=session('company_id');

        $start_day = Carbon::now()->format('Y-m-d 00:00:00');
        $end_day = Carbon::now()->format('Y-m-d 23:59:59');
        $network = NetworkData::whereBetween('created_at', [$start_day, $end_day])->where('topic', 0)->where('company_id', $company_id)->first();
        $data = isset($network) ? json_decode($network->data) : "" ;
        $result['name'] = isset($network) ?  $network->company: session("company");
        $result['chart'] = $data;
        return $result;
    }

    public function NetworkDetail(){
        $company_id = session('company_id');
        $start_day = Carbon::now()->format('Y-m-d 00:00:00');
        $end_day = Carbon::now()->format('Y-m-d 23:59:59');

        $network = NetworkData::whereBetween('created_at', [$start_day, $end_day])->where('topic', 1)->where('company_id', $company_id)->first();
        $data = isset($network) ? json_decode($network->data) : "" ;
        $result['name'] = isset($network) ?  $network->company: session("company");
        $result['chart'] = $data;
        return $result;
    }
    public function companies()
    {
        $user_id=Auth::id();
        $user=User::find($user_id);
        $companies=$user->companies;
        return view('companies', compact('companies'));
    }

    public function setSession(Request $request){
        $company=Company::where('slug',$request->companies)->first();
        session([
            'company' => $request->companies,
            'company_id' => $company->id
        ]);
        $response['company']=session('company');
        $response['company_id']=session('company_id');
        return $response;
    }

    public function indexFb()
    {
        if(session('company')){
            //$pages=Page::where('company_id',session('company_id'))->join("info_pages","info_pages.page_id","scraps.page_id")->distinct('scraps.page_id')->select('info_pages.*','scraps.token')->get();
            $pages=Page::where('company_id',session('company_id'))
                ->join("info_pages","info_pages.page_id","scraps.page_id")
                ->select('info_pages.*','scraps.token', 'scraps.categoria_id')
                ->groupby('scraps.page_id')
                ->orderBy('scraps.categoria_id')
                ->get();
            return view('Facebook.Fanpage.index',compact('pages'));
        }else{
            return redirect()->route('comments.index');
        }
    }

    public function privacy()
    {
        return view('facebook.policies.privacy');
    }
    public function permissions()
    {
        return view('facebook.policies.permissions');
    }

    public function susbcribe(Request $request){
        $company = session('company');
        $company=Company::where('slug',$company)->first();
        $response['message']='No ha sido posible guardar la página';
        $response['status']='Error';
        $response['type']='error';

        $request->request->add([
            'user_id' => auth()->id(),
            'company_id' => $company->id
        ]);
        $page=Page::where('page_id',$request->page_id)->where('company_id',$request->company_id)->where('user_id',$request->user_id)->first();
        if(!$page){
            Page::create($request->all());
            $response['message']='Página guardada';
            $response['status']='Exito';
            $response['type']='success';

        }else{
            $request->request->add([
                'status' => 1,
            ]);
            $page->update($request->all());
            $response['message']='Página guardada';
            $response['status']='Exito';
            $response['type']='success';
        }
        $this->PostFB($request->page_id);
        $this->CommentsFB($request->page_id);
        return $response;
    }
    public function unsubscribe(Request $request){
        $companys = session('company');
        $company=Company::where('slug',$companys)->first();
        $response['message']='No ha sido posible eliminar la página';
        $response['status']='Error';
        $response['type']='error';

        $request->request->add([
            'user_id' => auth()->id(),
            'company_id' => $company->id
        ]);
        $page=Page::where('page_id',$request->page_id)->where('company_id',$request->company_id)->where('user_id',$request->user_id)->first();
        if($page){
            $page->update(['status'=>0]);
            $response['message']='Página eliminada';
            $response['status']='Exito';
            $response['type']='success';
        }
        return $response;
    }

    public function pages(){
        $paginas=Page::where('user_id',Auth::id());
        return $paginas;
    }

}
