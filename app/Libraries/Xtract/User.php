<?php

namespace App\Libraries\Xtract;

use Illuminate\Support\Facades\Storage;

class User {
    public string $id;

    public string $name;
    public string $username;
    public ?string $bio = null;
    public ?string $location = null;
    public ?string $url = null;

    public ?string $banner_url = null;
    public ?string $avatar_url = null;
    public ?string $created_at = null;


    public int $likes_count;
    public int $tweets_count;
    public int $media_count;
    public int $followers_count;
    public int $following_count;
    public int $listed_count; // How many lists this person is in

    public function __construct(array &$data)
    {
        $this->id = $data['rest_id'];

        $d = $data['legacy'];

        $this->name = $data['core']['name'];
        $this->username = $data['core']['screen_name'];
        if (array_key_exists('description', $d)) $this->bio = $d['description'];
        if (array_key_exists('location', $d)) $this->location = $d['location'];

        $this->created_at = $data['core']['created_at'];

        $this->likes_count = $d['favourites_count'];
        $this->tweets_count = $d['statuses_count'];
        $this->media_count = $d['media_count'];
        $this->followers_count = $d['followers_count'];
        $this->following_count = $d['friends_count'];
        $this->listed_count = $d['listed_count'];

        if (array_key_exists('profile_banner_url', $d)) $this->banner_url = $d['profile_banner_url'];
        if (array_key_exists('profile_image_url_https', $d)) $this->avatar_url = $data['avatar']['image_url'];

        // Set true profile link
        if (array_key_exists('url', $d['entities'])) {
            $links = $d['entities']['url']['urls'];
            if (count($links) > 0) $this->url = str_replace($links[0]['url'], $links[0]['expanded_url'], $d['url']);
        }

        // TODO: Set true description links
//        $bioLinks = $data['entities']['description']['urls'];
//        if (count($bioLinks) > 0) {
//            for ($i = 0; $i < count($bioLinks); $i++) {
//                $this->bio = str_replace($links[$i]['url'], $links[$i]['expanded_url'], $data['url']);
//            }
//        }
    }
}
