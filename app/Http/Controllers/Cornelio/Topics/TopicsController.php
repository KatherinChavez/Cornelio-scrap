<?php

namespace App\Http\Controllers\Cornelio\Topics;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Compare;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Models\Word;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    //
    public function index()
    {
        return view('Cornelio.Topics.index');
    }

    function syncTopics(): array
    {
        $data = [];
        $topics = Subcategory::where('company_id', session('company_id'))->get();
        $whatsapps = NumberWhatsapp::all();
        $words = Compare::all();
        foreach ($topics as $topic) {
            $details = $whatsapps->where('subcategory_id', $topic->id);
            $whats = [];
            foreach ($details as $detail) {
                $whats [] = ['id' => $detail->id, 'descripcion' => $detail->descripcion, 'numeroTelefono' => $detail->numeroTelefono];
            }
            $words_details = $words->where('subcategoria_id', $topic->id);
            $words_array = [];
            foreach ($words_details as $detail) {
                $words_array [] = ['id' => $detail->id, 'palabra' => $detail->palabra, 'detalle' => $detail->detalle, 'priority' => $detail->prioridad];
            }
            $data[] = ['id' => $topic->id, 'name' => $topic->name, 'nameTelegram' => $topic->nameTelegram, 'channel' => $topic->channel, 'whats' => $whats, 'words' => $words_array];
        }
        return $data;
    }

    public function store(Request $request)
    {
        $company_id = session('company_id');
        $topics = Subcategory::create([ 'name' => $request->topic_name,
                                        'subcategory_id' => $request->id,
                                        'channel' => $request->telegramChannel,
                                        'nameTelegram' => $request->telegramName,
                                        'company_id' => $company_id,
                                        'status' => 1]);
        $id_topics = $topics->id;
        if ($request->whatsapps) {
            $num = json_decode($request->whatsapps);
            foreach ($num as $whats) {
//                $codigopais = strpos($whats->id, '506');
//                if ($codigopais === false) {
//                    $whats->id = "506" . $whats->id;
//                }
                $whatsapp = NumberWhatsapp::create([
                    'numeroTelefono' => $whats->codigo.$whats->id,
                    'descripcion' => $whats->name,
                    'subcategory_id' => $id_topics,
                ]);
            }
        }
        if ($request->words) {
            $words = \GuzzleHttp\json_decode($request->words);
            foreach ($words as $word) {
                $wordO = (object)$word;
                $data = ([
                    'palabra' => $wordO->word,
                    'prioridad' => $word->priority,
                    'subcategoria_id' => $id_topics,
                ]);
                $palabras = Compare::create($data);
            }
        }

        $alert = Alert::create([
            'subcategory_id' => $id_topics,
            'notification' => 0,
            'report' => 0,
        ]);

        $mensaje = 'Registro guardado con éxito';
        return Response()->json($mensaje, 200);
    }

    public function update(Request $request)
    {
        $topic = Subcategory::find($request->topic_id);
        $topic->update([
            'name' => $request->topic_name,
            'detail' => '*',
            'nameTelegram' => $request->telegramName,
            'channel' => $request->telegramChannel
        ]);
        if ($request->whatsapps) {
            $num = json_decode($request->whatsapps);

            foreach ($num as $whats) {
                //if (key_exists('id', $whats)) {
                if (isset($whats->id)) {
//                    $codigopais = strpos($whats->numeroTelefono, '506');
//                    if ($codigopais === false) {
//                        $whats->numeroTelefono = "506" . $whats->numeroTelefono;
//                    }

                    NumberWhatsapp::whereId($whats->id)->update([
                        'numeroTelefono' => $whats->numeroTelefono,
                        'descripcion' => $whats->descripcion,
                    ]);
                }
                if (!isset($whats->id)) {
//                    $codigopais = strpos($whats->numeroTelefono, '506');
//                    if ($codigopais === false) {
//                        $whats->numeroTelefono = "506" . $whats->numeroTelefono;
//                    }

                    NumberWhatsapp::create([
                        'numeroTelefono' => $whats->codigo.$whats->numeroTelefono,
                        'descripcion' => $whats->descripcion,
                        'subcategory_id' => $topic->id
                    ]);
                }
            }
        }
        if ($request->words) {
            $words = json_decode($request->words);
            foreach ($words as $word) {
                //if (key_exists('id', $word)) {
                if (isset($word->id)) {
                    Compare::whereId($word->id)->update([
                            'palabra' => $word->palabra,
                            'prioridad' => $word->priority]
                    );
                }
                //if (!key_exists('id', $word)) {
                if (!isset($word->id)) {
                    Compare::create([
                        'palabra' => $word->palabra,
                        'prioridad' => $word->priority,
                        'subcategoria_id' => $topic->id,
                    ]);
                }
            }
        }
        $mensaje = 'Registro actualizo con éxito';
        return Response()->json($mensaje, 200);
    }

    public function delete(Request $request)
    {

        if (isset($request->id)) {
            $whats = NumberWhatsapp::findOrFail($request->id);
            $whats->delete();
        }
        $mensaje = 'Registro eliminado con éxito';
        return Response()->json($mensaje, 200);


    }

    public function deleteWord(Request $request)
    {
        try {
            if (isset($request->id)) {
                $word = Compare::findOrFail($request->id);
                $word->delete();
            }
            $mensaje = 'Registro actualizo con éxito';
            return Response()->json($mensaje, 200);
        }catch (\exception $e){
            $mensaje="La palabra no puede ser eliminado por tener clasificaciones asociadas, comuníquese con  un administrador";
            return  Response()->json($mensaje,201);
        }

    }

    public function deleteTopic($id)
    {
        try {
            $topic = Subcategory::findOrFail($id);
            $topic->delete();
            $mensaje = 'Registro actualizo con éxito';
            return Response()->json($mensaje, 200);
        }catch (\exception $error){
            $mensaje="El tema no puede ser eliminado por tener clasificaciones asociadas, comuníquese con  un administrador";
            return  Response()->json($mensaje,201);
        }

    }
}
