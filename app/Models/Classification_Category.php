<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification_Category extends Model
{
    protected $table='classification_category';
    protected $fillable = [
        'post_id',
        'page_id',
        'user_id',
        'company_id',
        'megacategoria_id',
        'subcategoria_id'];

    public function post(){
        return $this->hasOne(Post::class,'post_id','post_id');
    }
    public function megacategory(){
        return $this->hasOne(Category::class,'id','megacategoria_id');
    }
    public function subcategory(){
        return $this->hasOne(Subcategory::class,'id','subcategoria_id');
    }
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
    public function sentimentPost(){
        return $this->hasOne(Sentiment_posts::class,'post_id','post_id');
    }
}
