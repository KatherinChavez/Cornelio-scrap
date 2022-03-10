<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Scraps extends Model
{
    protected $table='scraps';
    protected $fillable=[
        'page_id',
        'page_name',
        'post_id',
        'competence',
        'user_id',
        'picture',
        'categoria_id',
        'created_time',
        'company_id',
    ];
    public function categories(){
        return $this->hasOne(Category::class,'id','categoria_id');
    }
    public function post(){
        return $this->hasOne(Post::class,'id','post_id');
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function attachment(){
        return $this->hasOne(Attachment::class,'post_id','post_id');
    }
    public function classification_category(){
        return $this->hasOne(Classification_Category::class,'post_id','post_id');
    }
    public function comments(){
        return $this->hasMany(Comment::class,'post_id','post_id');
    }
    public function reactions(){
        return $this->hasOne(Reaction::class,'post_id','post_id');
    }

}
