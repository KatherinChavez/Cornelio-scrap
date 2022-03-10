<?php


namespace App\Traits;

use App\Models\Twitter\Tweet;
use App\Models\Twitter\TweetAttachmet;
use App\Models\Twitter\TweetComment;
use App\Models\Twitter\TweetMention;
use App\Models\Twitter\TweetReaction;
use App\Models\Twitter\Twitter_info;
use App\Models\Twitter\TwitterApp;
use Carbon\Carbon;


trait ScrapTweetTrait
{
    public function InformationPageTweet($page_id){
        $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
        $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
        $curl = curl_init();

        try{
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.twitter.com/1.1/users/show.json?user_id=$page_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array("Authorization: Bearer $bearer",),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response_deco = json_decode($response);

            $query = Twitter_info::where('id_page', $response_deco->id_str)->first();
            if(!$query){
                Twitter_info::create([
                    'id_page'          => $response_deco->id_str,
                    'name_page'        => $response_deco->name,
                    'user_name'        => $response_deco->screen_name,
                    'location'         => ($response_deco->location ? $response_deco->location : ''),
                    'profile_location' => ($response_deco->profile_location ? $response_deco->profile_location : ''),
                    'description'      => ($response_deco->description ? $response_deco->description : ''),
                    'url_page'         => ($response_deco->url ? $response_deco->url : ''),
                    'followers_count'  => ($response_deco->followers_count ? $response_deco->followers_count : 0),
                    'friends_count'    => ($response_deco->friends_count ? $response_deco->friends_count : 0),
                    'listed_count'     => ($response_deco->listed_count ? $response_deco->listed_count : 0),
                    'favourites_count' => ($response_deco->favourites_count ? $response_deco->favourites_count : 0),
                    'statuses_count'   => ($response_deco->statuses_count ? $response_deco->statuses_count : 0),
                    'picture'          => $response_deco->profile_image_url_https,
                    'created_time'     => Carbon::parse($response_deco->created_at),
                ]);
            }else{
                Twitter_info::where('id_page', $response_deco->id_str)->update([
                    'name_page'        => $response_deco->name,
                    'user_name'        => $response_deco->screen_name,
                    'location'         => ($response_deco->location ? $response_deco->location : ''),
                    'profile_location' => ($response_deco->profile_location ? $response_deco->profile_location : ''),
                    'description'      => ($response_deco->description ? $response_deco->description : ''),
                    'url_page'         => ($response_deco->url ? $response_deco->url : ''),
                    'followers_count'  => ($response_deco->followers_count ? $response_deco->followers_count : 0),
                    'friends_count'    => ($response_deco->friends_count ? $response_deco->friends_count : 0),
                    'listed_count'     => ($response_deco->listed_count ? $response_deco->listed_count : 0),
                    'favourites_count' => ($response_deco->favourites_count ? $response_deco->favourites_count : 0),
                    'statuses_count'   => ($response_deco->statuses_count ? $response_deco->statuses_count : 0),
                    'picture'          => $response_deco->profile_image_url_https,
                    'created_time'     => Carbon::parse($response_deco->created_at),
                ]);
            }
            return 200;
        }catch (\Exception $e){
            return 500;
        }


    }

    public function get_username($username){
        try{
            $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
            $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.twitter.com/2/users/by/username/$username",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $bearer",
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }catch (\Exception $e){
            return 500;
        }

    }

    public function get_Tweets($page, $limit){
        $limit = ($limit != null) ? $limit : 20;
        $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
        $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                //CURLOPT_URL => "https://api.twitter.com/2/users/$page->id/tweets?tweet.fields=created_at&expansions=author_id&user.fields=created_at&max_results=20",
                CURLOPT_URL => "https://api.twitter.com/2/users/$page->id/tweets?tweet.fields=created_at,lang,entities&expansions=author_id,attachments.media_keys&user.fields=created_at&media.fields=preview_image_url,url&max_results=$limit",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $bearer",
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response_deco = json_decode($response);
            foreach($response_deco->data as $result){
                $query = Tweet::where('id_tweet',$result->id)->first();
                if(!$query){
                    Tweet::create([
                        'id_tweet'     => $result->id,
                        'author_id'    => $result->author_id,
                        'name'         => $page->name,
                        'content'      => $result->text,
                        'expanded_url' => (isset($result->entities->urls[0]->expanded_url) ? $result->entities->urls[0]->expanded_url : ''),
                        'link'         => "twitter.com/$page->username/status/$result->id",
                        'created_time' => Carbon::parse($result->created_at),
                    ]);
                }
                if(isset($result->attachments->media_keys)){
                    foreach ($result->attachments->media_keys as $media){
                        $attachment = TweetAttachmet::where('media_key', $media)->first();
                        if(!$attachment){
                            TweetAttachmet::create([
                                'id_page'   => $result->author_id,
                                'id_tweet'  => $result->id,
                                'media_key' => $media,
                            ]);
                        }
                    }
                }
            }
            if(isset($response_deco->includes->media)){
                foreach ($response_deco->includes->media as $value){
                    $queryAttachment = TweetAttachmet::where('media_key', $value->media_key)->first();
                    if($queryAttachment){
                        TweetAttachmet::where('media_key', $value->media_key)->update([
                            'picture' => (isset($value->url)? $value->url : $value->preview_image_url),
                            'type'    => $value->type,
                        ]);
                    }
                }
            }
            return 200;
        }catch (\Exception $e){
            return 500;
        }
    }

    public function get_reaction($tweet){
        try{
            $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
            $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.twitter.com/1.1/statuses/show.json?id=$tweet",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $bearer",
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response_deco = json_decode($response);
            $query = TweetReaction::where('id_tweet', $tweet)->first();
            if(!$query){
                TweetReaction::create([
                    'id_page'        => $response_deco->user->id,
                    'id_tweet'       => $response_deco->id_str,
                    'retweet_count'  => $response_deco->retweet_count,
                    'favorite_count' => $response_deco->favorite_count,
                ]);
            }
            else{
                TweetReaction::where('id_tweet', $tweet)->update([
                    'retweet_count'  => $response_deco->retweet_count,
                    'favorite_count' => $response_deco->favorite_count,
                ]);
            }
            return 200;
        }catch (\Exception $e){
            return 500;
        }
    }

    public function get_Mentions($page){
        $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
        $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.twitter.com/2/users/$page->id/mentions?tweet.fields=created_at&expansions=author_id,referenced_tweets.id&user.fields=created_at&max_results=20",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $bearer",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response_deco = json_decode($response);

        if(isset($response_deco->data)) {
            foreach ($response_deco->data as $data) {
                $query_d = TweetMention::where('id_mention', $data->id)->first();
                if (!$query_d) {
                    TweetMention::create([
                        'page_id' => $page->id,
                        'referenced_tweets' => (isset($data->referenced_tweets[0]->id) ? $data->referenced_tweets[0]->id : ''),
                        'id_mention' => $data->id,
                        'author_id' => $data->author_id,
                        'text' => $data->text,
                        'created_time' => $data->created_at,
                    ]);
                }
            }
        }

        if(isset($response_deco->includes->tweets)){
            foreach($response_deco->includes->tweets as $tweet){
                $query_t = TweetMention::where('id_mention', $tweet->id)->first();
                if(!$query_t && $tweet->author_id != $page->id){
                    TweetMention::create([
                        'page_id'           => $page->id,
                        'referenced_tweets' => (isset($tweet->referenced_tweets[0]->id)? $tweet->referenced_tweets[0]->id : ''),
                        'id_mention'        => $tweet->id,
                        'author_id'         => $tweet->author_id,
                        'text'              => $tweet->text,
                        'created_time'      => $tweet->created_at,
                    ]);
                }
            }
        }

        if(isset($response_deco->includes->users)){
            foreach($response_deco->includes->users as $user){
                TweetMention::where('author_id', $user->id)->update([
                    'username' => $user->username,
                    'name'     => $user->name,
                ]);
            }
        }
    }

    public function get_Comments($tweet, $limit){
        // No se obtiene los cometarios despues de 7 siete dias
        $limit = ($limit != null) ? $limit : 20;
        try{
            $app = TwitterApp::inRandomOrder()->get()->pluck('bearer_token')->first();
            $bearer = ($app != null) ? base64_decode($app): env('BEARER_TOKEN');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.twitter.com/2/tweets/search/recent?query=conversation_id:$tweet->id_tweet&expansions=author_id&tweet.fields=created_at&max_results=$limit",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $bearer",
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response_deco = json_decode($response);

            if(isset($response_deco->data)){
                foreach ($response_deco->data as $comment){
                    $query = TweetComment::where('comment_id', $comment->id)->first();
                    if(!$query){
                        TweetComment::create([
                            'id_page'      => $tweet->author_id,
                            'id_tweet'     => $tweet->id_tweet,
                            'comment_id'   => $comment->id,
                            'user_id'      => $comment->author_id,
                            'content'      => $comment->text,
                            'created_time' => $comment->created_at,
                        ]);
                    }
                }

                foreach ($response_deco->includes->users as $user) {
                    TweetComment::where('user_id', $user->id)->update([
                        'username' => $user->username,
                        'name'     => $user->name,
                    ]);
                }
                return 200;
            }
            return 500;
        }catch (\Exception $e){
            return 500;
        }
    }

}