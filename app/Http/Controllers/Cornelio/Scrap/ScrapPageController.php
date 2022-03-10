<?php

namespace App\Http\Controllers\Cornelio\Scrap;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Company;
use App\Models\Scraps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScrapPageController extends Controller
{
    public function index()
    {
        $user_id=Auth::id();
        $compain=Company::where('slug',session('company'))->first();
        $companies=$compain->id;
        $categories=Category::where('company_id',$companies)->pluck('name','id');
        return view('Cornelio.Scraps.ScrapsPage.index',compact('categories'));
    }
    public function delete(Request $request){
        if($request->id){
            $scrap=Scraps::where('id',$request->id)->first()->delete();
            return Response()->json(['code'=>200]);
        }
        return Response()->json(['code'=>204]);

    }
    public function saveScrap(Request $request){
        $competence = ($request->competence != "") ? $request->competence : 0;
        $compain = session('company');
        $company = Company::where('slug', $compain)->first();
        $user_id=Auth::id();
        $guardar=Scraps::where('page_id',$request->idP)->where('company_id', $company->id)->first();
        if(!$guardar){
            $scraps=Scraps::create([
                'page_id'=>$request->idP,
                'page_name'=>$request->nombre,
                'user_id'=>$user_id,
                'competence'=>$competence,
                'categoria_id'=>$request->categoria,
                'token'=>$request->pageAccessToken,
                'company_id'=>$company->id,
                'created_time' => Carbon::now(),
                'picture' =>$request->picture,
            ]);
            $status=200;
        }else {
            $guardar->update([
                'page_id'=>$request->idP,
                'page_name'=>$request->nombre,
                'competence'=>$competence,
                'user_id'=>$user_id,
                'categoria_id'=>$request->categoria,
                'token'=>$request->pageAccessToken,
                'company_id'=>$company->id,
                'picture' =>$request->picture,
            ]);
            $status=204;
        }
        return Response()->json($status);
    }


    public function ScrapValidation(Request $request){
        $guardar=Scraps::where('page_id',$request->page_id)->first();
        if($guardar != null){
            $status=true;
        }else {
            $status=false;
        }
        return Response()->json($status);
    }

    public function indexCRUD()
    {
        $scraps=Scraps::paginate();
        return view('Cornelio.Scraps.ScrapsPage.indexCRUD',compact('scraps'));
    }

    public function destroy($company,Scraps $scraps)
    {
        $scraps->delete();
        return back()->with('info', 'Eliminada correctamente',compact('company'));
    }

    public function showCRUD(Scraps $scraps)
    {
        return view('Cornelio.Scraps.ScrapsPage.showCRUD', compact('scraps'));
    }
}
