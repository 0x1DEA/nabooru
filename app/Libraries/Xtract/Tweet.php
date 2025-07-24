<?php

namespace App\Libraries\Xtract;

use App\Models\TwitterUser;

class Tweet {
    public string $id;
    public ?string $conversationId = null;

    public string $text;

    public User $author;

    public int $likes_count;
    public int $retweets_count;
    public int $replies_count;
    public int $bookmarks_count;
    public ?int $views_count = null;
    public int $quotes_count;

    public string $device;

    public ?Tweet $quote = null;
    public ?string $quote_id = null;
    public string $created_at;
    public array $mentions = [];
    public array $urls = [];
    public array $timestamps = [];
    /** @var Media[] */
    public array $media = [];
    public array $location;

    public function __construct(array &$data, ?array $head = ['wrong source call']) {
        if (!array_key_exists('__typename', $data)) dd([$data, $head]);

        if ($data['__typename'] === 'TweetUnavailable') throw new \Error($data['reason']);

        if ($data['__typename'] === 'TweetWithVisibilityResults') {
            $ts = microtime(true);
            $data = $data['tweet'];
            \Log::debug('- - ##### data copy ' . (microtime(true) - $ts) * 1_000_000 . 'us');
        } else {
            if ($data['__typename'] !== 'Tweet') throw new \Error('Unrecognized type: ' . $data['__typename']);
        }

        $ts = microtime(true);
        $this->id = $data['rest_id'];
        $this->author = new User($data['core']['user_results']['result']);
        $this->views_count = $data['views']['count'] ?? null;
        $this->device = $data['source'];
        \Log::debug('- - ##### Block 1 copy ' . (microtime(true) - $ts) * 1_000 . 'ms');

        // Does not do nested qrts
        $ts = microtime(true);
        if ($data['legacy']['is_quote_status'] && array_key_exists('quoted_status_result', $data)) {
            $this->quote_id = $data['legacy']['quoted_status_id_str'];
            if (!empty($data['quoted_status_result']['result'])) {
                if ($data['quoted_status_result']['result']['__typename'] === 'Tweet') $this->quote = new Tweet($data['quoted_status_result']['result']);
            }
        }
        \Log::debug('- - ##### QRT copy ' . (microtime(true) - $ts) * 1_000_000 . 'us');

        $ts = microtime(true);
        $data = $data['legacy'];
        \Log::debug('- - ##### Legacy copy ' . (microtime(true) - $ts) * 1_000_000 . 'us');

        $this->text = $data['full_text'];

        $ts = microtime(true);
        if (array_key_exists('coordinates', $data)) $this->location = $data['coordinates'];
        if (array_key_exists('media', $data['entities']) && count($data['entities']['media']) > 0) {
            foreach ($data['extended_entities']['media'] as $media) {
                $this->media[] = new Media($media, $this);
            }
        }
        \Log::debug('- - ##### Media copy ' . (microtime(true) - $ts) * 1_000_000 . 'us');

        $ts = microtime(true);
        $this->mentions = $data['entities']['user_mentions'];
        $this->urls = $data['entities']['urls'];

        $this->likes_count = $data['favorite_count'];
        $this->replies_count = $data['reply_count'];
        $this->retweets_count = $data['retweet_count'];
        $this->bookmarks_count = $data['bookmark_count'];
        $this->quotes_count = $data['quote_count'];

        $this->conversationId = $data['conversation_id_str'] ?? null;

        $this->created_at = $data['created_at'];
        \Log::debug('- - ##### Block 2 copy ' . (microtime(true) - $ts) * 1_000_000 . 'us');
    }

    public function save(array &$cache): \App\Models\Tweet|array
    {
        if (array_key_exists($this->id, $cache['tweets'])) {
            // we already saved this tweet
            return $cache['tweets'][$this->id];
        } else {
            if (!array_key_exists($this->author->id, $cache['users'])) {
                $author = TwitterUser::query()->updateOrCreate([
                    'id' => $this->author->id
                ], [
                    'name' => $this->author->name,
                    'username' => $this->author->username,

                    'avatar_url' => $this->author->avatar_url,
                    'banner_url' => $this->author->banner_url,

                    'bio' => $this->author->bio,
                    'location' => $this->author->location,
                    'url' => $this->author->url,

                    'created_at' => $this->created_at,

                    'likes_count' => $this->author->likes_count,
                    'tweets_count' => $this->author->tweets_count,
                    'media_count' => $this->author->media_count,
                    'followers_count' => $this->author->followers_count,
                    'following_count' => $this->author->following_count,
                    'listed_count' => $this->author->listed_count,

                    'downloaded' => false
                ]);

                // FIXME: authors have had post created at instead of account!!
                //$author->created_at = $this->created_at;

                // add to the cache so that we dont double add
                $cache['users'][$this->author->id] = $author;
            }

            $tweet = \App\Models\Tweet::query()->updateOrCreate([
                'id' => $this->id,
            ], [
                'author_id' => $this->author->id,
                'conversation_id' => $this->conversationId,
                'device' => $this->device,
                'created_at' => $this->created_at,
                'likes_count' => $this->likes_count,
                'replies_count' => $this->replies_count,
                'retweets_count' => $this->retweets_count,
                'quotes_count' => $this->quotes_count,
                'views_count' => $this->views_count,
                'bookmarks_count' => $this->bookmarks_count,
                'quote_id' => $this->quote !== null ? $this->quote_id : null,
                'mentions' => json_encode($this->mentions),
                'text' => $this->text,
            ]);

            if ($this->quote instanceof Tweet) $this->quote->save($cache);

            // add to the cache so that we dont double add
            $cache['tweets'][$this->id] = $tweet;

            foreach ($this->media as $media) $media->save($cache);

            return $tweet;
        }
    }
}
