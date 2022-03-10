<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\BitacoraMessageFb;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Traits\TopicCountTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NumberWhatsappController extends Controller
{
    use TopicCountTrait;

    public function index(Request $request)
    {
        $whatsapp = NumberWhatsapp::join('subcategory', 'subcategory.id', 'numeros_whatsapp.subcategory_id')
            ->join('megacategory', 'megacategory.id', 'subcategory.megacategory_id')
            ->where('megacategory.company_id', session('company_id'))
            ->distinct('subcategory.id')
            ->paginate();

        $sub = Subcategory::where('company_id', session('company_id'))
            ->distinct('subcategory.id')
            ->paginate();


        return view('Cornelio.Category.NumberWhatsapp.index', compact('sub'));
    }

    public function create()
    {
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;
        $subcategories = Subcategory::where('company_id', $companies)->pluck('name', 'id');
        return view('Cornelio.Category.NumberWhatsapp.create', compact('subcategories'));
    }

    public function ShowNumber(Request $request)
    {
        $theme = NumberWhatsapp::where('subcategory_id', $request->subcategoria)->get();
        return $theme;
    }

    public function store(Request $request)
    {

        $request->validate([
            'numeroTelefono' => 'required',
            'descripcion' => 'required',
            'subcategory_id' => 'required',
        ]);

        $codigopais = strpos($request->numeroTelefono, '506');
        if ($codigopais === false) {
            $request->numeroTelefono = "506" . $request->numeroTelefono;
        }
        $whatsapp = NumberWhatsapp::create(['numeroTelefono' => $request->numeroTelefono, 'descripcion' => $request->descripcion, 'subcategory_id' => $request->subcategory_id]);
        return redirect()->route('whatsapp.index', [$whatsapp->id])->with('info', 'Registro guardado con éxito');
    }

    public function edit(NumberWhatsapp $whatsapp)
    {

        $user_id = Auth::id();
        $subcategories = Subcategory::where('user_id', $user_id)->pluck('name', 'id');

        return view('Cornelio.Category.NumberWhatsapp.edit', compact('whatsapp', 'subcategories'));
    }

    public function update(Request $request, NumberWhatsapp $whatsapp)
    {
        $request->validate([
            'numeroTelefono' => 'required',
            'descripcion' => 'required',
            'subcategory_id' => 'required',
        ]);
        $codigopais = strpos($request->numeroTelefono, '506');
        if ($codigopais === false) {
            $request->numeroTelefono = "506" . $request->numeroTelefono;
        }
        $whatsapp->update(array_merge($request->all(), ['numeroTelefono' => $request->numeroTelefono]));
        return redirect()->route('whatsapp.index', [$whatsapp->id])->with('info', 'Registro actualizado con éxito');
    }

    public function destroy(NumberWhatsapp $whatsapp)
    {
        $whatsapp->delete();
        //return back()->with('info', 'Eliminada correctamente');
        return redirect()->route('whatsapp.index')->with('info', 'Eliminada correctamente');
    }

    public function Contact(Request $request)
    {
        if ($request->compania) {
            $contactos = NumberWhatsapp::join('subcategory', 'subcategory.id', 'numeros_whatsapp.subcategory_id')
                //->where('subcategory.company_id', '=', $request->compania)
            ->where('subcategory_id','=',$request->sub)
            ->get();
        } else {
            $contactos = NumberWhatsapp::join('subcategory', 'subcategory.id', 'numeros_whatsapp.subcategory_id')
                //->where('subcategory.company_id','=',$request->compania)->get();
                ->where('subcategory', 'subcategory_id', '=', $request->sub)->get();
        }


        return $contactos;
    }

    public function Send(Request $request)
    {
        $data = [];
        $sub = Subcategory::where('id', '=', $request->sub)->first();
        $sub_name = $sub['name'];
        $comp = Company::where('id', $sub->company_id)->whereNotNull('client_id')->whereNotNull('instance')->first();
        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        if ($request->phone == 'todos') {
            $contactos = NumberWhatsapp::where('subcategory_id', '=', $request->sub)->pluck('numeroTelefono');
        } else {
            $destination = $request->phone;
            $contactos = array($destination);
        }

        foreach ($contactos as $contacto) {
            if($comp){
                $client_id = $comp->client_id;
                $instance = $comp->instance;
            }
            $nameSub = $this->eliminar_acentos(str_replace(' ', '+', $sub_name));
            $namePage = $this->eliminar_acentos(str_replace(' ', '+', $request->pagina));
            $message = "%C2%A1Hola%21+Tengo+la+siguiente+alerta+relacionada+con+$nameSub+la+encontr%C3%A9+en+$namePage%2C+ac%C3%A1+te+dejo+el+link+para+que+la+veas%3A+https://www.facebook.com/$request->post_id";

            BitacoraMessageFb::create([
                'type'        => $contacto != 0 ? 'phone'   : 'group',
                'number'      => $contacto,
                'typeMessage' =>'text',
                'report'      => '99',
                'message'     => $message,
                'status'      => 0,
            ]);
            return 200;

            /*$urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
            $result = file_get_contents($urlMessage);
            $data = json_decode($result);
            if($data->status == true){
                //Se envia correctamente el mensaje con la instancia de la compañia
                return 200;
            }
            elseif ($data->status == false && $data->message == "Device not connected "){
                //Se encuentra desconectada la instancia de la compañia, por lo tanto se llama la instancia de Cornelio
                $client_id = env('CLIENT_ID');
                $instance = env('INSTANCE');
                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                $result = file_get_contents($urlMessage);
                $data = json_decode($result);
                if ($data->status == false && $data->message == "Device not connected "){
                    //Se indica que la instancia de Cornelio se encuentra desconectada
                    return 500;
                }
                elseif ($data->status == false){
                    //Se encontro algun problema al enviar mnensaje con la instancia de Cornelio
                    return 400;
                }
            }
            elseif ($data->status == false){
                //Se encuentra algun error en la instancia que tiene la compañia, se llama la instancia que tiene cornelio
                $client_id = env('CLIENT_ID');
                $instance = env('INSTANCE');
                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                $result = file_get_contents($urlMessage);
                $data = json_decode($result);
                if ($data->status == false && $data->message == "Device not connected "){
                    //Se indica que la instancia de cornelio se encuentre desconectada
                    return 500;
                }
                elseif ($data->status == false){
                    //Se indica que se encontro un problema al enviar el mensaje
                    return 400;
                }
            }*/
            /*$message = "¡Hola! Tengo la siguiente alerta relacionada con: " . $sub_name . " la encontré en " . $request->pagina .
                ", acá te dejo el link para que la veas: https://www.facebook.com/" . $request->post_id;

            $data = [
                'phone' => $contacto,
                'body' => $message,
            ];
            $json = json_encode($data); // Encode data to JSON
            $url = env('WHA_API_URL') . env('WHA_API_TOKEN');
            $options = stream_context_create(['http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $json
            ]
            ]);
            // Send a request
            return $result = file_get_contents($url, false, $options);*/
        }
    }
}
