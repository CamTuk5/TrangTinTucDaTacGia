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
    /**
     * POST /api/posts/{post}/votes  (auth:sanctum)
     * Body: { choice: 1|2 }
     * - Chỉ cho phép vote nếu post đã publish.
     * - Nếu đã vote trước đó thì cập nhật lựa chọn (200).
     * - Nếu lần đầu vote thì tạo mới (201).
     */
    public function store(Request $request, Post $post)
    {
        $request->headers->set('Accept', 'application/json');

        // chỉ cho phép vote bài đã publish
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
            // phòng khi thiếu unique index hoặc lỗi DB khác
            return response()->json([
                'message' => 'Cannot save vote',
            ], 422);
        }
    }

    /**
     * GET /api/posts/{post}/votes/summary (public)
     * Trả tổng hợp theo choice 1|2 và tổng.
     */
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
