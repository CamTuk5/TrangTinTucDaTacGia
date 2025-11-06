<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function index(Request $request, Post $post)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $page = $post->comments()
            ->with('user:id,name')
            ->where('status', 'approved')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data'    => $page->items(),
            'meta'    => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ],
        ]);
    }

    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => ['required','string','min:1','max:2000'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => 'pending',
        ]);

        return response()->json([
            'message' => 'Created',
            'data'    => $comment->load('user:id,name'),
        ], 201);
    }

    public function moderate(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['approve','reject'])],
        ]);

        $newStatus = $data['action'] === 'approve' ? 'approved' : 'rejected';

        try {
            $comment->update(['status' => $newStatus]);

            return response()->json([
                'message' => 'Moderated',
                'data'    => $comment->fresh()->load('user:id,name'),
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot moderate comment',
            ], 422);
        }
    }
}
