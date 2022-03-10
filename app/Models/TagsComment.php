<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagsComment extends Model
{
    protected $table='tagscomments';
    protected $fillable=[
        'name',
        'type',
        'user_id'
    ];
}
