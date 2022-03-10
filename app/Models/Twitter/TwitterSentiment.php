<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TwitterSentiment extends Model
{
    protected $table='twitter_sentiments';
    protected $fillable = [
        'comment_id' ,
        'sentiment',
        'status' ,
    ];
}
