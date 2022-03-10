<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $table='tweets';
    protected $fillable = [
        'id_tweet',
        'author_id',
        'name',
        'content',
        'expanded_url',
        'link',
        'created_time',
    ];

    public function attachment(){
        return $this->hasOne(TweetAttachmet::class,'id_tweet','id_tweet');
    }
    public function page(){
        return $this->hasOne(TwitterScrap::class,'page_id','author_id');
    }
    public function comments(){
        return $this->hasMany(TweetComment::class,'id_tweet','id_tweet');
    }
    public function reactions(){
        return $this->hasOne(TweetReaction::class,'id_tweet','id_tweet');
    }
    public function classification(){
        return $this->hasOne(TwitterClassification::class,'id_tweet','id_tweet');
    }
}
