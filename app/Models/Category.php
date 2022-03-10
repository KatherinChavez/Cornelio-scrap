<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table='category';
    protected $fillable=[
        'name',
        'description',
        'company_id',
        'megacategory_id',
    ];
    public function mega(){
        return $this->hasOne(Megacategory::class,'id','megacategory_id');
    }
    public function subs(){
        return $this->hasMany(Subcategory::class,'subcategory_id','id');
    }
}
