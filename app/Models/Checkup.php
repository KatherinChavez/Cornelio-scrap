<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkup extends Model
{
    protected $table='checkups';
    protected $fillable=[
        'page_id',
        'page_name',
        'id_appPost',
        'id_appReaction',
        'statusPost',
        'statusReaction',
        'updated_Reaction',
        'updated_Post'
    ];

    public function cron(){
        return $this->hasOne(Subcategory::class,'page_id','page_id');
    }
}
