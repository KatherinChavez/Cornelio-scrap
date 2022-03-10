<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table='scraps';
    protected $fillable=[
        'page_id',
        'page_name',
        'token',
        'picture',
        'company_id',
        'status',
        'user_id'
    ];
    public function sentiment_User()
    {
        return $this->hasMany(Sentiment_User::class); // Realación de uno a muchos con la tabla area
    }
}
