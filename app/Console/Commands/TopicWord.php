<?php

namespace App\Console\Commands;

use App\Models\Classification_Category;
use App\Models\Post;
use App\Models\Top;
use App\Traits\TopicCountTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;
use SebastianBergmann\CodeCoverage\TestFixture\C;

class TopicWord extends Command
{
    use TopicCountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Topic:word';

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

        $comments = Classification_Category::where('subcategoria_id', 20)
            ->join('comments', 'comments.post_id', 'classification_category.post_id')
            //->take(3000)
            ->get();
        //dd($comments);
        $topic=$this->TopicWord($comments);
        $interaction= json_encode($topic);

        dd($topic);
    }
}