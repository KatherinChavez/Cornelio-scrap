<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Megacategory extends Model
{
    protected $table='megacategory';
    protected $fillable=[
        'name',
        'description',
        'user_id',
        'company_id',
    ];
}
