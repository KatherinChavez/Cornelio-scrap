<?php

namespace App\Models\Twitter;

use Illuminate\Database\Eloquent\Model;

class Twitter_info extends Model
{
    protected $table='twitter_info_pages';
    protected $fillable = [
        'id_page' ,
        'name_page' ,
        'user_name',
        'location',
        'profile_location',
        'description',
        'url_page',
        'followers_count',
        'friends_count',
        'listed_count',
        'favourites_count',
        'statuses_count',
        'picture',
        'created_at',
    ];
}
