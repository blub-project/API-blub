<?php

namespace App\Http\Controllers\Feed;

use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        return response([
            'feeds' => $feeds,
        ], 200);
    }

    public function store(PostRequest $request)
    {
        $request->validated();

        auth()->user()->feeds()->create([
            'content' => $request->content,
        ]);

        return response([
            'message' => 'success',
        ], 201);
    }

    public function likePost($feed_id)
    {
        $feed = Feed::whereId($feed_id)->first();

        // not found post
        if (!$feed) {
            return response([
                'message' => '404 Not found'
            ], 500);
        }

        // unlike post
        $unlike = Like::where('user_id', auth()->id())
            ->where('feed_id', $feed_id)
            ->delete();
        if ($unlike) {
            return response([
                'message' => 'Unlike'
            ], 200);
        }

        // like post
        $like = Like::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id,
        ]);
        if ($like) {
            return response([
                'message' => 'Like'
            ], 200);
        }
    }

    public function comment(Request $request, $feed_id)
    {
        $request->validate([
            'body' => 'required',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id,
            'body' => $request->body,
        ]);

        return response([
            'message' => 'success',
        ], 201);
    }

    public function getComments($feed_id)
    {
        $comments = Comment::whereFeedId($feed_id)
            ->with('feed')->with('user')
            ->latest()->get();

        return response([
            'comments' => $comments,
        ], 200);
    }
}
