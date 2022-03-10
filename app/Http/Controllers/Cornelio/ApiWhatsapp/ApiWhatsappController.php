<?php

namespace App\Http\Controllers\Cornelio\ApiWhatsapp;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use Illuminate\Http\Request;

class ApiWhatsappController extends Controller
{
    public function index(){
        $apis = ApiWhatsapp::paginate();
        return view('Cornelio.ApiWhatsapp.index', compact('apis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'instance' => 'required',
            'key' => 'required',
        ]);
        $api = ApiWhatsapp::create([
            'client_id'=>$request->client_id,
            'instance'=>$request->instance,
            'key' => $request->key
        ]);
        return '200';
    }

    public function edit(Request $request)
    {
        $api = ApiWhatsapp::where('id', $request->id_apiW)->first();
        return $api;
    }

    public function update(Request $request){
        $request->validate([
            'client_id' => 'required',
            'instance' => 'required',
            'key' => 'required',
        ]);
        $api = ApiWhatsapp::where('id', $request->id_apiW)->first();
        $api->update(['client_id' => $request->client_id, 'instance'=>$request->instance, 'key' => $request->key]);
        return redirect()->route('apiWhatsapp.index')->with('info','Se ha actualizado exitosamente');
    }

    public function destroy(ApiWhatsapp $apiWhatsapp)
    {
        $apiWhatsapp->delete();
        return redirect()->route('apiWhatsapp.index')->with('info', 'Eliminada correctamente');
    }
}