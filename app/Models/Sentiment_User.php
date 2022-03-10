<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentiment_User extends Model
{
    protected $table='sentiments_user';
    protected $fillable=[
        'sentiment',
        'sentiment_detail',
        'page_id',
        'user_id'  
    ];
}
