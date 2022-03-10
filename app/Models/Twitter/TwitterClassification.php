<?php

namespace App\Models\Twitter;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Model;

class TwitterClassification extends Model
{
    protected $table='twitter_classifications';
    protected $fillable = [
        'page_id' ,
        'id_tweet' ,
        'subcategoria_id',
        'company_id',
    ];

    public function attachment(){
        return $this->hasOne(TweetAttachmet::class,'id_tweet','id_tweet');
    }
    public function page(){
        return $this->hasOne(TwitterScrap::class,'page_id','page_id');
    }
    public function comments(){
        return $this->hasMany(TweetComment::class,'id_tweet','id_tweet');
    }
    public function reactions(){
        return $this->hasOne(TweetReaction::class,'id_tweet','id_tweet');
    }
    public function tweet(){
        return $this->hasOne(Tweet::class,'id_tweet','id_tweet');
    }
    public function topics(){
        return $this->hasOne(Subcategory::class,'id','subcategoria_id');
    }
}
