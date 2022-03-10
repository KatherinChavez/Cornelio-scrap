<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TwitterNotification extends Model
{
    protected $table='twitter_notification';
    protected $fillable = [
        'id_tweet' ,
        'subcategory_id' ,
        'word',
        'status',
    ];
}
