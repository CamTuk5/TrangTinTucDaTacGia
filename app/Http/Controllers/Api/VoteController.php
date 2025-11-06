<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->headers->set('Accept', 'application/json');

        if ($post->status !== 'published' || is_null($post->published_at)) {
            return response()->json(['message' => 'Post is not published'], 422);
        }

        $data = $request->validate([
            'choice' => ['required', Rule::in([1, 2])],
        ]);

        try {
            $vote = Vote::updateOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $request->user()->id,
                ],
                [
                    'choice' => (int) $data['choice'],
                ]
            );

            $status = $vote->wasRecentlyCreated ? 201 : 200;

            return response()->json([
                'message' => $vote->wasRecentlyCreated ? 'Created' : 'Updated',
                'data'    => $vote,
            ], $status);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot save vote',
            ], 422);
        }
    }

    public function summary(Post $post)
    {
        $counts = $post->votes()
            ->selectRaw('choice, COUNT(*) as total')
            ->groupBy('choice')
            ->pluck('total', 'choice');

        $choice1 = (int) ($counts[1] ?? 0);
        $choice2 = (int) ($counts[2] ?? 0);

        return response()->json([
            'message'  => 'OK',
            'post_id'  => $post->id,
            'choice_1' => $choice1,
            'choice_2' => $choice2,
            'total'    => $choice1 + $choice2,
        ]);
    }
}
