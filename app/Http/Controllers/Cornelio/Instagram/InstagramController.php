<?php

namespace App\Http\Controllers\Cornelio\Instagram;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InstagramScraper\Instagram;
use Phpfastcache\Helper\Psr16Adapter;

class InstagramController extends Controller
{
    public function index()
    {

        $instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
        $medias = $instagram->getMedias('kevin', 25);

// Let's look at $media
        $media = $medias[0];

        echo "Media info:\n";
        echo "Id: {$media->getId()}\n";
        echo "Shortcode: {$media->getShortCode()}\n";
        echo "Created at: {$media->getCreatedTime()}\n";
        echo "Caption: {$media->getCaption()}\n";
        echo "Number of comments: {$media->getCommentsCount()}";
        echo "Number of likes: {$media->getLikesCount()}";
        echo "Get link: {$media->getLink()}";
        echo "High resolution image: {$media->getImageHighResolutionUrl()}";
        echo "Media type (video or image): {$media->getType()}";
        $account = $media->getOwner();
        echo "Account info:\n";
        echo "Id: {$account->getId()}\n";
        echo "Username: {$account->getUsername()}\n";
        echo "Full name: {$account->getFullName()}\n";
        echo "Profile pic url: {$account->getProfilePicUrl()}\n";


// If account private you should be subscribed and after auth it will be available
        $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'Tati_H2198', 'Hos86258376.', new Psr16Adapter('Files'));
        $instagram->login();
        $medias = $instagram->getMedias('private_account', 100);


//        $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'Tati_H2198', 'Hos86258376.', new Psr16Adapter('Files'));
//        $instagram->login();
//        sleep(2); // Delay to mimic user
//
//        $username = 'ameliarueda';
//        $followers = [];
//        $account = $instagram->getAccount($username);
//        sleep(1);
//        $followers = $instagram->getFollowers($account->getId(), 1000, 100, true); // Get 1000 followers of 'kevin', 100 a time with random delay between requests
//        echo '<pre >' . json_encode($followers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';

    }


}
