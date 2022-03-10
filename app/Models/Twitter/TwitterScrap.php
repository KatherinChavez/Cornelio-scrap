<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TwitterScrap extends Model
{
    protected $table='twitter_scraps';
    protected $fillable = [
        'page_id' ,
        'username' ,
        'name',
        'user_id',
        'categoria_id',
        'company_id',
        'status',
    ];
}
