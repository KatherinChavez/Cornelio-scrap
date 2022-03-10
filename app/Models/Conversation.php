<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table='conversations';
    protected $fillable=[
        'conv_id',
        'msg_id',
        'page_id',
        'page_name',
        'author',
        'author_id',
        'interaction',
        'message',
        'sentiment',
        'created_time',
        'status'
    ];
}
