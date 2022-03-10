<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberWhatsapp extends Model
{
    protected $table='numeros_whatsapp';
    protected $fillable=[
        'id',
        'numeroTelefono',
        'group_id',
        'descripcion',
        'subcategory_id',
        'content',
        'report',
        'company_id'
    ];
    public function subcategory(){
        return $this->hasOne(Subcategory::class,'id','subcategory_id');
    }
}
