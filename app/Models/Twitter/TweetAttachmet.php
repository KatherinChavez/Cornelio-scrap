<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TweetAttachmet extends Model
{
    protected $table='tweets_attachment';
    protected $fillable = [
        'id_page',
        'id_tweet',
        'media_key',
        'picture',
        'type',
    ];
}
