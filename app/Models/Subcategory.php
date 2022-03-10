<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table='subcategory';
    protected $fillable=[
        'id',
        'name',
        'detail',
        'category_id',
        'nameTelegram',
        'channel',
        'status',
        'company_id',
    ];

    public function numberWhat(){
        return $this->hasOne(NumberWhatsapp::class,'subcategory_id','id');
    }

    public function word(){
        return $this->hasOne(Compare::class,'subcategoria_id','id');
    }
}
