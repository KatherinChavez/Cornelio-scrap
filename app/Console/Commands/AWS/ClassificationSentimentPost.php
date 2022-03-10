<?php

namespace App\Console\Commands\AWS;

use App\Models\Sentiment_posts;
use App\Traits\AWS_Trait;
use Illuminate\Console\Command;
use App\Models\Classification_Category;
use Carbon\Carbon;

class ClassificationSentimentPost extends Command
{
    use AWS_Trait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClassificationSentimentPost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clasificacion automatica de los sentimiento de la publicación';

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
        $end_time   =  Carbon::now()->addDay(1);

        $client = $this->query_AWS();

        $clasifications = Classification_Category::with(['post'])
            ->whereBetween('created_at',[$start_time , $end_time])
            ->orderBy('created_at','desc')
            ->get();

        foreach ($clasifications as $post){
            if(isset($post->post) && strlen($post->post->content) > 20){
                dd($post);
                $content = $post->post->content;
                try{
                    $result = $client->batchDetectSentiment([
                        'LanguageCode' => 'es',
                        'TextList' => [$content],
                    ]);

                    if(count($result['ResultList']) > 0) {
                        $sentiment = $result['ResultList'][0]['Sentiment'];
                        $score = 0;

                        foreach ($result['ResultList'][0]['SentimentScore'] as $key => $value) {
                            if (strtolower($key) === strtolower($sentiment)) {
                                $score = $value;
                            }
                        }

                        switch ($sentiment){
                            Case "POSITIVE":
                                $sentiment = "Positivo";
                                break;
                            Case "NEGATIVE":
                                $sentiment = "Negativo";
                                break;
                            Case "NEUTRAL":
                                $sentiment = "Neutral";
                                break;
                            Case "MIXED":
                                $sentiment = "Mixto";
                                break;
                        }

                        $query_post = Sentiment_posts::where('post_id', $post->post_id)->first();
                        if(!$query_post){
                            Sentiment_posts::create([
                                'page_id' => $post->page_id,
                                'post_id' => $post->post_id,
                                'sentiment' => $sentiment,
                                'score' => $score]);
                        }
                    }
                }catch (\Exception $e){
                    dd($e);
                }

            }
            else{
                $query_post = Sentiment_posts::where('post_id', $post->post_id)->first();
                if(!$query_post){
                    Sentiment_posts::create([
                        'page_id' => $post->page_id,
                        'post_id' => $post->post_id,
                        'sentiment' => 'Neutral',
                        'score' => 0]);
                }
            }
        }
        return 200;
    }
}
