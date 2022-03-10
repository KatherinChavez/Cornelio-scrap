<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable=[
        'nombre',
        'descripcion',
        'slug',
        'page',
        'status',
        'user_id',
        'created_by',
        'phone',
        'phoneOptional',
        'emailCompanies',
        'channel',
        'key',
        'client_id',
        'instance',
        'group_id'
    ];
    public function users(){
        return $this->belongsToMany(User::class,'company_user','company_id','user_id');
    }
}