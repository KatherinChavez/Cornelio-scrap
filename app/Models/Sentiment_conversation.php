<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentiment_conversation extends Model
{
    protected $table='sentiments_conversations';
    protected $fillable=[
        'conv_id',
        'msg_id',
        'sentiment',
        'estado',
        'company_id'
    ];
}
