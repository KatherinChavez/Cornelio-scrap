<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TweetReaction extends Model
{
    protected $table='tweets_reaction';
    protected $fillable = [
        'id_page',
        'id_tweet',
        'retweet_count',
        'favorite_count',
    ];
}
