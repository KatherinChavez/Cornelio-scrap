<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkData extends Model
{
    protected $table='network_data';
    protected $fillable=[
        'company_id',
        'company',
        'topic',
        'data',
    ];
}
