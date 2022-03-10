<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App_Fb extends Model
{
    protected $table='apps_fb';
    protected $fillable=[
        'name_app',
        'app',
        'app_fb_id',
        'app_fb_secret',
        'app_fb_token',
        'number_one',
        'number',
    ];

}
