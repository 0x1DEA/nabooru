<?php

use App\Libraries\Xtract\Proto;
use App\Models\TweetMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::inertia('/gddp', 'GDDP');

Route::get('/', function () {
    return Inertia::render('Home', [
        'stats' => [
            'tweets' => \App\Models\Tweet::query()->count(),
            'queue' => TweetMedia::query()->where('downloaded', '=', false)->count(),
            'media' => TweetMedia::query()->where('downloaded', '=', true)->count(),
            'accounts' => \App\Models\TwitterUser::query()->count()
        ],
        'galleries' => \App\Models\Gallery::all()
    ]);
});

Route::post('/media/{media:id}/mark-sensitive', function (Request $request, TweetMedia $media) {
    $media->nsfw = !$media->nsfw;
    $media->timestamps = false;
    $media->save();

    return back();
});

Route::get('/tweets', function () {
    return Inertia::render('Tweets', [
        'tweets' => \App\Models\Tweet::query()
            ->orderBy('updated_at', 'DESC')
            ->has('media', '=', 0)
            //->where('text', 'LIKE', '%pheromones%')
            ->with(['author', 'media', 'quote', 'quote.author', 'quote.media'])
            ->paginate(48)
    ]);
});

Route::get('/tweet/{tweet:id}', function (\App\Models\Tweet $tweet) {
    return Inertia::render('Tweet', [
        'tweet' => $tweet->load(['author', 'media', 'quote', 'quote.author', 'quote.media']),
        'quotes' => \App\Models\Tweet::query()->where('quote_id', $tweet->id)->with(['author', 'media'])->get()
    ]);
});

Route::get('/users', function () {
    return Inertia::render('Users', [
        'users' => \App\Models\TwitterUser::query()
            ->orderBy('updated_at', 'DESC')
            ->paginate(60)
    ]);
});

Route::get('/gallery', [\App\Http\Controllers\GalleryController::class, 'all'])->name('gallery');
Route::get('/galleries', [\App\Http\Controllers\GalleryController::class, 'index'])->name('galleries.index');
Route::get('/gallery/new', [\App\Http\Controllers\GalleryController::class, 'create'])->name('galleries.create');
Route::post('/gallery/new', [\App\Http\Controllers\GalleryController::class, 'store'])->name('galleries.store');
Route::get('/gallery/{gallery:id}', [\App\Http\Controllers\GalleryController::class, 'show'])->name('galleries.show');
Route::delete('/gallery/{gallery:id}', [\App\Http\Controllers\GalleryController::class, 'destroy']);

Route::post('/gallery-item/move', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'media_id' => ['required', 'exists:'. TweetMedia::class.',id']
    ]);

    \App\Models\Gallery::query()->latest()->first()->media()->attach($request->integer('media_id'));
});

Route::post('/gallery-item/new', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'media_id' => ['required', 'exists:'. TweetMedia::class.',id']
    ]);

    \App\Models\Gallery::query()->latest()->first()->media()->attach($request->integer('media_id'));
});

Route::delete('/gallery-item/delete', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'media_id' => ['required', 'exists:'. TweetMedia::class.',id']
    ]);

    \App\Models\Gallery::query()->latest()->first()->media()->detach($request->integer('media_id'));
});

$twurl = '(https?:\/\/.*(?:twitter|x)\.com\/)?(?<username>.*?)\/status\/(?<id>\d*)\??';

//Route::get('{tweet}', function (\Illuminate\Http\Request $request) use ($twurl) {
//    $m = [];
//    preg_match_all('/'.$twurl.'/mi', $request->path(), $m);
//
//    return Inertia::render('Tweet', [
//        'tweet' => Proto::tweet($m['id'][0])->save()->load(['author', 'media'])
//    ]);
//})->where('tweet', $twurl);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

