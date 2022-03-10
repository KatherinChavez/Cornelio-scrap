<?php

namespace App\Http\Controllers\Cornelio\Cron;

use App\Http\Controllers\Controller;
use App\Models\App_Fb;
use App\Models\Cron;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Carbon\Carbon;
use Facebook;
use Illuminate\Http\Request;
use App\Traits\ScrapTrait;

class CronController extends Controller
{
    use ScrapTrait;
    public function index(Request $request)
    {
        if($request->search){
            $company_id = session('company_id');
            $cron = Cron::join('scraps', 'scraps.page_id','cron_pages.page_id')
                ->where('cron_pages.page_name', 'like', '%' . $request->search . '%')
                ->orWhere('cron_pages.page_id', 'like', '%' . $request->search . '%')
                ->select('cron_pages.*', 'scraps.status')
                ->distinct()
                ->paginate();

        }else {
            $company_id = session('company_id');
            $cron = Cron::/*where('company_id', $company_id)
                ->*/with(['page','posts' => function ($q) {
                    $q->orderBy('posts.updated_at', 'DESC');
                }])->join('scraps', 'scraps.page_id','cron_pages.page_id')->select('cron_pages.*', 'scraps.status')->distinct()->paginate();

        }
        return view('Cornelio.Cron.index',compact('cron'));
    }

    public function create(){
        $cron = Cron::get();
        $app = App_Fb::pluck('name_app','id');
        return view("Cornelio.Cron.create", compact('cron', 'app'));
    }

    public function store(Request $request)
    {
        $company_id = session('company_id');
        $request->validate
        ([
            'page_id' => 'required',
            'appPost' => 'required',
            'appReaction' => 'required',
            'timePost' => 'required',
            'timeReaction' => 'required',
            'limit_time' => 'required',
            'limit' => 'required'
        ]);
        $cron=Cron::where('page_id',$request->page_id)->where('company_id',$company_id)->first();
        if($cron){
            $request->validate
            ([
                'page_name' => 'required|unique:cron_pages',
            ]);
        }

        $cron = Cron::create([
            'page_id' => $request->page_id,
            'page_name'=> $request->page_name,
            'timePost'=> $request->timePost,
            'timeReaction'=> $request->timeReaction,
            'id_appPost'=>$request->appPost,
            'id_appReaction'=>$request->appReaction,
            'limit_time'=>$request->limit_time,
            'limit'=>$request->limit,
            'company_id'=>$company_id
        ]); // Se guardan todos los datos que se han registrado
        return redirect()->route('Cron.index',[$cron->id])->with('info','Registro guardado con éxito');

    }

    public function edit(Cron $cron)
    {
        $cronScrap = Cron::get(); // Llama solo ciertos valores de la tabla centro
        $app = App_Fb::pluck('name_app','id');
        return view('Cornelio.Cron.edit', compact('cron','cronScrap', 'app'));
    }

    public function update(Request $request, Cron $cron)
    {
        $company_id = session('company_id');
        $cron->update($request->all());
        return redirect()->route('Cron.index',$cron->id)->with('info','Registro actualizado con éxito');
    }

    public function delete($id)
    {
        $cron = Cron::findOrFail($id);
        $cron->delete();
        return redirect()->route('Cron.index')->with('info', 'Eliminada correctamente');
    }

    public function execution(){
        //Muestra las paginas que se desea ejecutar
        $cron = Cron::get();

        foreach ($cron as $pages){
            //Tiempo que se debe ejecutar el scrap
            $time = $pages->time; // El tiempo que se registra es en minutos
            $page = $pages->page_id;
            $now = Carbon::now()->subMinutes($time);

            //Se pregunta por el ultimo scrap y se valida si debe de realizar scrap
            $list = Post::where('page_id',$page)
                ->where('created_at', '>=', $now)
                ->orderBy('created_at', 'DESC')
                //->take(1)
                //->get();
                ->first();

            if(!$list){
                $this->PostFB($pages);
            }
        }
    }

    public function pruebaScrap(){
        $page = Scraps::where('categoria_id', 7)
            ->join('cron_pages','cron_pages.page_id','=','scraps.page_id')
            ->get();

        $config=array(
            'app_id' => env('APP_FB_ID'),
            'app_secret' => env('APP_FB_SECRET_2'),
            'default_graph_version' => env('APP_FB_VERSION')
        );

        $fb = new Facebook\Facebook($config);
        // $token=env('APP_FB_TOKEN_2');

        foreach($page as $pages){
            $idPage = $pages['page_id'];
            $scrapTime = $pages['time'];
            $lastScrap = $pages['last_scrap'];
            $interval = Carbon::now()->diff( $lastScrap );
            $formato = $interval->format("%H:%I:%S");
            //$formato = ($interval->i, 'minute');
            dd(Carbon::now(),$lastScrap , $formato);

            // if($formato >= $scrapTime){
            //     $token= $pages['token'];
            //     dd($token);
            // }

        }
    }

    public function stop(Cron $cron){
        try{
            Scraps::where('page_id', $cron->page_id)->update(['status' => 0]);
            return redirect()->route('Cron.index')->with('info','Se ha inactivado la página de forma exitosa ');
        }catch (\Exception $e){
            return redirect()->route('Cron.index')->with('info','No se puede inactivar la página seleccionada');
        }
    }

    public function play(Cron $cron){
        try{
            Scraps::where('page_id', $cron->page_id)->update(['status' => 1]);
            return redirect()->route('Cron.index')->with('info','Se ha activado la página de forma exitosa ');
        }catch (\Exception $e){
            return redirect()->route('Cron.index')->with('info','No se puede activar la página seleccionada');
        }
    }

}
