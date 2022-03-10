<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class TwitterContent extends Model
{
    protected $table='twitter_contents';
    protected $fillable = [
        'name' ,
        'description' ,
        'company_id',
    ];
}
