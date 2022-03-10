<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraPDF extends Model
{
    protected $table='bitacora_pdf';
    protected $fillable=[
        'file',
        'url',
        'created_at',
        'updated_at',
    ];
}