<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Top;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;
use SebastianBergmann\CodeCoverage\TestFixture\C;

class TopicCount extends Command
{
    use TopicCountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Topic:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temas mas sonados en redes sociales';

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
        $posts = Post::join('attachments', 'attachments.post_id', 'posts.post_id')
            ->where('posts.created_at','>=', Carbon::now()->subHours(24))
            ->orderBy('posts.id','DESC')
            ->get();
        $topic=$this->TopicCount($posts);
        $interaction= json_encode($topic);

        $top = Top::where('type', 'topic')->where('created_at', Carbon::today()->format("Y-m-d"))->first();
        if(!$top){
            $top = Top::create([
                'type' => 'topic',
                'interaction' => $interaction,
                'tipo' => 'topic',
                'created_at'=> Carbon::today()->format("Y-m-d")
            ]);
        }
        else{
            $top->update([
                'type' => 'topic',
                'interaction' => $interaction,
                'tipo' => 'topic',
            ]);
        }
    }
}
