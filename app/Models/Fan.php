<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fan extends Model
{
    protected $table='fans';
    protected $fillable = ['id',
        'page_id',
        'page_name',
        'fan_count', 
        'category', 
        'about', 
        'company_overview',
        'city', 
        'country', 
        'latitude', 
        'longitude', 
        'street', 
        'zip',
        'phone', 
        'emails', 
        'talking', 
        'created_time'    
    ];
}
