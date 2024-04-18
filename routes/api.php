<?php

use App\Libraries\Xtract\Tweet;
use App\Models\TweetMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/scrape-bookmarks', function (Request $request) {
    Log::debug('-----~=[ starting request ]=~-----');

    // Save the server response raw if we ever wanna do more with it retroactively
    Storage::put('server/' . time() . '.json', json_encode(json_decode($request->getContent()), JSON_PRETTY_PRINT));
    Log::debug('Stored server response');

    // I wonder if there's a better way of "querying" data in json
    $data = $request->json('data.bookmark_timeline_v2.timeline.instructions.0.entries');
    // If we fucked up then fuck it
    if (!$data) dd($request);

    Log::debug('Traversed JSON');

    // get this ahead of time to reduce queries IDs only
    $ids = [
        'tweets' => [],
        'users' => [],
        'media' => [],
    ];

    $tweets = [];

    foreach ($data as &$entry) {
        if ($entry['content']['__typename'] === 'TimelineTimelineItem') {
            Log::debug('- Parsing tweet');
            $ts = microtime(true);
            $t = new Tweet($entry['content']['itemContent']['tweet_results']['result']);
            Log::debug('- - instantiated tweet obj');

            $ids['tweets'][] = $t->id;
            $ids['users'][] = $t->author->id;
            foreach ($t->media as $m) $ids['media'][] = $m->id;

            if ($t->quote) {
                $ids['tweets'][] = $t->quote->id;
                $ids['users'][] = $t->quote->author->id;
                foreach ($t->quote->media as $m) $ids['media'][] = $m->id;
            }

            $tweets[] = $t;


            Log::debug('- - Parsed tweet in ### ' . microtime(true) - $ts . 's');
        }
    }

    Log::debug('Parsed tweets');

    $saved = [
        'users' => \App\Models\TwitterUser::query()->whereIn('id', $ids['users'])->get()->keyBy('id')->toArray(),
        'tweets' => \App\Models\Tweet::query()->whereIn('id', $ids['tweets'])->get()->keyBy('id')->toArray(),
        'media' => \App\Models\TweetMedia::query()->whereIn('id', $ids['media'])->get()->keyBy('id')->toArray(),
    ];

    Log::debug('Fetched cache');

    foreach ($tweets as &$t) {
        $t->save($saved);
        Log::debug('Saved tweet ' . $t->id);
    }

    Log::debug('------~=[ ending request ]=~------');

    return 'ok';
});

Route::get('/dl_old', function () {
    $media = TweetMedia::query()->where('downloaded', false)->get();
    $users = \App\Models\TwitterUser::query()->where('downloaded', false)->get();

    try {
        $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use (&$media, &$users) {
            // Add each image url and, if applicable, media url to the download queue
            $media->map(function (TweetMedia $m) use (&$pool) {
                // Don't add to download queue if we already have it
                $path = 'ext' . parse_url($m->image_url, PHP_URL_PATH);
                if ($m->image_url && !Storage::exists($path)) $pool->as($m->id . '_image')->get($m->image_url);
                $path = 'ext' . parse_url($m->media_url, PHP_URL_PATH);
                if ($m->media_url && !Storage::exists($path)) $pool->as($m->id . '_media')->get($m->media_url);
            });

            $users->map(function (\App\Models\TwitterUser $u) use (&$pool) {
                $path = 'banners' . parse_url($u->banner_url, PHP_URL_PATH);
                if ($u->banner_url && !Storage::exists($path)) $pool->as('banner_' . $u->id)->get($u->banner_url);

                if ($u->avatar_url) {
                    $path = 'avatars' . parse_url($u->avatar_url, PHP_URL_PATH);
                    if (!Storage::exists($path)) $pool->as('avatar_' . $u->id . '_x96')
                        ->get(str_replace('normal', 'x96', $u->avatar_url));
                    if (!Storage::exists($path)) $pool->as('avatar_' . $u->id . '_400x400')
                        ->get(str_replace('normal', '400x400', $u->avatar_url));
                }
            });
        });
    } catch (Exception $e) {
        dd($e);
    }

    //dd($responses, $media, $users);

    $media->map(function (TweetMedia $m) use (&$responses) {
        $path = 'ext' . parse_url($m->image_url, PHP_URL_PATH);
        Storage::put($path, $responses[$m->id . '_image']->body(), 'public');
        $m->image_url = $path;

        if ($m->media_url) {
            $path = 'ext' . parse_url($m->media_url, PHP_URL_PATH);
            Storage::put($path, $responses[$m->id . '_media']->body(), 'public');
            $m->media_url = $path;
        }

        $m->downloaded = true;
        $m->save();
    });

    $users->map(function (\App\Models\TwitterUser $u) use (&$responses) {
        if ($u->banner_url) {
            $path = 'banners' . parse_url($u->banner_url, PHP_URL_PATH);
            if (!Storage::exists($path)) Storage::put($path, $responses['banner_' . $u->id]->body(), 'public');
            $this->banner_url = $path;
        }

        if ($u->avatar_url) {
            $path = 'avatars' . parse_url(str_replace('normal', 'x96', $u->avatar_url), PHP_URL_PATH);
            if (!Storage::exists($path)) Storage::put($path, $responses['avatar_' . $u->id . '_x96']->body(), 'public');
            $path = str_replace('x96', '400x400', $path);
            if (!Storage::exists($path)) Storage::put($path, $responses['avatar_' . $u->id . '_400x400']->body(), 'public');

            $this->avatar_url = 'avatars' . parse_url($u->avatar_url, PHP_URL_PATH);
        }

        $u->downloaded = true;
        $u->save();
    });

    return 'ok';
});
