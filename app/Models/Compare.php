<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Compare extends Model
{
    /*
    1 ALTA
    2 MEDIA
    3 BAJA
    */
    protected $table = 'compare';

    protected $fillable = [
        'id',
        'palabra',
        'detalle',
        'prioridad',
        'subcategoria_id',
        'user_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
    }

    public function subcategory()
    {
        return $this->hasOne(Subcategory::class, 'id', 'subcategoria_id');
    }
}
