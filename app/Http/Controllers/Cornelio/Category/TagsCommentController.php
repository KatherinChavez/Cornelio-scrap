<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\TagsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagsCommentController extends Controller
{
    public function index(Request $request){
        $user_id=Auth::id();
        $tags=TagsComment::paginate();
        if($request){
            $tags=TagsComment::where('tagscomments.name','LIKE','%'.$request->search.'%')
                ->orWhere('tagscomments.type', 'LIKE', '%' .$request->search.'%')
                ->paginate();
        }else{
            $tags=TagsComment::paginate();
        }

        return view('Cornelio.Category.TagsComment.index', compact('tags','user_id'));
    }

    public function create(){
        return view('Cornelio.Category.TagsComment.create');
    }

    public function store(Request $request){
        $user_id=Auth::id();
        $request->request->add([
            'user_id' => $user_id
        ]);
        $tags=TagsComment::create($request->all());

        return redirect()->route('tags.index',[$tags->id])->with('info','Registro guardado con éxito');
    }

    public function edit(TagsComment $tags){
        return view('Cornelio.Category.TagsComment.edit', compact('tags'));
    }

    public function update(Request $request, TagsComment $tags){
        $tags->update($request->all());

        return redirect()->route('tags.index',[$tags->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(TagsComment $tags){
        $tags->delete();
        return back()->with('info', 'Eliminada correctamente');
    }
    
    public function search(Request $request){
        $take=20;
        if($request->search){
            $tag = TagsComment::where('name','like','%' . $request->search . '%')
                ->orWhere('type','LIKE','%' .$request->search. '%')
                ->take($take)
                ->get();
        }else{
            $tag = TagsComment::take($take)->get();
        }
        return $tag;
    }
}
