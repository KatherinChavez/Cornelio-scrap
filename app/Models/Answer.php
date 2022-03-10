<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table='answers';
    protected $fillable=[
        'company_id',
        'respuesta',
        'user_id',
    ];

    public function companies(){
        return $this->hasOne(Company::class,'id','company_id');
    }
}
