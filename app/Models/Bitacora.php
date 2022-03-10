<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table='bitacora_api';
    protected $fillable=[
        'message',
        'client_id',
        'instance',
    ];
}