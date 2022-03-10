<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Info_page extends Model
{
    protected $table='info_pages';
    protected $fillable = [
        'id',
        'page_id',
        'page_name',
        'fan_count', 
        'category', 
        'about', 
        'company_overview', 
        'location',
        'phone',
        'emails',
        'talking',
        'picture',
        'created_time'
        ];
}
