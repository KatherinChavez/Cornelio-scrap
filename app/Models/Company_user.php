<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Company_user extends Pivot
{
    protected $table='company_user';
    protected $fillable=[
        'user_id',
        'company_id',
    ];
}
