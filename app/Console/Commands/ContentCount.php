<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Top;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;

class ContentCount extends Command
{
    use TopicCountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Content:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temas de los contenidos mas sonados en redes sociales';

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
     * @return int
     */
    public function handle()
    {
        $content = Category::get();
        foreach ($content as $tops){
            $posts = Post::where('scraps.categoria_id', $tops->id)
                ->where('posts.created_at','>=', Carbon::now()->subHours(24))
                ->join('scraps', 'scraps.page_name', 'posts.page_name')
                ->join('attachments', 'attachments.post_id', 'posts.post_id')
                ->orderBy('posts.id','DESC')
                ->get();
            $topic=$this->TopicCount($posts);
            $interaction= json_encode($topic);
            if($interaction != '[]'){
                # Se guarda los temas mas hablado de los contenido
                $top = Top::where('type', $tops->id)->where('created_at', Carbon::today()->format("Y-m-d"))->first();
                if(!$top){
                    $top = Top::create([
                        'type' => $tops->id,
                        'interaction' => $interaction,
                        'company_id' =>$tops->company_id,
                        'created_at'=> Carbon::today()->format("Y-m-d")
                    ]);
                }
                else{
                    $top->update([
                        'type' => $tops->id,
                        'interaction' => $interaction,
                        'company_id' =>$tops->company_id,
                    ]);
                }
            }
            else{
                continue;
            }
        }
    }
}
