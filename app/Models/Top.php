<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Top extends Model
{
    protected $table='tops';
    protected $fillable=[
        'type',
        'interaction',
        'company_id',
        'created_at',
        'updated_at',
    ];

}
