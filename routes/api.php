<?php

use App\Libraries\Xtract\Tweet;
use App\Models\TweetMedia;
use App\Models\TwitterUser;
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

Route::post('/scrape-likes', function (Request $request) {
    Log::debug('-----~=[ starting request ]=~-----');

    $data = null;
    if ($request->has('file')) {
        $file = 'server/' . $request->string('') . '.json';
        if (!Storage::exists($file)) return 'bad snapshot';
        $data = json_decode(Storage::get($file), true);
    } else {
        // I wonder if there's a better way of "querying" data in json
        $data = $request->json('data.user.result.timeline.timeline.instructions.0.entries');

        if (count($data) > 2) {
            // Save the server response raw if we ever wanna do more with it retroactively
            Storage::put('server/' . time() . '.json', json_encode(json_decode($request->getContent()), JSON_PRETTY_PRINT));
            Log::debug('Stored server response');
        } else {
            return 'ok';
        }
    }

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
        'users' => [],
        'tweets' => [],
        'media' => [],
    ];

    if (!$request->boolean('force')) {
        $saved = [
            'users' => TwitterUser::query()->whereIn('id', $ids['users'])->get()->keyBy('id')->toArray(),
            'tweets' => \App\Models\Tweet::query()->whereIn('id', $ids['tweets'])->get()->keyBy('id')->toArray(),
            'media' => \App\Models\TweetMedia::query()->whereIn('id', $ids['media'])->get()->keyBy('id')->toArray(),
        ];
        Log::debug('Fetched cache');
    } else {
        Log::debug('Forced bypass cache');
    }

    foreach ($tweets as &$t) {
        $t->save($saved);
        Log::debug('Saved tweet ' . $t->id);
    }

    Log::debug('------~=[ ending request ]=~------');

    return 'ok';
});

Route::post('/scrape-bookmarks', function (Request $request) {
    Log::debug('-----~=[ starting request ]=~-----');

    $data = null;
    if ($request->has('file')) {
        $file = 'server/' . $request->string('file') . '.json';
        if (!Storage::exists($file)) return 'bad snapshot';
        $data = json_decode(Storage::get($file), true)['data']['bookmark_timeline_v2']['timeline']['instructions'][0]['entries'];
    } else {
        // I wonder if there's a better way of "querying" data in json
        $data = $request->json('data.bookmark_timeline_v2.timeline.instructions.0.entries');

        // Save the server response raw if we ever wanna do more with it retroactively
        Storage::put('server/' . time() . '.json', json_encode(json_decode($request->getContent()), JSON_PRETTY_PRINT));
        Log::debug('Stored server response');
    }
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
        'users' => [],
        'tweets' => [],
        'media' => [],
    ];

    if (!$request->boolean('force')) {
        $saved = [
            'users' => TwitterUser::query()->whereIn('id', $ids['users'])->get()->keyBy('id')->toArray(),
            'tweets' => \App\Models\Tweet::query()->whereIn('id', $ids['tweets'])->get()->keyBy('id')->toArray(),
            'media' => \App\Models\TweetMedia::query()->whereIn('id', $ids['media'])->get()->keyBy('id')->toArray(),
        ];
        Log::debug('Fetched cache');
    } else {
        Log::debug('Forced bypass cache');
    }

    foreach ($tweets as &$t) {
        $t->save($saved);
        Log::debug('Saved tweet ' . $t->id);
    }

    Log::debug('------~=[ ending request ]=~------');

    return 'ok';
});

Route::get('/dl_old', function (Request $request) {
    $stats = [
        'errors' => [
            'media'   => 0,
            'avatars' => 0,
            'banners' => 0,
            'total'   => null
        ],
        'downloads' => [
            'media'   => 0,
            'avatars' => 0,
            'banners' => 0,
            'total'   => null
        ]
    ];
    $errors = [];

    $p = fn (string $url, string $prefix = '') => $prefix . parse_url($url, PHP_URL_PATH);

    $media = TweetMedia::query()->where('downloaded', false)->limit($request->integer('n', 50))->get();
    $users = TwitterUser::query()->where('downloaded', false)->limit($request->integer('n', 50))->get();

    $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use (&$media, &$users, $p) {
        // Add each image url and, if applicable, media url to the download queue
        $media->map(function (TweetMedia $m) use ($p, &$pool) {
            // Don't add to download queue if we already have it
            if ($m->image_url)
                $pool->as($m->id . '_image')->get($m->image_url);

            if ($m->media_url)
                $pool->as($m->id . '_media')->get($m->media_url);
        });

        $users->map(function (TwitterUser $u) use (&$pool, $p) {
            if ($u->banner_url) {
                $pool->as('banner_' . $u->id)->get($u->banner_url);
            }

            if ($u->avatar_url) {
                $x96 = str_replace('normal', 'x96', $u->avatar_url);
                $x400 = str_replace('normal', '400x400', $u->avatar_url);

                $pool->as('avatar_' . $u->id . '_x96')->get($x96);
                $pool->as('avatar_' . $u->id . '_400x400')->get($x400);
            }
        });
    });

    //dd($responses, $media, $users);

    $media->map(function (TweetMedia $m) use (&$stats, &$responses, &$errors) {
        try {
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
            $stats['downloads']['media']++;
        } catch (Exception $e) {
            $errors[] = $e;
            $stats['errors']['media']++;
        }
    });

    $users->map(function (TwitterUser $u) use (&$errors, &$stats, &$responses) {
        $clean = true;

        try {
            if ($u->banner_url) {
                //if ($responses['banner_' . $u->id] instanceof \GuzzleHttp\Exception\ConnectException)

                $path = 'banners' . parse_url($u->banner_url, PHP_URL_PATH);
                Storage::put($path, $responses['banner_' . $u->id]->body(), 'public');
                $u->banner_url = $path;
            }
            $stats['downloads']['banners']++;
        } catch (Exception $e) {
            $errors[] = $e;
            $stats['errors']['banners']++;
            $clean = false;
        }

        try {
            if ($u->avatar_url) {
                $path = 'avatars' . parse_url(str_replace('normal', 'x96', $u->avatar_url), PHP_URL_PATH);
                Storage::put($path, $responses['avatar_' . $u->id . '_x96']->body(), 'public');
                $path = str_replace('x96', '400x400', $path);
                Storage::put($path, $responses['avatar_' . $u->id . '_400x400']->body(), 'public');

                $u->avatar_url = 'avatars' . parse_url($u->avatar_url, PHP_URL_PATH);
            }
            $stats['downloads']['avatars']++;
        } catch (Exception $e) {
            $errors[] = $e;
            $stats['errors']['avatars']++;
            $clean = false;
        }

        if ($clean) $u->downloaded = true;
        $u->save();
    });

    dd([
        'stats' => $stats,
        'errors' => $errors
    ], $responses, $media, $users);
});

Route::get('cluster-media', function () {
    $tweets = \App\Models\Tweet::query()->with(['media'])->get();

    $tweets->map(function (\App\Models\Tweet $t) {
        foreach ($t->media as $m) {
            $m->timestamps = false;
            $m->updated_at = $t->updated_at;
            $m->save();
        }
    });

    return 'ok';
});
