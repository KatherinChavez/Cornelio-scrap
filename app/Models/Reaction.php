<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    //protected $table='reactions';
    /*protected $fillable = [
        'id',
        'post_id',
        'reacciones',
        'page_id'
     ];*/
     protected $table='reaction_classifications';
     protected $fillable = ['post_id','page_id','likes','love','haha','sad','wow','angry','shared','updated_at'];

    public function scrap(){
        return $this->hasOne(Scraps::class,'page_id','page_id');
    }

    public function post(){
        return $this->hasOne(Post::class,'post_id','post_id');
    }
}
