<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiWhatsapp extends Model
{
    protected $table='api_whatsapp';
    protected $fillable=[
        'client_id',
        'instance',
        'key',
    ];
}
