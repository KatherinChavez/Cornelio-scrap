<?php

namespace App\Http\Controllers\Cornelio\Telephone;

use App\Http\Controllers\Controller;
use App\Models\ApiWhatsapp;
use App\Models\Company;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Subcategory;
use App\Models\TopReaction;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Translation\t;

class TelephoneController extends Controller
{
    use TopicCountTrait;

    public function index(Request $request){
        $companies = Company::where('status', 1)->pluck( 'nombre', 'id');

        if($request->search){
            $numbers = NumberWhatsapp::where('descripcion','LIKE','%'.$request->search.'%')
                ->orWhere('numeroTelefono', 'LIKE', '%' .$request->search.'%')
                ->where('report', '!=',0)
                ->whereNull('subcategory_id')
                ->paginate();
        }
        else{
            //$numbers = NumberWhatsapp::where('subcategory_id', null)->paginate();
            $numbers = NumberWhatsapp::whereNull('subcategory_id')->where('report', '!=',0)->paginate();
        }
        return view('Cornelio.Telephone.index', compact('numbers', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'companies' => 'required',
        ]);

        foreach ($request->type as $type){
            $num = NumberWhatsapp::create([
                'numeroTelefono'=>$request->code.$request->number,
                'descripcion'=>$request->name,
                'group_id'=>$request->group_id,
//                'report' => $request->type,
                'report' => $type,
                'company_id' => $request->companies
            ]);
        }

        return '200';
    }

    public function edit(Request $request)
    {
        $num = NumberWhatsapp::where('id', $request->id_telephone)->first();
        return $num;
    }

    public function update(Request $request){
        $request->validate([
            'name' => 'required',
//            'number' => 'required|numeric|digits_between:11,15',
        ]);
        $num = NumberWhatsapp::where('id', $request->id_telephone)->first();
        $num->update([
            'numeroTelefono' => $request->number,
            'group_id'=>$request->group_id,
            'descripcion'=>$request->name,
            'report' => $request->type,
            'company_id' => $request->companies]);
        return redirect()->route('Telephone.index')->with('info','Se ha actualizado exitosamente');
    }

    public function destroy(NumberWhatsapp $numberWhatsapp)
    {
        $numberWhatsapp->delete();
        return redirect()->route('Telephone.index')->with('info', 'Eliminada correctamente');
    }

    public function sendPdf(Request $request)
    {
        //Ultimas 24 horas
        $endDateTime = Carbon::now()->subHours(24);
        //Top 10 de publicaciones con mas interacciones
        $data = array();
        $topData = array();
        $reacciones = Reaction::where('created_at', '>=', $endDateTime)->orderBy('created_at', 'DESC')->get();
        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys = array_column($data, 'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);
        $i = 0;
        foreach ($resultado as $post) {
            $info = Post::where('post_id', $post['posteo'])->with('attachment')->first();
            if ($info) {
                $topData[$i]['date'] = $info->created_time;
                $topData[$i]['name'] = $info->page_name;
                $topData[$i]['content'] = $info->content;
                $topData[$i]['reaction'] = $post['reacction'];
                $topData[$i]['count'] = $post['count'];

                if (isset($info['attachment']['picture'])) {
                    $topData[$i]['attachment'] = $info['attachment']['picture'];
                }
                $i++;
            } else {
                $topData = [];
                continue;
            }
        }

        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        $content = PDF::loadView('PDF.pdf', compact('topData'))->output();
        $fileName = 'Top-' . Carbon::now()->format('Y-m-d') . '_hora_' . Carbon::now()->format('H-i') . '.pdf';
        $file = Storage::disk('public_files')->put($fileName, $content);
        $path = Storage::disk('public_files')->path($fileName);

        $contacto = $request->codeSend.$request->numeroSend;
        $name = $this->eliminar_acentos(str_replace(' ', '+', $request->nombreSend));
        $mensaje = 'Hola+' . $name . '!+Adjunto+el+siguiente+link+con+los+datos+de+los+top+10+de+los+temas+con+m%C3%A1s+interacciones+en+las+redes+sociales';
        $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
        $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_files/' . $fileName . '&type=image';

        //$urlBody ='https://wapiad.com/api/send.php?client_id=28c32e382fe61d66c0c2c6d694a9f737&instance=0c7867b9ee967783f17a24195c1f1bb1&number='.$contacto.'&message='.$mensaje.'&type=text';
        //$urlPdf ='https://wapiad.com/api/send.php?client_id=28c32e382fe61d66c0c2c6d694a9f737&instance=0c7867b9ee967783f17a24195c1f1bb1&number=50686258376&message='.env('APP_URL').'whatsapp_files/'.$fileName.'&type=image';

        $resultM = file_get_contents($urlBody);
        $resultP = file_get_contents($urlPdf);

        $dataM = json_decode($resultM);
        $dataP = json_decode($resultP);

        if($dataM->status == true && $dataP->status == true){
            return '200';
        }
        return '500';

    }

    public function exportPdf(){
        //Ultimas 24 horas
        $endDateTime = Carbon::now()->subHours(24);
        //Top 10 de publicaciones con mas interacciones
        $data = array();
        $topData = array();
        $reacciones = Reaction::where('created_at', '>=',  $endDateTime)->orderBy('created_at', 'DESC')->get();
        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys= array_column($data,'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);
        $i=0;
        foreach ($resultado as $post){
            $info = Post::where('post_id', $post['posteo'])
                ->with(['attachment', 'classification_category' => function ($q) {
                    $q->join('subcategory', 'subcategory.id', 'classification_category.subcategoria_id');}])
                ->first();
            if($info){
                $topData[$i]['posicion']=$i+ 1;
                $topData[$i]['date']=$info->created_time;
                $topData[$i]['name']=$info->page_name;
                $topData[$i]['content']=$info->content;
                $topData[$i]['reaction']=$post['reacction'];
                $topData[$i]['count']=$post['count'];

                if(isset($info['attachment']['picture'])){
                    $topData[$i]['attachment']=$info['attachment']['picture'];
                }
                if(isset($info['classification_category'])){
                    $topData[$i]['company']=$info['classification_category']['company_id'];
                    $topData[$i]['classification_post']=$info['classification_category']['post_id'];
                    $topData[$i]['subcategory']=$info['classification_category']['name'];
                    $topData[$i]['subcategoria_id']=$info['classification_category']['subcategoria_id'];
                    $topData[$i]['classification']='Clasificado';
                }
                $i++;
            }
            else{
                $topData=[];
                continue;
            }
        }

        $pdf = \PDF::loadView('PDF.pdf', compact('topData'));
        return $pdf->download('TOP 10 de reacciones.pdf');
    }

    public function typeReportPdf(NumberWhatsapp $numberWhatsapp){

        if($numberWhatsapp->report == 1 || $numberWhatsapp->report == 2 ||$numberWhatsapp->report == 3 ||$numberWhatsapp->report == 4 ){
            $top = $this->TopTen($numberWhatsapp);
            if($top[0]->status == 'true' && $top[1]->status == 'true'){
                return redirect()->route('Telephone.index')->with('info', 'Se ha enviado con exito el PDF con el TOP 10 de reacciones');
            }
            else{
                return redirect()->route('Telephone.index')->with('info', 'No ha sido posible enviar el PDF con el TOP 10 de reacciones');
            }
        }

        elseif($numberWhatsapp->report == 5 || $numberWhatsapp->report == 6 ||$numberWhatsapp->report == 7 ||$numberWhatsapp->report == 8 ){
            $top = $this->TopAnalysis($numberWhatsapp);
            $top = json_decode($top);
            if($top->status == 'true'){
                return redirect()->route('Telephone.index')->with('info', 'Se ha enviado con exito análisis Top 10 de reacciones');
            }
            else{
                return redirect()->route('Telephone.index')->with('info', 'No ha sido posible enviar el análisis Top 10 de reacciones');
            }
        }

        elseif($numberWhatsapp->report == 9 || $numberWhatsapp->report == 10 ||$numberWhatsapp->report == 11 ||$numberWhatsapp->report == 12 ){
            $top = $this->TopComparator($numberWhatsapp);
            $top = json_decode($top);
            if($top->status == 'true'){
                return redirect()->route('Telephone.index')->with('info', 'Se ha enviado con exito la información del tema en el Top 5');
            }
            else{
                return redirect()->route('Telephone.index')->with('info', 'No ha sido posible enviar la información del tema en el Top 5');
            }
        }

        elseif($numberWhatsapp->report == 13 || $numberWhatsapp->report == 14 ||$numberWhatsapp->report == 15 ||$numberWhatsapp->report == 16 ){
            $top = $this->BublesContent($numberWhatsapp);
            $top = json_decode($top);
            if($top->status == 'true'){
                return redirect()->route('Telephone.index')->with('info', 'Se ha enviado con exito las palabras de los contenidos');
            }
            else{
                return redirect()->route('Telephone.index')->with('info', 'No ha sido posible enviar las palabras de los contenidos');
            }
        }
    }

    public function TopTen($numberWhatsapp){
        $endDateTime = Carbon::now()->subHours(24);
        //Top 10 de publicaciones con mas interacciones
        $data = array();
        $topData = array();
        $reacciones = Reaction::where('created_at', '>=', $endDateTime)->orderBy('created_at', 'DESC')->get();
        foreach ($reacciones as $item) {
            $reactions = Reaction::where('post_id', '=', $item['post_id'])->first();
            (!$reactions) ? $totalLinea = 0 : $totalLinea = $reactions['likes'] + $reactions['sad'] + $reactions['haha'] + $reactions['angry'] + $reactions['love'] + $reactions['wow'] + $reactions['shared'];
            $data[$item['post_id']]['count'] = $totalLinea;
            $data[$item['post_id']]['posteo'] = $item['post_id'];
            $data[$item['post_id']]['reacction'] = $reactions;
        }
        $keys = array_column($data, 'count');
        array_multisort($keys, SORT_DESC, $data);
        $resultado = array_slice($data, 0, 10);
        $i = 0;
        foreach ($resultado as $post) {
            $info = Post::where('post_id', $post['posteo'])->with('attachment')->first();
            if ($info) {
                $topData[$i]['date'] = $info->created_time;
                $topData[$i]['name'] = $info->page_name;
                $topData[$i]['content'] = $info->content;
                $topData[$i]['reaction'] = $post['reacction'];
                $topData[$i]['count'] = $post['count'];

                if (isset($info['attachment']['picture'])) {
                    $topData[$i]['attachment'] = $info['attachment']['picture'];
                }
                $i++;
            } else {
                $topData = [];
                continue;
            }
        }

        $api = ApiWhatsapp::first();
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';
        $client_id = $api->client_id;
        $instance = $api->instance;

        $content = PDF::loadView('PDF.pdf', compact('topData'))->output();
        $fileName = 'Top-' . Carbon::now()->format('Y-m-d') . '_hora_' . Carbon::now()->format('H-i') . '.pdf';
        $file = Storage::disk('public_files')->put($fileName, $content);
        $path = Storage::disk('public_files')->path($fileName);

        $contacto = $numberWhatsapp->numeroTelefono;
        $group_id = $numberWhatsapp->group_id;

        $name = $this->eliminar_acentos(str_replace(' ', '+', $numberWhatsapp->descripcion));
        $mensaje = 'Hola+' . $name . '!+Adjunto+el+siguiente+link+con+los+datos+de+los+top+10+de+los+temas+con+m%C3%A1s+interacciones+en+las+redes+sociales';

        if($contacto || $contacto != 0){
            $urlBody = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . $mensaje . '&type=text';
            $urlPdf = 'https://wapiad.com/api/send.php?client_id=' . $client_id . '&instance=' . $instance . '&number=' . $contacto . '&message=' . env('APP_URL') . 'whatsapp_files/' . $fileName . '&type=image';
            //$urlPdf ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message=https://monitoreo.cornel.io/whatsapp_files/Top-2021-06-25_hora_16-21.pdf&type=image';
        }

        elseif($group_id || $group_id != 0){
            $urlBody = 'https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$mensaje.'&type=file';
            $urlPdf = 'https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.env('APP_URL').'whatsapp_files/'.$fileName.'&type=file';
            //$urlPdf = 'https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message=https://monitoreo.cornel.io/whatsapp_files/Top-2021-06-25_hora_16-21.pdf&type=file';
        }

        $resultM = file_get_contents($urlBody);
        $resultP = file_get_contents($urlPdf);

        $dataM = json_decode($resultM);
        $dataP = json_decode($resultP);

        return [$dataM, $dataP];

    }

    public function TopAnalysis($numberWhatsapp){
        $data_pdf = TopReaction::orderby('created_at','DESC')->take(10)->get();
        $i = 0;

        $api = ApiWhatsapp::first();
        $client_id = $api->client_id;
        $instance = $api->instance;
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';

        $info = [];
        $arrayId = [];
        $arrayName = [];
        $arrayPosicion = [];

        $contacto = $numberWhatsapp->numeroTelefono;
        $group_id = $numberWhatsapp->group_id;
        //$contacto = '50686258376';

        $companie_top = $numberWhatsapp->company_id;
        $companie_n = Company::where('id', $numberWhatsapp->company_id)->first();
        $companie_name = str_replace('Prensa', '', $companie_n->nombre);
        $name = $this->eliminar_acentos(str_replace(' ', '', $companie_name));

        foreach ($data_pdf as $pdf) {
            $companie_pdf = $pdf->company;
            if ($companie_top == $companie_pdf) {
                $tema = Subcategory::where('id', $pdf->classification)->first();
                $info[$i]['posicion'] = $pdf->position;
                $info[$i]['tema'] = $tema->name;
                $info[$i]['tema_id'] = $pdf->classification;
                $info[$i]['companie'] = $tema->company_id;
                array_push($arrayId, $pdf->classification);
                array_push($arrayName, $tema->name);
                array_push($arrayPosicion, $pdf->position);
                $i++;
            }
        }
        if ($info != [] ) {
            $url_app = env("APP_URL");

            $uniqueId = array_unique($arrayId);
            $unique = array_unique($arrayName);

            $nameT = implode("%2C+", $unique);
            $positionTopics = implode("%2C+", $arrayPosicion);
            $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT));
            if ($i >= 1) {
                if (count(array_unique($unique)) == 1 && count(array_unique($uniqueId))==1) {
                    $message = 'El+tema+' . $nameTopics . '+figura+en+el+Top+10+en+la+posici%C3%B3n+' . $positionTopics . '.';

                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
                    }
                    $resultado = file_get_contents($urlMessage);

                    foreach ($uniqueId as $uniqueLInk){
                        $id_encry = base64_encode($uniqueLInk);
                        $link = "{$url_app}analysisLink/{$id_encry}/";
                        $messageLink = 'En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+'.$nameTopics.'+' . $link . '';

                        if($contacto || $contacto != 0){
                            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                        }
                        elseif($group_id || $group_id != 0){
                            $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$messageLink.'&type=file';
                        }
                        $resultado = file_get_contents($urlMessage);
                    }
                }
                else {
                    $message = 'Los+temas+' . $nameTopics . '+figuran+entre+el+Top+10+en+la+posici%C3%B3n' . $positionTopics . '.';

                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=text';
                    }
                    $resultado = file_get_contents($urlMessage);

                    foreach ($uniqueId as $infoPosicion){
                        $id_encry = base64_encode($infoPosicion);
                        $nameT = Subcategory::where('id', $infoPosicion)->first();
                        $link = "{$url_app}analysisLink/{$id_encry}/";
                        $nameTopics = $this->eliminar_acentos(str_replace(' ', '+', $nameT->name));
                        $messageLink = 'En+el+siguiente+link+podr%C3%A1n+ver+el+an%C3%A1lisis+de+sentimiento+de+la+conversaci%C3%B3n+y+la+nube+de+palabras+del+tema+'.$nameTopics.'+' . $link . '';

                        if($contacto || $contacto != 0){
                            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$messageLink.'&type=text&=';
                        }
                        elseif($group_id || $group_id != 0){
                            $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$messageLink.'&type=file';
                        }
                        file_get_contents($urlMessage);
                    }
                }
            }
            else {
                $message = 'No+se+identifica+ning%C3%BAn+tema+para+' . $name . '';

                if($contacto || $contacto != 0){
                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                }
                elseif($group_id || $group_id != 0){
                    $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
                }
                $resultado = file_get_contents($urlMessage);
            }
        }
        else {
            $message = 'No+se+identifica+ning%C3%BAn+tema+para+' . $name . '';
            if($contacto || $contacto != 0){
                $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
            }
            elseif($group_id || $group_id != 0){
                $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
            }
            $resultado = file_get_contents($urlMessage);
        }
        return $resultado;
    }

    public function TopComparator($numberWhatsapp){
        $data_pdf = TopReaction::orderby('created_at','DESC')->take(10)->get();
        $i = 0;

        $api = ApiWhatsapp::first();
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';
        $client_id = $api->client_id;
        $instance = $api->instance;

        $info = [];
        $arrayId = [];
        $arrayName = [];
        $arrayPosicion = [];

        $contacto = $numberWhatsapp->numeroTelefono;
        $group_id = $numberWhatsapp->group_id;

        $companie_top = $numberWhatsapp->company_id;
        $companie_name = Company::where('id',$numberWhatsapp->company_id)->first();

        foreach ($data_pdf as $pdf){
            $companie_pdf = $pdf->company;
            if($companie_top == $companie_pdf){
                $tema = Subcategory::where('id', $pdf->classification)->first();
                if($pdf->position  <= 5 ){
                    $info[$i]['posicion'] = $pdf->position;
                    $info[$i]['tema'] = $tema->name;
                    $info[$i]['tema_id'] = $pdf->classification;

                    array_push($arrayId, $pdf->classification);
                    array_push($arrayName, $tema->name);
                    array_push($arrayPosicion, $pdf->position);
                    $i++;
                }
            }
        }

        if($info != [] || $info != null){
            $uniqueId = array_unique($arrayId);
            $uniqueName = array_unique($arrayName);
            $url_app = env("APP_URL");
            $start = base64_encode(Carbon::now()->subHour(72));
            $end = base64_encode(Carbon::now());

            if (count(array_unique($uniqueId)) == 1 && count(array_unique($uniqueName)) == 1) {
                $idTopics = implode("", $uniqueId);
                $nameT = implode("", $uniqueName);

                $sub = base64_encode($idTopics);
                $link = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                $message = 'En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+'.$nameT.'+'.$link.'';

                if($contacto || $contacto != 0){
                    $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                }
                elseif($group_id || $group_id != 0){
                    $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
                }
                $resultado = file_get_contents($urlMessage);
            }
            else{
                foreach ($uniqueId as $infoPosicion){
                    $sub = base64_encode($infoPosicion);
                    $nameTopics = Subcategory::where('id', $infoPosicion)->first();
                    $name = $this->eliminar_acentos(str_replace(' ', '', $nameTopics->name));

                    $link = "{$url_app}topicsComparator/{$sub}/{$start}/{$end}";
                    $message = 'En+el+siguiente+link+encontrar%C3%A1s+el+impacto+general+del+tema+'.$name.'+'.$link.'';

                    if($contacto || $contacto != 0){
                        $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
                    }
                    elseif($group_id || $group_id != 0){
                        $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
                    }

                    $resultado = file_get_contents($urlMessage);
                }
            }
        }
        return $resultado;
    }

    public function BublesContent($numberWhatsapp){
        $api = ApiWhatsapp::first();
        //$client_id = '28c32e382fe61d66c0c2c6d694a9f737';
        //$instance = '0c7867b9ee967783f17a24195c1f1bb1';
        $client_id = $api->client_id;
        $instance = $api->instance;

        $contacto = $numberWhatsapp->numeroTelefono;
        $group_id = $numberWhatsapp->group_id;
        //$contacto = '50661418599';
        $nameUser = $this->eliminar_acentos(str_replace(' ', '+', $numberWhatsapp->descripcion));

        $company = $numberWhatsapp->company_id;
        $url_app = env("APP_URL");
        $comp = base64_encode($company);
        $link = "{$url_app}BublesContent/{$comp}";

        $message = 'Hola+'.$nameUser.'%2C+as%C3%AD+est%C3%A1+la+conversaci%C3%B3n+de+las+Redes+Sociales+con+relaci%C3%B3n+a+temas+de+mayor+relevancia+en+Medios+de+Comunicaci%C3%B3n%2C+Diputados+y+Grupos+Sindicales.+'.$link.'+';

        if($contacto || $contacto != 0){
            $urlMessage ='https://wapiad.com/api/send.php?client_id='.$client_id.'&instance='.$instance.'&number='.$contacto.'&message='.$message.'&type=text&=';
        }
        elseif($group_id || $group_id != 0){
            $urlMessage ='https://wapiad.com/api/sendgroupmsg.php?client_id='.$client_id.'&instance='.$instance.'&group_id='.$group_id.'&message='.$message.'&type=file';
        }
        $resultado = file_get_contents($urlMessage);
        //dd(file_get_contents($urlMessage));

        return $resultado;
    }
}
