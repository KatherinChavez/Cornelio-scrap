<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table=('notification');
    protected $fillable=[
        'post_id',
        'subcategory_id',
        'word',
        'status',
    ];
    public function subcategory(){
        return $this->hasOne(Subcategory::class,'id','subcategory_id');
    }

    public function word(){
        return $this->hasOne(Word::class,'id','word');
    }
}
