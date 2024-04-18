<?php

namespace App\Libraries\Xtract;

use App\Jobs\DownloadMedia;
use App\Models\TweetMedia;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Translation\t;

class Media
{
    public string $id;
    public string $media_key;
    public MediaType $type;
    public int $height;
    public int $width;

    public ?string $alt_text = null;

    public string $image_url;
    public ?string $media_url = null;
    public ?string $mime = null;
    public ?int $duration = null;

    public Tweet $tweet;

    public function __construct(array &$data, Tweet $tweet)
    {
        $this->id = $data['id_str'];
        $this->media_key = $data['media_key'];

        $this->tweet = $tweet;

        if (array_key_exists('ext_alt_text', $data)) $this->alt_text = $data['ext_alt_text'];

        $this->type = MediaType::from($data['type']);

        $this->height = $data['sizes']['large']['h'];
        $this->width = $data['sizes']['large']['w'];

        $this->image_url = $data['media_url_https'];
        //$path = 'images'.parse_url($this->image_url, PHP_URL_PATH);
        //if (!Storage::exists($path)) Storage::put($path, file_get_contents($this->image_url), 'public');
        //$this->image_url = $path;

        if ($this->type === MediaType::Video || $this->type === MediaType::Animated) {
            $sources = [];

            foreach ($data['video_info']['variants'] as $v) if (array_key_exists('bitrate', $v)) $sources[$v['bitrate']] = $v['url'];

            // Since the array of sources is keyed by bitrate we can sort it to get the best quality
            // Oh maybe we can instead find the largest in the array? w/e
            ksort($sources, SORT_NUMERIC);

            $this->media_url = $sources[array_key_last($sources)];
            //$path = 'images'.parse_url($this->media_url, PHP_URL_PATH);
            //if (!Storage::exists($path)) Storage::put($path, file_get_contents($this->media_url), 'public');
            //$this->media_url = $path;

            $this->mime = 'video/mp4';
            if ($this->type === MediaType::Video) $this->duration = $data['video_info']['duration_millis'];
        }
    }

    public function save(array &$cache): TweetMedia|array
    {
        if (array_key_exists($this->id, $cache['media'])) {
            return $cache['media'][$this->id];
        } else {
            $media = new TweetMedia();
            $media->id = $this->id;
            $media->tweet_id = $this->tweet->id;
            $media->media_key = $this->media_key;
            $media->type = $this->type;
            $media->mime = $this->mime;
            $media->height = $this->height;
            $media->width = $this->width;
            $media->alt_text = $this->alt_text;
            $media->duration = $this->duration;
            $media->media_url = $this->media_url;
            $media->image_url = $this->image_url;
            $media->created_at = $this->tweet->created_at;
            $media->save();

            // put into cache so it doesnt double save
            $cache['media'][$media->id] = $media;

            return $media;
        }
    }
}
