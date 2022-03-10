<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Compare;
use App\Models\Megacategory;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportNotificationController extends Controller
{

    function SendTelegram(Request $request){
        $channel=Subcategory::where('id','=',$request->subcategoria_id)->select('channel','name')->get();
        if($channel!=Null){
            $token = "434968623:AAF4tSy8bRke2FrOZCeym_L0WFwv1jB9gpg";
            $url   = "https://api.telegram.org/bot$token/sendMessage";
            //$canal="-100";
            $canal_agencia="1144464904";
            $grupo_agencia="275153292";
            //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
            // $chat_id =$canal.$canal_agencia;
            //$chat_id =$canal.$channel[0]['channel'];
            $channel = $channel[0]['channel'];
            $find = stripos($channel, "-100");
            if($find == false){
                //si es a un canal agregar -100 y solo la primer parte del id si es chat normal solo id
                $canal="-100";
                $chat_id =$canal.$channel;
            }else{
                $chat_id =$channel;
            }

            $comp=$channel[0]['name'];

            $data = array(
                "chat_id" => $chat_id,
                "text" => "Hola! tengo la siguiente alerta, relacionada con: ".$comp.", acá te dejo el link para que la veas: https://www.facebook.com/".$request->post_id
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            echo curl_exec($ch);
            return $channel;

        }


    }


    public function ManagementView($com,$post,$sub){

        $post=base64_decode($post);
        $sub=base64_decode($sub);
        $com=base64_decode($com);
        $subcategorias=Subcategory::where('company_id',$com)->get();
        $post=Post::where('posts.post_id','=',$post)
            ->join('attachments','attachments.post_id','=','posts.post_id')
            ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title')
            ->orderBy('posts.created_time', 'desc')
            ->first();
        $data=array('post'=>$post,'subcategoria'=>$sub,'compania'=>$com,'subcategories'=>$subcategorias);
        return view('Notification.Management',$data);

    }

    public function megacategoryNotifications(Request $request){
        $mega=Megacategory::where('company_id','=',$request->compania)->get();
        return $mega;
    }

    public function SendReportLink($idE,$startE,$endE){
        $id=base64_decode($idE);
        $start=base64_decode($startE);
        $end=base64_decode($endE);

        $subcategorias=Subcategory::where('company_id',$id)->get();
        $start_time = ($start != "") ? $start : Carbon::now()->subDays(1);
        $end_time = ($end != "") ? $end : Carbon::now();
        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time)->addDays(1);

        $posts=[];
        $fechas=array('start'=>$start,'end'=>$end);
        $reacciones=0;
        $adjunto="";
        $publicaciones="";

        foreach ($subcategorias as $subcategoriaData){

            $subcategoria=$subcategoriaData['id'];
            $categoria=Subcategory::where('id','=',$subcategoria)->select('name')->first();
            $sub=array('sub'=>$categoria['name'],'id'=>$subcategoria, 'companie_id'=>$id);

            $post=Classification_Category::where('classification_category.subcategoria_id','=',$subcategoria)
                ->whereBetween('classification_category.created_at',[$start_time_for_query , $end_time_for_query])
                ->join('posts','posts.post_id','=','classification_category.post_id')
                ->join('subcategory','subcategory.id','=','classification_category.subcategoria_id')
                ->join('attachments','attachments.post_id','=','posts.post_id')
                ->select('posts.*','attachments.picture','attachments.video','attachments.url','attachments.title','subcategory.name')
                ->orderBy('posts.created_time', 'desc')
                ->distinct('posts.post_id')
                ->get()
                ->toArray();

            if(array_key_exists(0, $post)){
                $post[0]=array_merge($post[0],$sub);
            }
            $posts= array_merge($posts,$post);
        }
        $i=0;

        foreach ($posts as $post){
            $total=0;
            $post_id=$post['post_id'];


            $reactions=Reaction::where('post_id','=',$post_id)->first();
            $reacciones = ($reactions != "") ? $reactions['wow']+$reactions['love']+$reactions['likes']+$reactions['sad']+$reactions['angry']+$reactions['haha']+$reactions['shared'] : $reactions = 0;
            $total= $reacciones;

            $coments=Comment::where('post_id','=',$post_id)->count();
            $image="";
            $imagen="";
            $video="";
            $posts[$i]['comentarios']=$coments;
            $posts[$i]['reacciones']=$total;
            $i++;
            if($post['picture']){
                $image=str_replace("AND", "&", $post['picture']);
                $adjunto=$imagen;
                if($post['url']){
                }
            }
            if($post['video']){
                $video=
                $image=str_replace("AND", "&", $post['picture']);
            }
        }

        $data=array('posts'=>$posts,'subcategorias'=>$subcategorias,'fechas'=>$fechas,'companie'=>$id);
        return view('Notification.SendReportLink',$data);
    }

}
