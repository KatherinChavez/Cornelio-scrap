<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Scraps;
use App\Models\Sentiment;
use App\Models\Word;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Classification_Category;
use App\Models\Compare;
use App\Models\Notification;
use App\Models\NumberWhatsapp;
use App\Models\Post;
use App\Models\Subcategory;
use Carbon\Carbon;

class ClassificationWord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClassificationWord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clasificacion automatica las palabras de los comentarios';

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
        // inicializa fechas
        $start_time =  Carbon::now()->subDays(1);
        $end_time =  Carbon::now()->addDay(1);

        //Se llama las palabras
        $word = Word::get();

        //Se llama los comentarios que se encuentra clasificado
        $comments = Classification_Category::whereBetween('created_at',[$start_time , $end_time])
            ->with('comments')
            ->orderby('created_at', 'DESC')
            ->get();

        //recorre cada palabra para comparar en los post
        foreach ($word as $palabra){
            $comp=$palabra['word'];
            $sentimiento=$palabra['sentiment'];

            //recorre cada uno de los comentarios que se encuentra clasifcado
            foreach ($comments as $comment){
                if(isset($comment['comments'])){
                    foreach ($comment['comments'] as $cont){
                        $content=$cont->comment;
                        $comment_id = $cont->comment_id;
                        $find = stripos($content, $comp);
                        if($find == true){
                            $consulta = Sentiment::where('comment_id', $comment_id)->first();
                            if(!$consulta){
                                $getSentiment = Sentiment::create([
                                    'comment_id' => $comment_id,
                                    'sentiment' => $sentimiento,
                                    'estado' => '0',
                                ]);
                            }
                            continue;
                        }
                    }
                }
            }
        }

        // Se debera consultar por los comemtarios que no se encuentra como positivo ni negativo
        foreach ($comments as $comentario){
            if(isset($comentario['comments'])){
                foreach ($comentario['comments'] as $opinion){
                    //dd($comment['comments'] );
                    $id_comment = $opinion->comment_id;
                    $consulta = Sentiment::where('comment_id', $id_comment)->first();
                    if(!$consulta){
                        $getSentiment = Sentiment::create([
                            'comment_id' => $id_comment,
                            'sentiment' => 'Neutral',
                            'estado' => '0',
                        ]);
                    }
                }
            }
        }
        dd('FINAL');
    }
}
