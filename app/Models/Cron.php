<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Cron extends Model
{
    protected $table='cron_pages';
    protected $fillable=[
        'company_id',
        'page_id',
        'page_name',
        'timePost',
        'timeReaction',
        'id_appPost',
        'id_appReaction',
        'limit_time',
        'limit',
    ];

    public function posts(){
        return $this->hasOne(Post::class,'page_id','page_id');
    }
    public function page(){
        return $this->belongsTo(Page::class,'page_id','page_id');
    }
}
