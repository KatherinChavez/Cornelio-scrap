<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentiment_posts extends Model
{
    protected $table='sentiments_posts';
    protected $fillable=[
        'page_id',
        'post_id',
        'sentiment',
        'score',
    ];
}
