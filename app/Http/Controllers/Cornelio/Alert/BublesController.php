<?php

namespace App\Http\Controllers\Cornelio\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Company;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class BublesController extends Controller
{
    public function index()
    {
        //Se llaman las companias
        $companies = Company::where('status', 1)->pluck( 'nombre', 'id');

        //Se llama los reportes de burbuja
        $bubles = NumberWhatsapp::whereIn('report', [13, 14, 15,16])->get();
        return view('Cornelio.Buble.index',compact('bubles', 'companies'));
    }

    public function show(Request $request){
        $num = NumberWhatsapp::where('numeros_whatsapp.id', $request->id_telephone)
            ->join('companies', 'companies.id', 'numeros_whatsapp.company_id')
            ->select('numeros_whatsapp.*', 'companies.nombre as company')
            ->first();
        $validation = ($num->content == "[]" ? null :  $num->content);
        $info['data'] = $num;
        if($validation != "null" && $validation){
            $contents_id = json_decode($num->content);
            $contents = Category::where('company_id', $num->company_id)->whereIn('id', $contents_id)->get();
            $info['data'] = $num;
            $info['contents'] = $contents;
        }
        return $info;
    }

    public function edit(NumberWhatsapp $numberWhatsapp){
        $num = NumberWhatsapp::where('id', $numberWhatsapp->id)->first();
        $company_name = Company::where('id', $num->company_id)->first();
        $contents = Category::where('company_id', $num->company_id)->get();

        $validation = ($num->content == "[]" ? null :  $num->content);
        if($validation != "null" && $validation) {
            $contents_id = json_decode($num->content);
            $contents = Category::where('company_id', $num->company_id)->whereNotIn('id', $contents_id)->get();
            $getContent = Category::whereIn('id', $contents_id)->get();
            return view('Cornelio.Buble.edit',compact('num','contents', 'company_name', 'getContent'));
        }
        return view('Cornelio.Buble.edit',compact('num','contents', 'company_name'));
    }

    public function update(Request $request, NumberWhatsapp $numberWhatsapp){
        if(isset($request->contents) && isset($request->deleteContents)){
            dd('entre en la primera');
            $destroyContent = json_decode($numberWhatsapp->content, true);
            foreach ($request->deleteContents as $destroy){
                $search = array_search($destroy, $destroyContent);
                if($search !== FALSE){
                    array_splice($destroyContent, $search, true);
                }
            }
            $numberWhatsapp->update(['content'=> json_encode($destroyContent)]);
            $content = (isset($request->contents) ? array_merge(json_decode($numberWhatsapp->content), $request->contents) : json_decode($numberWhatsapp->content));
            $numberWhatsapp->update(['content'=> json_encode($content)]);
        }
        else if(isset($request->deleteContents)){
            dd('entre en la segunda');
            $destroyContent = json_decode($numberWhatsapp->content, true);
            foreach ($request->deleteContents as $destroy){
                $search = array_search($destroy, $destroyContent);
                if($search !== FALSE){
                    array_splice($destroyContent, $search, true);
                }
            }
            $numberWhatsapp->update(['content'=> json_encode($destroyContent)]);
        }
        else if(isset($request->contents)){
            $contentArray = json_decode($numberWhatsapp->content) == null ? [] : json_decode($numberWhatsapp->content);
            $content = (isset($request->contents) ? array_merge($contentArray, $request->contents) : json_decode($numberWhatsapp->content));
            $numberWhatsapp->update(['content'=> json_encode($content)]);
        }
        return redirect()->route('bubles.index')->with('info','Reporte actualizado con éxito');
    }
}
