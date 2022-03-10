<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Models\Classification_Category;
use App\Models\Company;
use App\Models\Compare;
use App\Models\Page;
use App\Models\Scraps;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Comparator\Comparator;

class ClassificationWordController extends Controller
{
    public function index(){

        return view('Cornelio.Classification.Word.index');
    }

    public function create(){
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $subcategories=Subcategory::where('subcategory.company_id','=', session('company_id'))->pluck('name','id');
        return view('Cornelio.Classification.Word.create', compact('subcategories'));
    }

    public function store(Request $request){
        $request->validate([
            'palabra' => 'required',
            'detalle' => 'required',
            'subcategoria_id' => 'required',
            'prioridad' => 'required',
        ]);

        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $request->request->add([
            'user_id' => $user_id,
            'company_id' => $companies
        ]);

        $palabra=Compare::where('palabra','=',$request->palabra)
            ->where('user_id','=',$request->user_id)
            ->first();
        if($palabra == null){
            $palabra = Compare::create(['palabra'=>$request->palabra, 'detalle' => $request->detalle, 'subcategoria_id'=>$request->subcategoria_id, 'prioridad' => $request->prioridad, 'user_id' => $user_id]);
        }
        return redirect()->route('ClassificationWord.index',[$palabra->id])->with('info','Registro guardado con éxito');
    }

    public function edit2(Compare $compare){
        //dd($compare);
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $subcategories=Subcategory::where('user_id',$user_id)->pluck('name','id');
        return view('Cornelio.Classification.Word.edit', compact('subcategories','compare'));
    }

    public function edit(Compare $compare){

        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $subcategories=Subcategory::where('subcategory.company_id','=', session('company_id'))->pluck('name','id');

        return view('Cornelio.Classification.Word.edit', compact('compare','subcategories'));
    }

    public function update(Request $request, Compare $compare){
        $request->validate([
            'palabra' => 'required',
            'detalle' => 'required',
            'subcategoria_id' => 'required',
            'prioridad' => 'required',
        ]);

        $user_id=Auth::id();
        $compare->update(['palabra'=>$request->palabra, 'detalle' => $request->detalle, 'subcategoria_id'=>$request->subcategoria_id, 'prioridad' => $request->prioridad, 'user_id' => $user_id]);
        return redirect()->route('ClassificationWord.index',[$compare->id])->with('info','Registro actualizado con éxito');
    }

    public function destroy(Compare $compare){
        $compare->delete();
        return back()->with('info', 'Eliminada correctamente');
    }

    public function search(Request $request){

        if($request->search){
            $Compare=Compare::where('compare.palabra','LIKE','%'.$request->search.'%')
                ->orWhere('compare.detalle', 'LIKE', '%' .$request->search.'%')
                ->where('subcategory.company_id','=', session('company_id'))
                ->join('subcategory', 'subcategory.id', '=', 'subcategoria_id')
                ->select('subcategory.name as subcategoria', 'compare.*')
                ->paginate(50);


        }else{
            $Compare=Compare::join('subcategory', 'subcategory.id', '=', 'subcategoria_id')
                ->where('subcategory.company_id','=', session('company_id'))
                ->select('subcategory.name as subcategoria', 'compare.*')
                ->paginate(50);

        }
        return $Compare;
    }

    public function CompareSubcategory(Request $request){
        $sub=Subcategory::where('user_id','=',$request->user_id)->get();
        return $sub;
    }
    public function store1(Request $request){
        $palabra=Compare::where('palabra','=',$request->palabra)
            ->where('user_id','=',$request->user_id)
            ->first();
        if($palabra == null){
            Compare::create($request->all());
        }
    }
    public function getCompare(Request $request){
        $palabra=Compare::where('comparar.palabra','=',$request->palabra)
            ->where('comparar.user_id','=',$request->user_id)
            ->select('comparar.*','subcategoria_categorias.name')
            ->join('subcategoria_categorias', 'subcategoria_categorias.id', '=', 'comparar.subcategoria_id')
            ->get();
        return $palabra;
    }
    public function AllCompare(Request $request){
        $palabra=Compare::where('comparar.user_id','=',$request->user_id)
            ->select('comparar.*','subcategoria_categorias.name')
            ->join('subcategoria_categorias', 'subcategoria_categorias.id', '=', 'comparar.subcategoria_id')
            ->orderBy('palabra', 'asc')
            ->get();
        return $palabra;
    }
    public function getId(Request $request){
        $palabra=Compare::where('comparar.id','=',$request->id)
            ->join('subcategoria_categorias', 'subcategoria_categorias.id', '=', 'comparar.subcategoria_id')
            ->select('comparar.*','subcategoria_categorias.name')
            ->first();
        return $palabra;
    }


}
