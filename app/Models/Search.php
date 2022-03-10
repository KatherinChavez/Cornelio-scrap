<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $table='search';
    protected $fillable=[
        'page_id',
        'post_id',
        'page_name',
        'comment',
        'date',
        'user_id'
    ];
}
