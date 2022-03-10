<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table='attachments';
    protected $fillable=[
        'post_id',
        'picture',
        'video',
        'haha',
        'type',
        'url',
        'title',
        'page_id',
        'created_time'
    ];
}
