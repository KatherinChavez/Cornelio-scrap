<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table='comments';
    protected $fillable=[
        'page_id',
        'post_id',
        'page_id',
        'comment_id',
        'author_id',
        'commented_from',
        'comment',
        'sentiment',
        'created_time',
        'status',
        'user_id',
    ];
    public function post(){
        return $this->hasMany(Post::class,'post_id','post_id');
    }
    public function attachments(){
        return $this->hasOne(Attachment::class,'post_id','post_id');
    }
    public function page(){
        return $this->hasOne(Page::class,'page_id','page_id');
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function sentiment(){
        return $this->hasOne(Sentiment::class,'comment_id','comment_id');
    }
}
