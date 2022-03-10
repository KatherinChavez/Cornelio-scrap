<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Megacategory;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $user_id=Auth::id();
        if($request){
            $subcategorias=Subcategory::where('subcategory.name','LIKE','%'.$request->search.'%')
                ->orWhere('subcategory.detail', 'LIKE', '%' .$request->search.'%')
                ->orWhere('subcategory.category_id', $request->categoria)
                ->paginate();
        }else{
            $subcategorias=Subcategory::paginate();
        }

        return view('Cornelio.Category.Subcategory.index', compact('subcategorias','user_id'));
    }


    public function get(Request $request){
        if($request){
        $subcategorias=Subcategory::where('subcategory.category_id', $request->categoria)
            ->paginate();
        }else{
        $subcategorias=Subcategory::paginate();
        }
        return $subcategorias;
    }

    public function create(){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $megacategories=Megacategory::where('user_id',$user_id)->pluck('name','id');
        $categories=Category::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Category.Subcategory.create', compact('megacategories','categories'));
    }

    public function store(Request $request){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $request->request->add([
            'user_id' => $user_id,
            'company_id' => $companies
        ]);
        $subcategorias=Subcategory::create($request->all());

        return redirect()->route('subcategorias.index',[$subcategorias->id])->with('info','Registro guardado con éxito');
    }

    public function show(Subcategory $subcategorias){
        return view('Cornelio.Category.Subcategorias.show', compact('subcategorias'));
    }

    public function edit(Subcategory $subcategorias){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $megacategories=Megacategory::where('user_id',$user_id)->pluck('name','id');
        $categories=Category::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Category.Subcategory.edit', compact('megacategories','categories','subcategorias'));
    }

    public function update(Request $request, Subcategory $subcategorias){
        $subcategorias->update($request->all());
        return redirect()->route('subcategorias.index',[$subcategorias->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(Subcategory $subcategorias){
        $subcategorias->delete();
        return back()->with('info', 'Eliminada correctamente');
    }

    public function search(Request $request){
        $take=20;
        if($request->search){
            $subcategoria = Subcategory::where('name','like','%' . $request->search . '%')
                ->orWhere('detail','LIKE','%' .$request->search. '%')
                ->orWhere('channel','LIKE','%' .$request->search. '%')
                ->take($take)
                ->get();
        }else{
            $subcategoria = Subcategory::take($take)->get();
        }
        return $subcategoria;
    }
}
