<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 13/7/2021
 * Time: 12:56
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopReaction extends Model {
    protected $table='top_reaction';
    protected $fillable = [
        'position',
        'post_id',
        'page_id',
        'page_name',
        'company',
        'content',
        'classification',
        'likes',
        'love',
        'haha',
        'sad',
        'wow',
        'angry',
        'shared',
        'count',
        'date',
        'fileName',
    ];

    public function scrap(){
        return $this->hasOne(Scraps::class,'page_id','page_id');
    }
    public function attachment(){
        return $this->hasOne(Attachment::class,'post_id','post_id');
    }
}