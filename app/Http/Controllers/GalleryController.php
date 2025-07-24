<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\TweetMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function all(Request $request): Response
    {
        $query = TweetMedia::query()->where('downloaded', '=', true);

        if (!true) {
            $query->where('type', '=', 'video');
        }

        return Inertia::render('Gallery', [
            'gallery' => $query->with(['tweet', 'tweet.author', 'galleries'])
                //->where('nsfw', '=', true)
                ->orderBy('updated_at', 'DESC')
                ->orderBy('tweet_id', 'DESC')
                ->paginate(60),
            'galleries' => \App\Models\Gallery::all(),
            'open' => \request()->integer('open', 0)
        ]);
    }

    public function index(): Response
    {
        return Inertia::render('Galleries/Index', [
            'galleries' => Gallery::all()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Galleries/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'max:64', 'string', 'min:1']
        ]);

        $gallery = new Gallery();
        $gallery->name = $request->string('name');
        $gallery->description = $request->string('description');
        $gallery->save();

        return redirect()->route('galleries.show', $gallery->id);
    }

    public function show(Gallery $gallery): Response
    {
        return Inertia::render('Gallery', [
            'gallery' => $gallery->media()
                ->with(['tweet', 'tweet.author'])
                ->orderBy('updated_at', 'DESC')
                ->paginate(48)
        ]);
    }

    public function edit(Gallery $gallery)
    {
        //
    }

    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return back();
    }
}
