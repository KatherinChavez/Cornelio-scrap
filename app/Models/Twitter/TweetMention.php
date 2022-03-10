<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TweetMention extends Model
{
    protected $table='tweets_mentions';
    protected $fillable = [
        'page_id',
        'referenced_tweets',
        'id_mention',
        'author_id',
        'username',
        'name',
        'text',
        'country_code',
        'full_name',
        'created_time',
    ];

    public function post(){
        return $this->hasMany(Tweet::class,'id_tweet','referenced_tweets');
    }
    public function attachments(){
        return $this->hasOne(TweetAttachmet::class,'id_tweet','referenced_tweets');
    }
    public function page(){
        return $this->hasOne(TwitterScrap::class,'page_id','page_id');
    }
}
