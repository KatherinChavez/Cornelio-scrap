<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table='posts';
    protected $fillable=[
        'page_id',
        'page_name',
        'post_id',
        'content',
        'type',
        'status',
        'created_time'
    ];
    public function attachment(){
        return $this->hasOne(Attachment::class,'post_id','post_id');
    }
    public function page(){
        return $this->belongsTo(Page::class,'page_id','page_id');
    }
    public function comments(){
        return $this->hasMany(Comment::class,'post_id','post_id');
    }
    public function reactions(){
        return $this->hasOne(Reaction::class,'post_id','post_id');
    }
    public function classification_category(){
        return $this->hasOne(Classification_Category::class,'post_id','post_id');
    }
}
