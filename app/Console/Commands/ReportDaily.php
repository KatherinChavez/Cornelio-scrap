<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Traits\ScrapTrait;
use App\Traits\TopicCountTrait;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Mail;
use QuickChart;
use function Symfony\Component\Translation\t;


class ReportDaily extends Command
{
    use TopicCountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ReportDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte diario';
    public $subject;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $comp = Company::where('status',1)->get();
        $k = 0;
        foreach ($comp as $c) {
            $email = $c->emailCompanies;
            //Se llaman todos los temas que se encuentra registrado en la compania
            //$temas = Subcategory::where('company_id', 21)->get();
            $temas = Subcategory::where('company_id', $c->id)->get();
            $start_time = Carbon::now()->subDays(1);
            $end_time = Carbon::now();

            $start_time_for_query = Carbon::parse($start_time);
            $end_time_for_query = Carbon::parse($end_time)->addDay();
            $posts = [];
            $tema= [];
            $adjunto = "";
            $publicaciones = "";
            $j = 0;

            $sub_alerta = [];
            $info = [];
            foreach($temas as $topic) {
                $alerta = Alert::where('subcategory_id', '=', $topic->id)->first();
                $alerta??$alerta['report'] = 1;

                $sub = array('sub' => $topic->name);
                if ($alerta['report'] == 1) {
                    //Se llama las publicaciones clasificadas cuando el tema sea igual al objeto topic
                    $posts = Classification_Category::where('classification_category.subcategoria_id', '=', $topic->id)
                        ->whereBetween('classification_category.created_at', [$start_time_for_query, $end_time_for_query])
                        ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
                        ->join('attachments', 'attachments.post_id', '=', 'posts.post_id')
                        ->select('posts.*', 'attachments.picture', 'attachments.video', 'attachments.url', 'attachments.title')
                        ->orderBy('posts.created_time', 'desc')
                        ->distinct('classification_category.post_id')
                        ->groupby('classification_category.post_id')
                        ->get();

                    //Consulta si se encuentra datos en el objeto post
                    if(count($posts) > 0){
                        $sub_alerta[] = $topic;

                        /********************************** SE OBTIENE LOS TEMA / SUBCATEGORIA*************************/
                        $rating = Classification_Category::where('subcategoria_id', $topic->id)
                            ->whereBetween('classification_category.created_at',[$start_time_for_query, $end_time_for_query])
//                            ->join('attachments', 'attachments.post_id', 'classification_category.post_id')
                            ->join('comments', 'comments.post_id', '=', 'classification_category.post_id')
                            ->join('sentiments', 'sentiments.comment_id', '=', 'comments.comment_id')
                            ->distinct('post_id')
                            ->take(100)
                            ->get();

                        $interation = 0;
                        $positivo = 0;
                        $negativo = 0;
                        $neutral = 0;

                        //Se obtiene los sentimientos
                        foreach ($rating as $comment) {
                            if ($comment['sentiment'] == 'Positivo') {
                                $positivo++;
                            } elseif ($comment['sentiment'] == 'Negativo') {
                                $negativo++;
                            } elseif ($comment['sentiment'] == 'Neutral') {
                                $neutral++;
                            }
                            $interation++;
                        }
                        $tema['Tema'] = $topic->name;
                        $tema['negativo'] = $interation ? round((($negativo / $interation) * 100),2) : $interation;;
                        $tema['neutral'] = $interation ? round((($neutral / $interation) * 100),2) : $interation;
                        $tema['positivo'] = $interation ? round((($positivo / $interation) * 100),2) : $interation;
                        /********************************** SE GRIFICA LOS SENTIMIENTOS *******************************************/

                        $labels[] = strtolower($tema['Tema']);
                        $pie = new QuickChart(array(
                            'width' => 600,
                            'height' => 300,
                            'backgroundColor' => 'white',
                        ));
                        $pie->setConfig('{
                                  type: "pie",
                                  data: {
                                    labels:["Positivo", "Negativo", "Neutral"],
                                    datasets: [{
                                      label: "' . $tema['Tema'] . '",
                                      data: ['.$tema['positivo'].', '.$tema['negativo'].','.$tema['neutral'].'],
                                      backgroundColor: ["#317f43", "#CB3234", "#ffff00"]
                                    }]
                                  },
                                  options: {
                                    title: {
                                      display: true,
                                      text: "Sentimiento de conversación del tema ' . $tema['Tema'] . ' en las Últimas 24 horas"
                                    }
                                  }
                                }'
                        );
                        $b = $pie->getUrl();

                        /*************************************** TEMAS MAS HABLADO ************************************************/
                        $infoTemas=array();
                        $h=0;
                        $y = 0;
                        $word=$this->TopicCount($rating);
                        foreach ($word as $interact){
                            foreach ($interact as $valor) {
                                foreach ($valor as $key => $value) {
                                    $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                                        "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",
                                        "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https" );
                                    $result_replace = str_ireplace($array_replace_words,"", $value['word']);
                                    if($result_replace != "" &&  strlen($result_replace)>=4){
                                        $infoTemas[$y]['word'] = $value['word'];
                                        $infoTemas[$y]['count'] = $value['count'];
                                        $y++;
                                    }
                                    else{
                                        continue;
                                    }
                                }
                            }
                        }
                        $temp = array_unique(array_column($infoTemas, 'word'));
                        $infoTemas = array_intersect_key($infoTemas, $temp);
                        $keysContent = array_column($infoTemas,'count');
                        array_multisort($keysContent, SORT_DESC, $infoTemas);
                        $responseTemas['temas'][$h]['datos'] = array_slice($infoTemas, 0, 10);
                        $h++;

                        /********************************** DATOS QUE CONTIENE LA PUBLICACION *************************************/
                        $i = 0;
                        foreach ($posts as $post) {
                            $total = 0;
                            $post_id = $post['post_id'];

                            /************************************************************ Reacciones ***************************************************************/
                            $reacciones = Reaction::where('post_id', '=', $post_id)->first();
                            if ($reacciones == null) {
                                $reacciones['likes'] = 0;
                                $reacciones['love'] = 0;
                                $reacciones['haha'] = 0;
                                $reacciones['wow'] = 0;
                                $reacciones['sad'] = 0;
                                $reacciones['angry'] = 0;
                                $reacciones['shared'] = 0;
                            }
                            $total = $reacciones['likes'] + $reacciones['love'] + $reacciones['haha'] + $reacciones['wow'] + $reacciones['sad'] + $reacciones['angry'] + $reacciones['shared'];

                            /************************************************************ Comentarios ***************************************************************/
                            $coments = Comment::where('post_id', '=', $post_id)->count();
                            $random = Comment::where('post_id', '=', $post_id)->inRandomOrder()->limit(10)->get();

                            $imagen = "";
                            $posts[$i]['random'] = $random;
                            $posts[$i]['comentarios'] = $coments;
                            $posts[$i]['TotalReacciones'] = $total;
                            $posts[$i]['like'] = $reacciones['likes'];
                            $posts[$i]['love'] = $reacciones['love'];
                            $posts[$i]['haha'] = $reacciones['haha'];
                            $posts[$i]['wow'] = $reacciones['wow'];
                            $posts[$i]['sad'] = $reacciones['sad'];
                            $posts[$i]['anger'] = $reacciones['angry'];
                            $posts[$i]['shared'] = $reacciones['shared'];
                            $i++;

                            if ($post['picture']) {
                                $image = str_replace("AND", "&", $post['picture']);
                                $adjunto = $imagen;
                                if ($post['url']) {
                                }
                            }
                            if ($post['video']) {
                                $video = "";
                                $image = str_replace("AND", "&", $post['picture']);
                            }
                        }

                        $info[$k]['name'] = $topic->name;
                        $info[$k]['posts'] = $posts;
                        $info[$k]['tema'] = $tema;
                        $info[$k]['grafica'] = $b;
                        $info[$k]['word'] = $responseTemas;
                        $k++;
                    }
                }
            }
            /************************************** ENVIO DE DATOS ****************************************************/
            $fecha = Carbon::now();
            $this->subject = 'Reporte diario con corte al ' . $fecha->format('Y-m-d H:i');
            $data = array('posts' => $posts, 'subcategorias' => $sub_alerta);

            //if ($sub_alerta) {
                Mail::send('Notification.Mail', ['info' => $info], function ($message) use ($email) {
                    $message
                        ->to($email)
                        ->bcc('htatianachavez@gmail.com')
                        ->subject($this->subject);

                });
            //}
        }
    }


    public function handle_Original()
    {
        $comp = Company::where('status',1)->get();
        foreach ($comp as $c) {
            $email = $c->emailCompanies;
            //Se llaman todos los temas que se encuentra registrado en la compania
            $temas = Subcategory::where('company_id', $c->id)->get();
            $start_time = Carbon::now()->subDays(1);
            $end_time = Carbon::now();

            $start_time_for_query = Carbon::parse($start_time);
            $end_time_for_query = Carbon::parse($end_time)->addDay();
            $posts = [];
            $tema= [];
            $adjunto = "";
            $publicaciones = "";
            $j = 0;
            $sub_alerta = [];

            foreach($temas as $topic) {
                $alerta = Alert::where('subcategory_id', '=', $topic->id)->first();
                $alerta??$alerta['report'] = 1;

                $subcategoria_id = $topic['id'];
                $sub = array('sub' => $topic->name);
                if ($alerta['report'] == 1) {
                    //Se llama las publicaciones clasificadas cuando el tema sea igual al objeto topic
                    $post = Classification_Category::where('classification_category.subcategoria_id', '=', $topic->id)
                        ->whereBetween('classification_category.created_at', [$start_time_for_query, $end_time_for_query])
                        ->join('posts', 'posts.post_id', '=', 'classification_category.post_id')
                        ->join('attachments', 'attachments.post_id', '=', 'posts.post_id')
                        ->select('posts.*', 'attachments.picture', 'attachments.video', 'attachments.url', 'attachments.title')
                        ->orderBy('posts.created_time', 'desc')
                        ->distinct('post_id')
                        ->groupby('post_id')
                        ->get()
                        ->toArray();

                    //Consulta si se encuentra datos en el objeto post
                    if($post){
                        if (array_key_exists(0, $post)) {
                            //Por lo datos que se encuentre se almacena en un array
                            $post[0] = array_merge($post[0], $sub);
                        }
                        $posts = array_merge($posts, $post);
                        $sub_alerta[] = $topic;
                    }
                }
            }

            /********************************** SE OBTIENE LOS TEMA / SUBCATEGORIA**************************************/
            foreach ($sub_alerta as $sub){
                $rating = Classification_Category::where('subcategoria_id', $sub->id)
                    ->whereBetween('classification_category.created_at',[$start_time_for_query, $end_time_for_query])
                    ->join('attachments', 'attachments.post_id', 'classification_category.post_id')
                    ->join('comments', 'comments.post_id', '=', 'classification_category.post_id')
                    ->join('sentiments', 'sentiments.comment_id', '=', 'comments.comment_id')
                    ->distinct('post_id')
                    ->get();

                $interation = 0;
                $positivo = 0;
                $negativo = 0;
                $neutral = 0;

                //Se obtiene los sentimientos
                foreach ($rating as $comment) {
                    if ($comment['sentiment'] == 'Positivo') {
                        $positivo++;
                    } elseif ($comment['sentiment'] == 'Negativo') {
                        $negativo++;
                    } elseif ($comment['sentiment'] == 'Neutral') {
                        $neutral++;
                    }
                    $interation++;
                }
                $tema[$j]['Tema'] = $sub->name;
                $tema[$j]['negativo'] = $interation ? ($negativo / $interation) * 100 : $interation;;
                $tema[$j]['neutral'] = $interation ? ($neutral / $interation) * 100 : $interation;
                $tema[$j]['positivo'] = $interation ? ($positivo / $interation) * 100 : $interation;
                $j++;
            }

            /********************************** SE GRIFICA LOS SENTIMIENTOS *******************************************/
            $a = 0;
            foreach($tema as $detail){
                $labels[] = strtolower($detail['Tema']);
                $pie = new QuickChart(array(
                    'width' => 600,
                    'height' => 300,
                    'backgroundColor' => 'white',
                ));
                $pie->setConfig('{
                                  type: "pie",
                                  data: {
                                    labels:["Positivo", "Negativo", "Neutral"],
                                    datasets: [{
                                      label: "' . $detail['Tema'] . '",
                                      data: ['.$detail['positivo'].', '.$detail['negativo'].','.$detail['neutral'].']
                                    }]
                                  },
                                  options: {
                                    title: {
                                      display: true,
                                      text: "Sentimiento de conversación del tema ' . $detail['Tema'] . '"
                                    }
                                  }
                                }'
                );
                $b[$a] = $pie->getUrl();
                $a++;
            }

            /********************************** DATOS QUE CONTIENE LA PUBLICACION *************************************/
            $i = 0;
            foreach ($posts as $post) {
                $total = 0;
                $post_id = $post['post_id'];

                /************************************************************ Reacciones ***************************************************************/
                $reacciones = Reaction::where('post_id', '=', $post_id)->first();
                if ($reacciones == null) {
                    $reacciones['likes'] = 0;
                    $reacciones['love'] = 0;
                    $reacciones['haha'] = 0;
                    $reacciones['wow'] = 0;
                    $reacciones['sad'] = 0;
                    $reacciones['angry'] = 0;
                    $reacciones['shared'] = 0;
                }
                $total = $reacciones['likes'] + $reacciones['love'] + $reacciones['haha'] + $reacciones['wow'] + $reacciones['sad'] + $reacciones['angry'] + $reacciones['shared'];

                /************************************************************ Comentarios ***************************************************************/
                $coments = Comment::where('post_id', '=', $post_id)->count();
                $random = Comment::where('post_id', '=', $post_id)->inRandomOrder()->limit(10)->get();


                $image = "";
                $imagen = "";
                $video = "";
                $posts[$i]['random'] = $random;
                $posts[$i]['comentarios'] = $coments;
                $posts[$i]['TotalReacciones'] = $total;
                $posts[$i]['like'] = $reacciones['likes'];
                $posts[$i]['love'] = $reacciones['love'];
                $posts[$i]['haha'] = $reacciones['haha'];
                $posts[$i]['wow'] = $reacciones['wow'];
                $posts[$i]['sad'] = $reacciones['sad'];
                $posts[$i]['anger'] = $reacciones['angry'];
                $posts[$i]['shared'] = $reacciones['shared'];


                $i++;
                if ($post['picture']) {
                    $image = str_replace("AND", "&", $post['picture']);
                    $adjunto = $imagen;
                    if ($post['url']) {
                    }
                }
                if ($post['video']) {
                    $video = "";
                    $image = str_replace("AND", "&", $post['picture']);
                }
            }

            /*************************************** TEMAS MAS HABLADO ************************************************/
            $infoTemas=array();
            $h=0;
            $y = 0;
            $word=$this->TopicCount($rating);
            foreach ($word as $interact){
                foreach ($interact as $valor) {
                    foreach ($valor as $key => $value) {
                        $array_replace_words = array('la', 'el', 'para','debe', 'www', 'pag', 'anos', "ha", "Nº", "ese", "han", "tanto",
                            "Se", "le", "http","https", "dado", "debido", "duda", "fin", 'han', "un"," poco", "Les", "•","🔴", "🚨",
                            "com", "ano", "https"," https ",  "📺","💻", " Este ", "aqui", " gran ","📻","➡", " cada ", "https" );
                        $result_replace = str_ireplace($array_replace_words,"", $value['word']);
                        if($result_replace != "" &&  strlen($result_replace)>=4){
                            $infoTemas[$y]['word'] = $value['word'];
                            $infoTemas[$y]['count'] = $value['count'];
                            $y++;
                        }
                        else{
                            continue;
                        }
                    }
                }
            }
            $temp = array_unique(array_column($infoTemas, 'word'));
            $infoTemas = array_intersect_key($infoTemas, $temp);
            $keysContent = array_column($infoTemas,'count');
            array_multisort($keysContent, SORT_DESC, $infoTemas);
            $responseTemas['temas'][$h]['datos'] = array_slice($infoTemas, 0, 10);
            $h++;

            /************************************** ENVIO DE DATOS ****************************************************/
            $fecha = Carbon::now();
            $this->subject = 'Reporte diario con corte al ' . $fecha->format('Y-m-d H:i');
            $data = array('posts' => $posts, 'subcategorias' => $sub_alerta);
            if ($sub_alerta) {
                Mail::send('Notification.PDF', ['data' => $data, 'posts' => $posts, 'b' => $b, 'responseTemas'=>$responseTemas], function ($message) use ($email) {
                    $message
                        ->to($email)
                        ->bcc('htatianachavez@gmail.com')
                        ->subject($this->subject);

                });
            }
        }
    }

}
