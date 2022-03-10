<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentiment extends Model
{
    protected $table='sentiments';
    protected $fillable = ['comment_id','user_id','sentiment','estado', 'score'];
}
