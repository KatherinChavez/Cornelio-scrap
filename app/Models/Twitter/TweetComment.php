<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TweetComment extends Model
{
    protected $table='tweets_comments';
    protected $fillable = [
        'id_page',
        'id_tweet',
        'comment_id',
        'user_id',
        'username',
        'name',
        'content',
        'created_time',
    ];

    public function post(){
        return $this->hasMany(Tweet::class,'id_tweet','id_tweet');
    }
    public function attachments(){
        return $this->hasOne(TweetAttachmet::class,'id_tweet','id_tweet');
    }
    public function page(){
        return $this->hasOne(TwitterScrap::class,'page_id','id_page');
    }
    public function sentiment(){
        return $this->hasOne(TwitterSentiment::class,'comment_id','comment_id');
    }
}
