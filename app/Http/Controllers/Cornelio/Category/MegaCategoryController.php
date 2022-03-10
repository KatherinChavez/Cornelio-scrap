<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Company;
use App\Models\Megacategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MegaCategoryController extends Controller
{
    public function index(Request $request){
        $user_id=Auth::id();
        if($request){
            $megacategorias=Megacategory::where('megacategory.name','LIKE','%'.$request->search.'%')
                ->orWhere('megacategory.description', 'LIKE', '%' .$request->search.'%')
                ->paginate();
        }else{
            $megacategorias=Megacategory::paginate();
        }
        return view('Cornelio.Category.Megacategory.index', compact('megacategorias','user_id'));
    }

    public function create(){
        return view('Cornelio.Category.Megacategory.create');
    }

    public function store(Request $request){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $request->request->add([
            'user_id' => $user_id,
            'company_id' => $companies
        ]);
        $megacategorias=Megacategory::create($request->all());

        $id_mega=$megacategorias->id;
        $request->request->add([
            'megacategory_id' => $id_mega,
            'notification' => 0,
            'report' => 0,
        ]);
        $alert=Alert::create($request->all());

        return redirect()->route('megacategory.index',[$megacategorias->id])->with('info','Registro guardado con éxito');
    }

    public function show( Megacategory $megacategorias){
        return view('Cornelio.Category.Megacategory.show', compact('megacategorias'));
    }

    public function edit(Megacategory $megacategorias){


        return view('Cornelio.Category.Megacategory.edit', compact('megacategorias'));
    }

    public function update(Request $request, Megacategory $megacategorias){
        
        $megacategorias->update($request->all());

        return redirect()->route('megacategory.index',[$megacategorias->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(Megacategory $megacategorias){
        $megacategorias->delete();
        return back()->with('info', 'Eliminada correctamente');
    }

    public function search(Request $request){
        $take=20;
        if($request->search){
            $megacategoria = Megacategory::where('name','like','%' . $request->search . '%')
                ->orWhere('description','LIKE','%' .$request->search. '%')
                ->take($take)
                ->get();
        }else{
            $megacategoria = Megacategory::take($take)->get();
        }
        return $megacategoria;
    }
}
