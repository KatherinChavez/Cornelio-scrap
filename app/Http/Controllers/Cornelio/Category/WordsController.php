<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subcategory;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WordsController extends Controller
{
    public function index(Request $request){
        $user_id=Auth::id();
        if($request){
            $words=Word::where('word.word','LIKE','%'.$request->search.'%')
                ->orWhere('word.description', 'LIKE', '%' .$request->search.'%')
                ->paginate();
        }else{
            $words=Word::paginate();
        }

        return view('Cornelio.Category.Words.index', compact('words','user_id'));
    }

    public function create(){
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $subcategories=Subcategory::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Category.Words.create',compact('subcategories'));
    }

    public function store(Request $request){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $request->validate([
            'numeroTelefono' => 'required',
            'descripcion' => 'required',
            'subcategory_id' => 'required',
        ]);
        $request->request->add([
            'user_id' => $user_id,
            'company_id' => $companies
        ]);
        $words=Word::create($request->all());
        return redirect()->route('words.index',[$words->id])->with('info','Registro guardado con éxito');
    }

    public function edit(Word $words){
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $subcategories=Subcategory::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Category.Words.edit', compact('words','subcategories'));
    }

    public function update(Request $request, Word $words){
        $words->update($request->all());
        return redirect()->route('words.index',[ $words->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(Word $words){
        $words->delete();
        return back()->with('info', 'Eliminada correctamente');
    }

    public function search(Request $request){
        $take=20;
        if($request->search){
            $word = Word::where('word','like','%' . $request->search . '%')
                ->orWhere('description','LIKE','%' .$request->search. '%')
                ->orWhere('priority','LIKE','%' .$request->search. '%')
                ->take($take)
                ->get();
        }else{
            $word = Word::take($take)->get();
        }
        return $word;
    }
}
