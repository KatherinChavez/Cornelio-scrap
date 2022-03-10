<?php

namespace App\Http\Controllers\Cornelio\Analysis;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Scraps;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $page=Scraps::where('company_id', session('company_id'))
            ->orderBy('page_name')
            ->pluck('page_name','page_id');
        $data = 0;
        if($request->page_id){
            $data = 1;
            //Muestra las publicaciones
            $post = Post::where('page_id', $request->page_id)->orderBy('created_at','DESC')->paginate(5);

            //Muestra los comentaios
            $comment = Comment::where('page_id', $request->page_id)->orderBy('created_time','DESC')->paginate(5);

            //Muestra las clasificaciones
            $clasification = Classification_Category::where('megacategoria_id', '!=', null)->where('page_id', $request->page_id)->orderBy('created_at','DESC')->paginate(5);

            //Muestra el ultimo scrap de reacciones
            $reaction = Reaction::where('page_id', $request->page_id)->orderBy('created_at','DESC')->paginate(5);

            return view('Cornelio.Analysis.index',compact('data','page','post', 'comment', 'clasification', 'reaction'));
        }
        return view('Cornelio.Analysis.index',compact('page','data'));
    }


}
