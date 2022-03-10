<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table='tokens';
    protected $fillable=[
        'page_id',
        'page_name',
        'access_token',
        'user_id'
    ];
}
