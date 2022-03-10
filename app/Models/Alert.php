<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table='alert';
    protected $fillable=[
        'subcategory_id',
        'notification',
        'report',
    ];

    public function category(){
        return $this->hasOne(Subcategory::class,'id','subcategory_id');
    }
}
