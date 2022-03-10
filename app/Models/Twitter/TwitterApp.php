<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TwitterApp extends Model
{
    protected $table='apps_twitter';
    protected $fillable = [
        'name_app' ,
        'consumer_key' ,
        'consumer_secret',
        'token_twitter',
        'token_secret_twitter',
        'bearer_token',
        'number_one',
        'number',
    ];
}
